/**
 * Flipcard Bio Block - Interacción Frontend
 * Maneja el efecto flip al hacer clic en la tarjeta o los botones
 */
( function () {
    'use strict';

    function initFlipcards() {
        var cards = document.querySelectorAll( '.flipcard-wrapper' );

        cards.forEach( function ( card ) {
            // Clic en toda la tarjeta
            card.addEventListener( 'click', function ( e ) {
                // Evitar flip si se hizo clic en un enlace de redes sociales
                if ( e.target.closest( '.flipcard-social-link' ) ) {
                    return;
                }
                card.classList.toggle( 'is-flipped' );
            } );

            // Botón "Ver perfil" en el frente
            var btnFront = card.querySelector( '.flipcard-btn:not(.flipcard-btn-back)' );
            if ( btnFront ) {
                btnFront.addEventListener( 'click', function ( e ) {
                    e.stopPropagation();
                    card.classList.add( 'is-flipped' );
                } );
            }

            // Botón "← Volver" en el reverso
            var btnBack = card.querySelector( '.flipcard-btn-back' );
            if ( btnBack ) {
                btnBack.addEventListener( 'click', function ( e ) {
                    e.stopPropagation();
                    card.classList.remove( 'is-flipped' );
                } );
            }

            // Soporte teclado: Enter o Espacio para flip
            card.setAttribute( 'tabindex', '0' );
            card.setAttribute( 'role', 'button' );
            card.setAttribute( 'aria-label', 'Tarjeta de perfil, presioná Enter para voltear' );

            card.addEventListener( 'keydown', function ( e ) {
                if ( e.key === 'Enter' || e.key === ' ' ) {
                    e.preventDefault();
                    card.classList.toggle( 'is-flipped' );
                }
            } );
        } );
    }

    // Inicializar cuando el DOM esté listo
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initFlipcards );
    } else {
        initFlipcards();
    }
} )();