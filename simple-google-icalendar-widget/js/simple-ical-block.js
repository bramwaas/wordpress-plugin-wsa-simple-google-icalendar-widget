/**
 * simple-ical-block.js
 *
 * Move styles to stylesheets - both edit and front-end.
 * and use attributes and editable fields
 * attributes as Inspectorcontrols (settings)
 * v1.6.0
 * 20220509 try to find a unique blockid from  clientId (only once) 
 * 20220511 integer excerptlength not initialised with '' and all parseInt(value) followed bij || 0 because result must comply type validation of REST endpoint and '' or NaN don't. (rest_invalid_type)
*           wp.components.ServerSideRender deprecated replaced by wp.serverSideRender
*           dependency to wp.editor although this seems not to be used (replaced by blockEditor) and gives a warning. 
*           preponed 'b' to blockid, because html id must not start with number.
 */
( function(wp, blocks, i18n, element, blockEditor, components ) {
	var el = element.createElement;
	var __ = i18n.__;

    var RichText = blockEditor.RichText;
	var useBlockProps = blockEditor.useBlockProps;
	var InspectorControls = blockEditor.InspectorControls;
	var InspectorAdvancedControls = blockEditor.InspectorAdvancedControls;
	var ServerSideRender = wp.serverSideRender;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
	blocks.registerBlockType( 'simplegoogleicalenderwidget/simple-ical-block', {
		edit: function( props ) {
 	      if ( ! props.attributes.blockid ) { props.setAttributes( { blockid: 'b' + props.clientId  } );} 
			return 	el(
               'div',
               useBlockProps ({key: 'simple_ical'}),
          el( ServerSideRender, {
                block: 'simplegoogleicalenderwidget/simple-ical-block',
                attributes: props.attributes
              }
			 ),

            /*
             * InspectorControls and InspectorAdvancedControls lets you add controls to the Block sidebar. In this case,
             */

            el( InspectorControls, 
				{key: 'setting'},
			el('div',
   			    {className: 'components-panel__body is-opened'},
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
                        onChange: function( value ) { props.setAttributes( { event_count: Math.max((parseInt(value) || 1),1) } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Number of days after today with events displayed:', 'simple_ical'),
                        value: props.attributes.event_period,
                        onChange: function( value ) { props.setAttributes( { event_period: Math.max((parseInt(value) || 1),1) } );},
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
            )
            ),
            el( InspectorAdvancedControls,
				{key: 'advancedsetting'},
                el(
                    TextControl,
                    {   label: __('Cache expiration time in minutes:', 'simple_ical'),
                        value: props.attributes.cache_time,
                        onChange: function( value ) { props.setAttributes( { cache_time: Math.max((parseInt(value) || 1),2) } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Excerpt length, max length of description:', 'simple_ical'),
                        value: props.attributes.excerptlength,
                        onChange: function( value ) {parsed =  parseInt(value);
                                                     if (isNaN(parse)) {parsed = ''};
                                                     props.setAttributes( { excerptlength: parsed } );},
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
            )			);
		},
	} );
}( window.wp,
   window.wp.blocks,
   window.wp.i18n,
   window.wp.element,
   window.wp.blockEditor,
   window.wp.components
 )
 );