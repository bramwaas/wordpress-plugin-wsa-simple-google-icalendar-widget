/**
 * simple-ical-block: Step 3
 *
 * Move styles to stylesheets - both edit and front-end.
 * and use attributes and editable fields
 *
 * Note the `className` property supplied to the `edit` callback.  To use the
 * `.wp-block-*` class for styling, plugin implementers must return an
 * appropriate element with this class.
 */
( function( blocks, i18n, element, blockEditor ) {
	var el = element.createElement;
	var __ = i18n.__;

    var RichText = blockEditor.RichText;
	var useBlockProps = blockEditor.useBlockProps;
    var AlignmentToolbar = blockEditor.AlignmentToolbar;
	var BlockControls = blockEditor.BlockControls;
	var InspectorControls = blockEditor.InspectorControls;
	var ColorPalette = blockEditor.ColorPalette;

	blocks.registerBlockType( 'simplegoogleicalenderwidget/simple-ical-block', {
        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'p',
                default: '',
            },
            alignment: {
                type: 'string',
                default: 'none',
            },
			bg_color: { 
				type: 'string',
				default: '#000000' },
        	text_color: { 
				type: 'string',
				default: '#ffffff' },
        },
		example: {
            attributes: {
                content: 'Hello World',
            },
        },
		edit: function( props ) {
            var content = props.attributes.content;
            var alignment = props.attributes.alignment;
            function onChangeContent( newContent ) {
                props.setAttributes( { content: newContent } );
            }
/*            var bg_color = props.attributes.bg_color;
            function onChangeBGColor ( hexColor ) {
                props.setAttributes( { bg_color: hexColor } );
            }
            var text_color = props.attributes.text_color;
            function onChangeTextColor ( hexColor ) {
                props.setAttributes( { text_color: hexColor } );
            }
*/
            function onChangeAlignment( newAlignment ) {
                props.setAttributes( { alignment: newAlignment === undefined ? 'none' : newAlignment } );
            }
 			
			return [
				el(
					BlockControls,
					{ key: 'controls' },
					el( AlignmentToolbar, {
						value: alignment,
						onChange: onChangeAlignment,
					} )
				),
				el(
                RichText,
                useBlockProps ( {
					key: 'richtext',
                    tagName: 'p',
					style: { textAlign: alignment },
					className: props.className,
                    onChange: onChangeContent,
                    value: content,
                } )
            ),
			];
		},
		save: function( props ) {
            return el(
                RichText.Content,
                useBlockProps.save( {
                    tagName: 'p',
				    className: 'gutenberg-examples-align-' + props.attributes.alignment,
                    value: props.attributes.content,
                } ) 
            );
		},
	} );
}( window.wp.blocks,
   window.wp.i18n,
   window.wp.element,
   window.wp.blockEditor )
 );