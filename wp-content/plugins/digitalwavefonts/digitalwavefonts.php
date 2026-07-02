<?php
/**
 * Plugin Name: DigitalWave Fonts
 * Plugin URI: https://digitalwave.local
 * Description: Buscador interactivo de tipografías usando la Google Fonts API. Permite previsualizar fuentes con texto de muestra y filtrar por categoría. Uso: [font_search]
 * Version: 1.0.0
 * Author: DigitalWave
 * Text Domain: digitalwavefonts
 */

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente
}

define('DWF_VERSION', '1.0.0');
define('DWF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DWF_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * =====================================================
 * 1. PÁGINA DE AJUSTES (para guardar la API Key)
 * =====================================================
 */
add_action('admin_menu', function () {
    add_options_page(
        'DigitalWave Fonts',
        'DigitalWave Fonts',
        'manage_options',
        'digitalwavefonts',
        'dwf_render_settings_page'
    );
});

add_action('admin_init', function () {
    register_setting('dwf_settings_group', 'dwf_google_fonts_api_key', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '',
    ]);
});

function dwf_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>DigitalWave Fonts — Ajustes</h1>
        <p>Pegá acá tu API Key de Google Fonts (Web Fonts Developer API). Es gratuita, se obtiene en
            <a href="https://console.cloud.google.com" target="_blank">Google Cloud Console</a>.</p>
        <form method="post" action="options.php">
            <?php settings_fields('dwf_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API Key</th>
                    <td>
                        <input type="text" name="dwf_google_fonts_api_key"
                               value="<?php echo esc_attr(get_option('dwf_google_fonts_api_key')); ?>"
                               style="width: 400px;" placeholder="AIzaSy..." />
                    </td>
                </tr>
            </table>
            <?php submit_button('Guardar API Key'); ?>
        </form>
        <p>Luego usá el shortcode <code>[font_search]</code> en cualquier página o entrada.</p>
    </div>
    <?php
}

/**
 * =====================================================
 * 2. ENCOLADO CONDICIONAL DE ASSETS
 * (solo carga CSS/JS si el shortcode está presente en la página)
 * =====================================================
 */
add_action('wp_enqueue_scripts', function () {
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'font_search')) {
        wp_enqueue_style('dwf-style', DWF_PLUGIN_URL . 'assets/style.css', [], DWF_VERSION);
        wp_enqueue_script('dwf-script', DWF_PLUGIN_URL . 'assets/script.js', [], DWF_VERSION, true);

        wp_localize_script('dwf-script', 'dwfAjax', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('dwf_search_nonce'),
        ]);
    }
});

/**
 * =====================================================
 * 3. SHORTCODE [font_search]
 * =====================================================
 */
add_shortcode('font_search', function () {
    ob_start();
    ?>
    <div class="dwf-container">
        <div class="dwf-search-box">
            <input type="text" id="dwf-search-input" placeholder="Buscá una fuente (ej. Roboto, Poppins, Lato)..." autocomplete="off" />
            <button id="dwf-search-btn" type="button">Buscar</button>
        </div>

        <div class="dwf-filters">
            <button class="dwf-filter-btn active" data-category="all">✨ Todas</button>
            <button class="dwf-filter-btn" data-category="serif">📜 Serif</button>
            <button class="dwf-filter-btn" data-category="sans-serif">🔤 Sans Serif</button>
            <button class="dwf-filter-btn" data-category="display">🎨 Display</button>
            <button class="dwf-filter-btn" data-category="handwriting">✍️ Handwriting</button>
            <button class="dwf-filter-btn" data-category="monospace">💻 Monospace</button>
        </div>

        <div class="dwf-preview-text-box">
            <label for="dwf-preview-text">Texto de muestra:</label>
            <input type="text" id="dwf-preview-text" value="DigitalWave — Diseño &amp; Desarrollo Web" />
        </div>

        <div id="dwf-loading" class="dwf-loading" style="display:none;">Cargando fuentes...</div>
        <div id="dwf-results" class="dwf-results"></div>
    </div>
    <?php
    return ob_get_clean();
});

/**
 * =====================================================
 * 4. HANDLER AJAX — consulta la Google Fonts API
 * =====================================================
 */
add_action('wp_ajax_dwf_search_fonts', 'dwf_search_fonts_handler');
add_action('wp_ajax_nopriv_dwf_search_fonts', 'dwf_search_fonts_handler');

function dwf_search_fonts_handler() {
    check_ajax_referer('dwf_search_nonce', 'nonce');

    $search   = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';

    $api_key = get_option('dwf_google_fonts_api_key');

    if (empty($api_key)) {
        wp_send_json_error(['message' => 'No hay API Key configurada. Andá a Ajustes → DigitalWave Fonts.']);
    }

    $cache_key = 'dwf_fonts_' . md5($category . $api_key);
    $fonts = get_transient($cache_key);

    if (false === $fonts) {
        $url = add_query_arg([
            'key'  => $api_key,
            'sort' => 'popularity',
        ], 'https://www.googleapis.com/webfonts/v1/webfonts');

        $response = wp_remote_get($url, ['timeout' => 15]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Error al conectar con Google Fonts API: ' . $response->get_error_message()]);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data['items'])) {
            wp_send_json_error(['message' => 'No se pudo obtener la lista de fuentes.']);
        }

        $fonts = $data['items'];
        set_transient($cache_key, $fonts, 6 * HOUR_IN_SECONDS);
    }

    // Filtrar por categoría
    if ($category !== 'all') {
        $fonts = array_values(array_filter($fonts, function ($font) use ($category) {
            return isset($font['category']) && $font['category'] === $category;
        }));
    }

    // Filtrar por término de búsqueda
    if (!empty($search)) {
        $search_lower = mb_strtolower($search);
        $fonts = array_values(array_filter($fonts, function ($font) use ($search_lower) {
            return strpos(mb_strtolower($font['family']), $search_lower) !== false;
        }));
    }

    // Limitar resultados para no saturar el frontend
    $fonts = array_slice($fonts, 0, 24);

    // Solo devolver los campos necesarios
    $results = array_map(function ($font) {
        return [
            'family'   => $font['family'],
            'category' => $font['category'],
            'variants' => array_slice($font['variants'], 0, 5),
            'files'    => isset($font['files']['regular']) ? $font['files']['regular'] : reset($font['files']),
        ];
    }, $fonts);

    wp_send_json_success(['fonts' => $results]);
}
