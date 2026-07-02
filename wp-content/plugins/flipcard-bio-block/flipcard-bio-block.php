<?php
/**
 * Plugin Name: Flipcard Bio Block
 * Plugin URI:  https://example.com
 * Description: Bloque de Gutenberg con tarjeta de perfil interactiva con efecto flip 3D.
 * Version:     1.0.0
 * Author:      Tu Nombre
 * License:     GPL-2.0-or-later
 * Text Domain: flipcard-bio-block
 */

// Seguridad: evitar acceso directo al archivo
defined( 'ABSPATH' ) || exit;

/**
 * Encola los scripts y estilos del bloque en el editor
 */
function flipcard_bio_editor_assets() {
    wp_enqueue_script(
        'flipcard-bio-editor',
        plugins_url( 'assets/editor.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'assets/editor.js' ),
        true
    );

    wp_enqueue_style(
        'flipcard-bio-editor-style',
        plugins_url( 'assets/editor.css', __FILE__ ),
        array( 'wp-edit-blocks' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'assets/editor.css' )
    );
}
add_action( 'enqueue_block_editor_assets', 'flipcard_bio_editor_assets' );

/**
 * Encola los estilos del frontend (solo cuando el bloque está en la página)
 */
function flipcard_bio_frontend_assets() {
    wp_enqueue_style(
        'flipcard-bio-style',
        plugins_url( 'assets/style.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'assets/style.css' )
    );

    wp_enqueue_script(
        'flipcard-bio-frontend',
        plugins_url( 'assets/frontend.js', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'assets/frontend.js' ),
        true
    );
}
add_action( 'enqueue_block_assets', 'flipcard_bio_frontend_assets' );