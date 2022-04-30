/**
 * simple-ical-block: Step 3
 *
 * Move styles to stylesheets - both edit and front-end.
 * and use attributes and editable fields
 * v1.6.0
 * 20220430
 */
( function( blocks, i18n, element, blockEditor, components ) {
	var el = element.createElement;
	var __ = i18n.__;

    var RichText = blockEditor.RichText;
	var useBlockProps = blockEditor.useBlockProps;
    var AlignmentToolbar = blockEditor.AlignmentToolbar;
	var BlockControls = blockEditor.BlockControls;
	var InspectorControls = blockEditor.InspectorControls;
	var ColorPalette = blockEditor.ColorPalette;
	var ServerSideRender = components.ServerSideRender;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;


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
        	foo: { 
				type: 'string',
				default: 'empty' },
        	toggle: { 
				type: 'boolean' },
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
            /*
             * The ServerSideRender element uses the REST API to automatically call
             * php_block_render() in your PHP code whenever it needs to get an updated
             * view of the block.
             */
/*            el( ServerSideRender, {
                block: 'nextgenthemes/arve-block',
                attributes: props.attributes,
            } ),
*/
            /*
             * InspectorControls lets you add controls to the Block sidebar. In this case,
             * we're adding a TextControl, which lets us edit the 'foo' attribute (which
             * we defined in the PHP). The onChange property is a little bit of magic to tell
             * the block editor to update the value of our 'foo' property, and to re-render
             * the block.
             */
            el( InspectorControls, {},
                el(
                    TextControl,
                    {
                        label: 'Foo',
                        value: props.attributes.foo,
                        onChange: function( value ) {
                            props.setAttributes( { foo: value } );
                        },
                    }
                ),
                el(
                    ToggleControl,
                    {
                        label: 'Toogle',
                        checked: props.attributes.toggle,
                        onChange: function( value ) {
                            props.setAttributes( { toggle: value } );
                        },
                    }
                )
            ),

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
				    className: 'simple-ical-align-' + props.attributes.alignment,
                    value: props.attributes.content,
                } ) 
            );
		},
	} );
}( window.wp.blocks,
   window.wp.i18n,
   window.wp.element,
   window.wp.blockEditor,
   window.wp.components
 )
 );