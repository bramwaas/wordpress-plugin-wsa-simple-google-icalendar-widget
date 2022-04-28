( function ( blocks, element, data, blockEditor, __ ) {
	console.log( "Plugin: Simple Google iCalendar Widget js 27-4 is loaded!" );
    var el = wp.element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var useSelect = data.useSelect;
    var useBlockProps = blockEditor.useBlockProps;
  
    registerBlockType( 'wsa/simpleicalblock', {
        apiVersion: 2,
        title: __('SimpleiCal', 'simple_ical'),
        icon: 'megaphone',
        category: 'widgets',
        edit: function () {
            var content;
            var blockProps = useBlockProps();
            var posts = useSelect( function ( select ) {
                return select( 'core' ).getEntityRecords( 'postType', 'post' );
            }, [] );
            if ( ! posts ) {
                content = 'Loading...';
            } else if ( posts.length === 0 ) {
                content = 'No posts';
            } else {
                var post = posts[ 0 ];
                content = el( 'a', { href: post.link }, post.title.rendered );
            }
 
            return el( 'div', blockProps, content );
        },
    } );
} )(
	window.wp.blocks,
    window.wp.element,
    window.wp.data,
    window.wp.blockEditor,
	window.wp.i18n
 );

