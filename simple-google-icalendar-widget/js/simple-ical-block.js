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
	var InspectorAdvancedControls = blockEditor.InspectorAdvancedControls;
	var ColorPalette = blockEditor.ColorPalette;
	var ServerSideRender = components.ServerSideRender;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;


	blocks.registerBlockType( 'simplegoogleicalenderwidget/simple-ical-block', {
        attributes: {
	
        title: {
            type: 'string',
            source: 'html',
            selector: '.block-title',
            default: 'Events'
        },
       calid: {
            type: 'string'
        },
       e_cnt: {
            type: 'integer',
            default: '10'
        },
       e_per: {
            type: 'integer',
            default: '92'
        },
       cache: {
            type: 'integer',
            default: '60'
        },
       df_lg: {
            type: 'string',
            default: 'l jS \of F'
        },
       df_tsum: {
            type: 'string',
            default: 'G:i '
        },
       df_tstrt: {
            type: 'string',
            default: 'G:i'
        },
       df_tend: {
            type: 'string',
            default: ' - G:i '
        },
       exc_ln: {
            type: 'integer'
        },
       sf_lg_cl: {
            type: 'string',
            default: ''
        },
       sf_lgi_cl: {
            type: 'string',
            default: ' py-0'
        },
       sf_lgia_cl: {
            type: 'string',
            default: ''
        },
       allowhtml: {
            type: 'boolean',
            default: false
        },
       cl_cache: {
            type: 'boolean',
            default: false
        },
	
	
	
            content: {
                type: 'array',
                source: 'children',
                selector: 'p',
                default: '',
            },
		},
		edit: function( props ) {
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
            el( InspectorControls, 
				{key: 'inspectorcontrols'},
                el(
                    TextControl,
                    {   label: __('Title:', 'simple_ical'),
                        value: props.attributes.title,
                        onChange: function( value ) { props.setAttributes( { title: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Calendar ID, or iCal URL:', 'simple_ical'),
                        value: props.attributes.calid,
                        onChange: function( value ) { props.setAttributes( { calid: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Number of events displayed:', 'simple_ical'),
                        value: props.attributes.e_cnt,
                        onChange: function( value ) { props.setAttributes( { e_cnt: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Number of days after today with events displayed:', 'simple_ical'),
                        value: props.attributes.e_per,
                        onChange: function( value ) { props.setAttributes( { e_per: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Date format first line:', 'simple_ical'),
                        value: props.attributes.df_lg,
                        onChange: function( value ) { props.setAttributes( { df_lg: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format time summary line:', 'simple_ical'),
                        value: props.attributes.df_tsum,
                        onChange: function( value ) { props.setAttributes( { df_tsum: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format start time:', 'simple_ical'),
                        value: props.attributes.df_tstrt,
                        onChange: function( value ) { props.setAttributes( { df_tstrt: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format end time:', 'simple_ical'),
                        value: props.attributes.df_tend,
                        onChange: function( value ) { props.setAttributes( { df_tend: value } );},
                    }
                )
            ),
            el( InspectorAdvancedControls,
				{key: 'inspectoradvancedcontrols'},
                el(
                    TextControl,
                    {   label: __('Cache expiration time in minutes:', 'simple_ical'),
                        value: props.attributes.cache,
                        onChange: function( value ) { props.setAttributes( { cache: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Excerpt length, max length of description:', 'simple_ical'),
                        value: props.attributes.exc_ln,
                        onChange: function( value ) { props.setAttributes( { exc_ln: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix group class:', 'simple_ical'),
                        value: props.attributes.sf_lg_cl,
                        onChange: function( value ) { props.setAttributes( { sf_lg_cl: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix event start class:', 'simple_ical'),
                        value: props.attributes.sf_lgi_cl,
                        onChange: function( value ) { props.setAttributes( { sf_lgi_cl: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix event details class:', 'simple_ical'),
                        value: props.attributes.sf_lgia_cl,
                        onChange: function( value ) { props.setAttributes( { sf_lgia_cl: value } );},
                    }
                ),
                el(
                    ToggleControl,
                    {   label: __('Allow safe html in description and summary.', 'simple_ical'),
                        checked: props.attributes.allowhtml,
                        onChange: function( value ) { props.setAttributes( { allowhtml: value } );},
                    }
                ),
                el(
                    ToggleControl,
                    {   label: __(' clear cache on save.', 'simple_ical'),
                        checked: props.attributes.cl_cache,
                        onChange: function( value ) { props.setAttributes( { cl_cache: value } );},
                    }
                )
            ),
			
			el(
               RichText,
               useBlockProps ( {
				key: 'richtext',
                tagName: 'p',
				className: props.className,
                onChange: function ( newContent ) {	props.setAttributes( { content: newContent } );},
                value: props.attributes.content,
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