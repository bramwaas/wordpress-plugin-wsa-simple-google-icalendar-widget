( function( blocks, i18n, element, blockEditor ) {
	var el = element.createElement;
	var __ = i18n.__;

	var useBlockProps = blockEditor.useBlockProps;

	blocks.registerBlockType( 'simplegoogleicalenderwidget/simple-ical-block', {
		example: {},
		edit: function( props ) {
			return el(
				'p',
				useBlockProps( { className: props.className } ),
				__( 'Hello World, step 2 (from the editor, in green).', 'simple_ical' )
			);
		},
		save: function() {
			return el(
				'p',
				useBlockProps.save(),
				__( 'Hello World, step 2 (from the frontend, in red).', 'simple_ical' )
			);
		},
	} );
}( window.wp.blocks, window.wp.i18n, window.wp.element, window.wp.blockEditor ) );