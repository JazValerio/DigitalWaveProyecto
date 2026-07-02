<?php
/**
 * The header for Astra Theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<?php astra_html_before(); ?>
<html <?php language_attributes(); ?>>
<head>
<?php astra_head_top(); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if ( apply_filters( 'astra_header_profile_gmpg_link', true ) ) {
	?>
	<link rel="profile" href="https://gmpg.org/xfn/11"> 
	<?php
}
?>
<?php wp_head(); ?>
<?php astra_head_bottom(); ?>
</head>

<body <?php astra_schema_body(); ?> <?php body_class(); ?>>
<?php astra_body_top(); ?>
<?php wp_body_open(); ?>

<a
	class="skip-link screen-reader-text"
	href="#content">
		<?php echo esc_html( astra_default_strings( 'string-header-skip-link', false ) ); ?>
</a>

<div id="page" class="site">

<header class="site-header-custom">
        <div class="container">
            <div class="site-logo">
            <a href="<?php echo home_url(); ?>">
                DigitalWave
            </a>
        </div>
            <button class="menu-toggle-btn" aria-label="Abrir menú">
                <span class="hamburger"></span>
            </button>
            <nav id="site-navigation" class="main-navigation">
                <?php 
                    wp_nav_menu(
                        [
                            'theme_location' => 'primary',
                            'menu_class'     => 'custom-menu',
                            'container'=>false,
                            'depth' => 3
                        ]
                    )
                ?>
            </nav>
        </div>
    </header>

	<div id="content" class="site-content">
		<div class="ast-container">
		<?php astra_content_top(); ?>
