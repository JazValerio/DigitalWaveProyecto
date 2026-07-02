( function ( blocks, element, blockEditor, components ) {
    var el = element.createElement;

    var InspectorControls = blockEditor.InspectorControls;
    var MediaUpload = blockEditor.MediaUpload;
    var PanelColorSettings = blockEditor.PanelColorSettings;
    var TextControl = components.TextControl;
    var TextareaControl = components.TextareaControl;
    var PanelBody = components.PanelBody;
    var Button = components.Button;
    var RangeControl = components.RangeControl;

    blocks.registerBlockType( 'flipcard-bio/card', {
        title: 'Flipcard Bio',
        icon: 'id-alt',
        category: 'widgets',
        attributes: {
            name:        { type: 'string', default: 'Tu Nombre' },
            role:        { type: 'string', default: 'Tu Cargo' },
            description: { type: 'string', default: 'Escribe aquí una descripción sobre esta persona.' },
            imageUrl:    { type: 'string', default: '' },
            imageAlt:    { type: 'string', default: '' },
            linkedin:    { type: 'string', default: '#' },
            github:      { type: 'string', default: '#' },
            instagram:   { type: 'string', default: '#' },
            frontBg:     { type: 'string', default: '#1a1a2e' },
            backBg:      { type: 'string', default: '#16213e' },
            accentColor: { type: 'string', default: '#2957ee' },
            cardHeight:  { type: 'number', default: 420 },
        },

        edit: function ( props ) {
            var attr = props.attributes;
            var setAttr = props.setAttributes;

            return el( 'div', { className: 'flipcard-editor-wrap' },

                el( InspectorControls, {},
                    el( PanelBody, { title: 'Contenido', initialOpen: true },
                        el( MediaUpload, {
                            onSelect: function ( media ) { setAttr( { imageUrl: media.url, imageAlt: media.alt } ); },
                            allowedTypes: [ 'image' ],
                            value: attr.imageUrl,
                            render: function ( obj ) {
                                return el( 'div', { style: { marginBottom: '12px' } },
                                    attr.imageUrl ? el( 'img', { src: attr.imageUrl, style: { width: '100%', borderRadius: '8px', marginBottom: '8px' } } ) : null,
                                    el( Button, { onClick: obj.open, variant: 'secondary', style: { width: '100%', justifyContent: 'center' } },
                                        attr.imageUrl ? 'Cambiar imagen' : 'Subir imagen de perfil'
                                    )
                                );
                            }
                        } ),
                        el( TextControl, { label: 'Nombre', value: attr.name, onChange: function ( val ) { setAttr( { name: val } ); } } ),
                        el( TextControl, { label: 'Cargo / Rol', value: attr.role, onChange: function ( val ) { setAttr( { role: val } ); } } ),
                        el( TextareaControl, { label: 'Descripción (reverso)', value: attr.description, onChange: function ( val ) { setAttr( { description: val } ); } } ),
                        el( TextControl, { label: 'URL LinkedIn', value: attr.linkedin, onChange: function ( val ) { setAttr( { linkedin: val } ); } } ),
                        el( TextControl, { label: 'URL GitHub', value: attr.github, onChange: function ( val ) { setAttr( { github: val } ); } } ),
                        el( TextControl, { label: 'URL Instagram', value: attr.instagram, onChange: function ( val ) { setAttr( { instagram: val } ); } } )
                    ),
                    el( PanelBody, { title: 'Dimensiones', initialOpen: false },
                        el( RangeControl, { label: 'Altura de la tarjeta (px)', value: attr.cardHeight, onChange: function ( val ) { setAttr( { cardHeight: val } ); }, min: 380, max: 600 } )
                    ),
                    el( PanelColorSettings, {
                        title: 'Colores', initialOpen: false,
                        colorSettings: [
                            { value: attr.frontBg, onChange: function ( val ) { setAttr( { frontBg: val || '#1a1a2e' } ); }, label: 'Fondo del frente' },
                            { value: attr.backBg, onChange: function ( val ) { setAttr( { backBg: val || '#16213e' } ); }, label: 'Fondo del reverso' },
                            { value: attr.accentColor, onChange: function ( val ) { setAttr( { accentColor: val || '#2957ee' } ); }, label: 'Color de acento' }
                        ]
                    } )
                ),

                el( 'div', { className: 'flipcard-editor-preview' },
                    el( 'div', { className: 'flipcard-face flipcard-front', style: { background: attr.frontBg, minHeight: attr.cardHeight + 'px' } },
                        attr.imageUrl
                            ? el( 'img', { src: attr.imageUrl, alt: attr.imageAlt, className: 'flipcard-avatar' } )
                            : el( 'div', { className: 'flipcard-avatar-placeholder' }, '👤' ),
                        el( 'h3', { className: 'flipcard-name', style: { color: attr.accentColor } }, attr.name ),
                        el( 'span', { className: 'flipcard-role' }, attr.role ),
                        el( 'span', { className: 'flipcard-hint' }, 'Clic para ver más →' )
                    ),
                    el( 'div', { className: 'flipcard-face flipcard-back flipcard-back-preview', style: { background: attr.backBg, minHeight: attr.cardHeight + 'px', marginTop: '1rem' } },
                        el( 'p', { className: 'flipcard-back-name', style: { color: attr.accentColor } }, attr.name ),
                        el( 'p', { className: 'flipcard-back-role' }, attr.role ),
                        el( 'div', { className: 'flipcard-divider', style: { background: attr.accentColor } } ),
                        el( 'p', { className: 'flipcard-desc' }, attr.description ),
                        el( 'div', { className: 'flipcard-social' },
                            el( 'a', { href: '#', className: 'flipcard-social-link', style: { color: attr.accentColor } }, el( 'span', { style: { color: attr.accentColor } }, 'in' ) ),
                            el( 'a', { href: '#', className: 'flipcard-social-link', style: { color: attr.accentColor } }, el( 'span', { style: { color: attr.accentColor } }, 'gh' ) ),
                            el( 'a', { href: '#', className: 'flipcard-social-link', style: { color: attr.accentColor } }, el( 'span', { style: { color: attr.accentColor } }, 'ig' ) )
                        )
                    )
                )
            );
        },

        save: function ( props ) {
            var attr = props.attributes;
            return el( 'div', { className: 'flipcard-wrapper', style: { height: attr.cardHeight + 'px' } },
                el( 'div', { className: 'flipcard-inner' },
                    el( 'div', { className: 'flipcard-face flipcard-front', style: { background: attr.frontBg } },
                        attr.imageUrl
                            ? el( 'img', { src: attr.imageUrl, alt: attr.imageAlt, className: 'flipcard-avatar' } )
                            : el( 'div', { className: 'flipcard-avatar-placeholder' }, '👤' ),
                        el( 'h3', { className: 'flipcard-name', style: { color: attr.accentColor } }, attr.name ),
                        el( 'span', { className: 'flipcard-role' }, attr.role ),
                        el( 'button', { className: 'flipcard-btn', style: { '--accent': attr.accentColor } }, 'Ver perfil' )
                    ),
                    el( 'div', { className: 'flipcard-face flipcard-back', style: { background: attr.backBg } },
                        el( 'p', { className: 'flipcard-back-name', style: { color: attr.accentColor } }, attr.name ),
                        el( 'p', { className: 'flipcard-back-role' }, attr.role ),
                        el( 'div', { className: 'flipcard-divider', style: { background: attr.accentColor } } ),
                        el( 'p', { className: 'flipcard-desc' }, attr.description ),
                        el( 'div', { className: 'flipcard-social' },
                            el( 'a', { href: attr.linkedin, className: 'flipcard-social-link', style: { color: attr.accentColor }, target: '_blank', rel: 'noopener noreferrer' }, el( 'span', { style: { color: attr.accentColor } }, 'in' ) ),
                            el( 'a', { href: attr.github, className: 'flipcard-social-link', style: { color: attr.accentColor }, target: '_blank', rel: 'noopener noreferrer' }, el( 'span', { style: { color: attr.accentColor } }, 'gh' ) ),
                            el( 'a', { href: attr.instagram, className: 'flipcard-social-link', style: { color: attr.accentColor }, target: '_blank', rel: 'noopener noreferrer' }, el( 'span', { style: { color: attr.accentColor } }, 'ig' ) )
                        ),
                        el( 'button', { className: 'flipcard-btn flipcard-btn-back', style: { '--accent': attr.accentColor } }, '← Volver' )
                    )
                )
            );
        }
    } );

} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components );