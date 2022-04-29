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

	blocks.registerBlockType( 'simplegoogleicalenderwidget/simple-ical-block', {
        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'p',
            },
        },
		example: {
            attributes: {
                content: 'Hello World',
            },
        },
		edit: function( props ) {
            var blockProps = useBlockProps();
            var content = props.attributes.content;
            function onChangeContent( newContent ) {
                props.setAttributes( { content: newContent } );
            }
 			
			return el(
                RichText,
                Object.assign( blockProps, {
                    tagName: 'p',
                    onChange: onChangeContent,
                    value: content,
                } )
            );
		},
		save: function( props ) {
            var blockProps = useBlockProps.save();
            return el(
                RichText.Content,
                Object.assign( blockProps, {
                    tagName: 'p',
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