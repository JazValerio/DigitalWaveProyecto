<?php
// Traer los estilos del padre
function enqueue_styles(){
    wp_enqueue_style('parent_styles', get_template_directory_uri().'/assets/css/style.css');
    wp_enqueue_style('astra_child_styles', get_stylesheet_directory_uri().'/assets/css/styles.css',[],'1.0.0');
}
add_action('wp_enqueue_scripts','enqueue_styles');

function enqueue_scripts(){    
    wp_enqueue_script('menu-toggle', get_stylesheet_directory_uri().'/assets/js/menu-toggle.js',[],'1.0.0');
}
add_action('wp_enqueue_scripts','enqueue_scripts');

// ============================================
// 1. Registrar el CPT "Testimonio"
// ============================================
function digitalwave_registrar_cpt_testimonio() {
    register_post_type('testimonio', [
        'labels' => [
            'name' => 'Testimonios',
            'singular_name' => 'Testimonio',
            'add_new_item' => 'Añadir nuevo testimonio',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
}
add_action('init', 'digitalwave_registrar_cpt_testimonio');

// ============================================
// 2. Meta box: Empresa y Calificación
// ============================================
function digitalwave_meta_box_testimonio() {
    add_meta_box(
        'dw_testimonio_datos',
        'Datos del cliente',
        'digitalwave_render_meta_box',
        'testimonio',
        'side'
    );
}
add_action('add_meta_boxes', 'digitalwave_meta_box_testimonio');

function digitalwave_render_meta_box($post) {
    $empresa = get_post_meta($post->ID, '_dw_empresa', true);
    $calificacion = get_post_meta($post->ID, '_dw_calificacion', true);
    ?>
    <p>
        <label>Empresa</label>
        <input type="text" name="dw_empresa" value="<?php echo esc_attr($empresa); ?>" style="width:100%;" />
    </p>
    <p>
        <label>Calificación (1 a 5)</label>
        <input type="number" name="dw_calificacion" min="1" max="5" value="<?php echo esc_attr($calificacion); ?>" style="width:100%;" />
    </p>
    <?php
}

function digitalwave_guardar_meta_box($post_id) {
    if (isset($_POST['dw_empresa'])) {
        update_post_meta($post_id, '_dw_empresa', sanitize_text_field($_POST['dw_empresa']));
    }
    if (isset($_POST['dw_calificacion'])) {
        update_post_meta($post_id, '_dw_calificacion', intval($_POST['dw_calificacion']));
    }
}
add_action('save_post', 'digitalwave_guardar_meta_box');

// ============================================
// 3. Shortcode [testimonios] — para usar en Elementor
// ============================================
function digitalwave_shortcode_testimonios() {
    ob_start();

    $testimonios = new WP_Query([
        'post_type' => 'testimonio',
        'posts_per_page' => -1,
    ]);

    if ($testimonios->have_posts()) :
        ?>
        <div class="dw-testimonios-grid">
            <?php
            while ($testimonios->have_posts()) : $testimonios->the_post();
                ?>
                <div class="dw-testimonio-card">
                    <p class="dw-testimonio-texto"><?php the_content(); ?></p>
                    <div class="dw-testimonio-autor">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('thumbnail', ['class' => 'dw-testimonio-foto']); ?>
                        <?php endif; ?>
                        <span class="dw-testimonio-nombre"><?php the_title(); ?></span>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php
    else :
        echo '<p>Todavía no hay testimonios cargados.</p>';
    endif;

    return ob_get_clean();
}
add_shortcode('testimonios', 'digitalwave_shortcode_testimonios');

// ============================================
// 4. Cargar el CSS de los testimonios
// ============================================
function digitalwave_cargar_estilos_testimonios() {
    wp_enqueue_style(
        'dw-testimonios-css',
        get_stylesheet_directory_uri() . '/assets/css/testimonios.css',
        [],
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'digitalwave_cargar_estilos_testimonios');