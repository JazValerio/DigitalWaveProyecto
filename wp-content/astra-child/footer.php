<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php astra_content_bottom(); ?>
	</div> <!-- ast-container -->
	</div><!-- #content -->
<?php
	astra_content_after();
?>
	<footer class="custom-footer">

    <div class="footer-container">

        <!-- Información -->
        <div class="footer-brand">
            <h3>DigitalWave</h3>
            <p>
                Creamos experiencias digitales modernas y responsivas
                para empresas y emprendedores.
            </p>
        </div>

        <!-- Links legales -->
        <div class="footer-links">
            <h4>Enlaces Legales</h4>

            <ul>
                <li><a href="#">Política de Privacidad</a></li>
                <li><a href="#">Términos y Condiciones</a></li>
                <li><a href="#">Cookies</a></li>
            </ul>
        </div>

        <!-- Redes sociales -->
        <div class="footer-social">
            <h4>Redes Sociales</h4>

            <div class="social-icons">
                <a href="#">Web</a>
                <a href="#">Portfolio</a>
                <a href="#">Instagram</a>
                <a href="#">Facebook</a>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        © 2026 DigitalWave - Todos los derechos reservados.
    </div>

</footer>
<?php
	astra_body_bottom();
	wp_footer();
?>
	</body>
</html>
