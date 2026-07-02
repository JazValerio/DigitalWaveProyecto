<?php
/**
 * Plugin Name: Paleta de Colores Generador plugin
 * Description: Muestra una paleta de colores aleatoria usando la api de The Color API. 
 * Author: Jahaziel :)
 */

defined('ABSPATH') || exit;

// Cargar CSS y JS
function pc_cargar_assets() {
    global $post;
    if ( is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'paleta_colores') ) {
        wp_enqueue_style('pc-estilo', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '1.0');
        wp_enqueue_script('pc-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array(), '1.0', true);

        // se la pasa el link de ajax a al JavaScript
        wp_localize_script('pc-script', 'PC_Config', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('pc_nonce'),
        ));
    }
}

add_action('wp_enqueue_scripts', 'pc_cargar_assets');

//  aquí se  consulta la Api y devuelve los colores
function pc_obtener_colores() {
    check_ajax_referer('pc_nonce', 'nonce');

    
    $hex = sprintf('%06X', mt_rand(0, 0xFFFFFF));

    
    $respuesta = wp_remote_get('https://www.thecolorapi.com/scheme?hex=' . $hex . '&mode=analogic&count=5');

    
    if ( is_wp_error($respuesta) ) {
        wp_send_json_error('No se pudo conectar con la API.');
        return;
    }

    $cuerpo = wp_remote_retrieve_body($respuesta);
    $datos  = json_decode($cuerpo, true);

    // pos i no devielve nada el api
    if ( empty($datos['colors']) ) {
        wp_send_json_error('La API no devolvió colores.');
        return;
    }

    // se prertaran para enciar al front
    $colores = array();
    foreach ( $datos['colors'] as $color ) {
        $colores[] = array(
            'hex'    => $color['hex']['value'],
            'nombre' => $color['name']['value'],
        );
    }

    wp_send_json_success($colores);
}

add_action('wp_ajax_pc_obtener_colores',        'pc_obtener_colores');

add_action('wp_ajax_nopriv_pc_obtener_colores', 'pc_obtener_colores');

// aui se crea el shortcode
function pc_shortcode() {
    ob_start();
    ?>
    <div class="pc-contenedor">
        <h3 class="pc-titulo">Generador de Paletas de Color</h3>
        <p class="pc-descripcion">Generá una paleta de colores aleatoria para tu próximo proyecto web.</p>

        <button class="pc-boton" id="pc-generar">Generar Paleta</button>

        <div class="pc-colores" id="pc-colores">
            <p class="pc-mensaje">Hacé clic en el botón para generar una paleta.</p>
        </div>

        <p class="pc-copiar-aviso" id="pc-aviso" style="display:none;">Color copiado al portapapeles!</p>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('paleta_colores', 'pc_shortcode');
