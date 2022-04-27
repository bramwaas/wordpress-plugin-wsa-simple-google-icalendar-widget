( function ( wp ) {
	console.log( "Plugin: Simple Google iCalendar Widget js 27-4 is loaded!" );
    var registerPlugin = wp.plugins.registerPlugin;
    var PluginSidebar = wp.editPost.PluginSidebar;
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var useSelect = wp.data.useSelect;
    var useDispatch = wp.data.useDispatch;


    var MetaBlockField = function ( props ) {
        var metaFieldValue = useSelect( function ( select ) {
            return select( 'core/editor' ).getEditedPostAttribute(
                'meta'
            )[ 'sidebar_plugin_meta_block_field' ];
        }, [] );
 
        var editPost = useDispatch( 'core/editor' ).editPost;
 
        return el( TextControl, {
            label: 'Meta Block Field ...',
            value: metaFieldValue,
            onChange: function ( content ) {
                editPost( {
                    meta: { sidebar_plugin_meta_block_field: content },
                } );
            },
        } );
    };

 
    registerPlugin( 'simple-google-icalendar-widget', {
        render: function () {
            return el(
                PluginSidebar,
                {
                    name: 'simple-google-icalendar-widget',
                    icon: 'admin-post',
                    title: 'Simple Google Calendar Outlook Events Widget',
                },
                el(
                    'div',
                    { className: 'plugin-sidebar-content' },
                    el( MetaBlockField  )
                )
            );
        },
    } );
} )( window.wp );

