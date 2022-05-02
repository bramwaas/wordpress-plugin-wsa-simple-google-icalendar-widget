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
            default: 'Events'
        },
       calendar_id: {
            type: 'string'
        },
       event_count: {
            type: 'integer',
            default: '10'
        },
       event_period: {
            type: 'integer',
            default: '92'
        },
       cache_time: {
            type: 'integer',
            default: '60'
        },
       dateformat_lg: {
            type: 'string',
            default: 'l jS \of F'
        },
       dateformat_tsum: {
            type: 'string',
            default: 'G:i '
        },
       dateformat_tstart: {
            type: 'string',
            default: 'G:i'
        },
       dateformat_tend: {
            type: 'string',
            default: ' - G:i '
        },
       excerptlength: {
            type: 'integer'
        },
       suffix_lg_class: {
            type: 'string',
            default: ''
        },
       suffix_lgi_class: {
            type: 'string',
            default: ' py-0'
        },
       suffix_lgia_class: {
            type: 'string',
            default: ''
        },
       allowhtml: {
            type: 'boolean',
            default: false
        },
       clear_cache_now: {
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
				useBlockProps ({key: 'inspectorcontrols'}),
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
                        value: props.attributes.calendar_id,
                        onChange: function( value ) { props.setAttributes( { calendar_id: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Number of events displayed:', 'simple_ical'),
                        value: props.attributes.event_count,
                        onChange: function( value ) { props.setAttributes( { event_count: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Number of days after today with events displayed:', 'simple_ical'),
                        value: props.attributes.event_period,
                        onChange: function( value ) { props.setAttributes( { event_period: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Date format first line:', 'simple_ical'),
                        value: props.attributes.dateformat_lg,
                        onChange: function( value ) { props.setAttributes( { dateformat_lg: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format time summary line:', 'simple_ical'),
                        value: props.attributes.dateformat_tsum,
                        onChange: function( value ) { props.setAttributes( { dateformat_tsum: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format start time:', 'simple_ical'),
                        value: props.attributes.dateformat_tstart,
                        onChange: function( value ) { props.setAttributes( { dateformat_tstart: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format end time:', 'simple_ical'),
                        value: props.attributes.dateformat_tend,
                        onChange: function( value ) { props.setAttributes( { dateformat_tend: value } );},
                    }
                )
            ),
            el( InspectorAdvancedControls,
				useBlockProps ({key: 'inspectoradvancedcontrols'}),
                el(
                    TextControl,
                    {   label: __('Cache expiration time in minutes:', 'simple_ical'),
                        value: props.attributes.cache_time,
                        onChange: function( value ) { props.setAttributes( { cache_time: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Excerpt length, max length of description:', 'simple_ical'),
                        value: props.attributes.excerptlength,
                        onChange: function( value ) { props.setAttributes( { excerptlength: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix group class:', 'simple_ical'),
                        value: props.attributes.suffix_lg_class,
                        onChange: function( value ) { props.setAttributes( { suffix_lg_class: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix event start class:', 'simple_ical'),
                        value: props.attributes.suffix_lgi_class,
                        onChange: function( value ) { props.setAttributes( { suffix_lgi_class: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix event details class:', 'simple_ical'),
                        value: props.attributes.suffix_lgia_class,
                        onChange: function( value ) { props.setAttributes( { suffix_lgia_class: value } );},
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
                        checked: props.attributes.clear_cache_now,
                        onChange: function( value ) { props.setAttributes( { clear_cache_now: value } );},
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