/**
 * simple-ical-block.js
 *
 * Move styles to stylesheets - both edit and front-end.
 * and use attributes and editable fields
 * attributes as Inspectorcontrols (settings)
 * v2.2.0
 * 20230625 added quotes to the options of the Layout SelectControl,
 *  add parseInt to all integers in transform, added conversion dateformat_lgend and _tsend and anchorid = blockid
 * 20230420 added parseInt on line 147(now 148) to keep layout in block-editor
 * 20230418 Added after_events and no_events HTML output after available events, or istead of unavailable events.
 * 20230401 use select 'layout' in stead of 'start with summary' to create more lay-out options.
 * 20220622  added enddate/times for startdate and starttime added Id as anchor.
 * 20220517  try to find a unique blockid from  clientId (only once) 
 *   excerptlength initialised with '' so cannot be integer, all parseInt(value) followed bij || 0,1,or 2  because result must comply type validation of REST endpoint and '' or NaN don't. (rest_invalid_type)
 *   preponed 'b' to blockid, because html id must not start with number.
 *   wp.components.ServerSideRender deprecated replaced by serverSideRender and dependency wp-server-side-render; clear_cache_now false after 1 second, to prevent excessive calling of calendar
 * 20240106 Added help to (some) settings. Changed the text domain to simple-google-icalendar-widget to make translations work by following the WP standard
 *   wrong part of blockname "simplegoogleicalenderwidget" cannot be changed because it is safed in the page and changing and changing invalidates the block.
 */
( function(blocks, i18n, element, blockEditor, components, serverSideRender ) {
	var el = element.createElement;
	var __ = i18n.__;
	var useBlockProps = blockEditor.useBlockProps;
	var InspectorControls = blockEditor.InspectorControls;
	var InspectorAdvancedControls = blockEditor.InspectorAdvancedControls;
	var ServerSideRender = serverSideRender;
	var iconEl =el('svg', { width: 24, height: 24,    viewBox: "0 0 128 128" },
    el('rect', { fill: "#ecf6fe",  stroke: "#ecf6fe", width: "128", height: "128", x: "0", y: "0" }),
    el('path', {fill: "#ffffff", stroke: "#3f48cc", d: "M 12,28 h 99 v 86 H 12 Z",  }),
    el('path', {fill: "#3f48cc", stroke: "#3f48cc", d: "M 44.466259,70.168415 q 2.864017,0 4.918637,-1.681054 2.085752,-1.681053 2.085752,-4.825245 0,-2.397057 -1.649923,-4.109241 -1.649923,-1.743315 -4.451678,-1.743315 -1.898968,0 -3.144192,0.529221 -1.214094,0.52922 -1.930098,1.400877 -0.716005,0.871658 -1.369748,2.241405 -0.622612,1.369747 -1.151832,2.583841 -0.311307,0.653743 -1.120703,1.02731 -0.809396,0.373567 -1.867836,0.373567 -1.245225,0 -2.303666,-0.996179 -1.02731,-1.027311 -1.02731,-2.708364 0,-1.618792 0.965049,-3.393237 0.996179,-1.805576 2.864016,-3.424368 1.898968,-1.618792 4.700723,-2.583841 2.801756,-0.996179 6.257254,-0.996179 3.01967,0 5.510119,0.840526 2.490449,0.809396 4.327156,2.365927 1.836706,1.556531 2.770624,3.611151 0.933919,2.054621 0.933919,4.420548 0,3.113061 -1.369747,5.354466 -1.338617,2.210273 -3.860197,4.327155 2.428188,1.307486 4.078111,2.988539 1.681053,1.681054 2.52158,3.735674 0.840527,2.02349 0.840527,4.389417 0,2.832886 -1.151833,5.478988 -1.120702,2.646103 -3.330976,4.731854 -2.210274,2.054621 -5.261074,3.237584 -3.01967,1.151833 -6.693082,1.151833 -3.735674,0 -6.693083,-1.338617 -2.957408,-1.338616 -4.887506,-3.330976 -1.898968,-2.02349 -2.895148,-4.171502 -0.965049,-2.148013 -0.965049,-3.54889 0,-1.805576 1.151833,-2.895147 1.182963,-1.120703 2.926278,-1.120703 0.871657,0 1.681053,0.529221 0.809396,0.49809 1.058441,1.214094 1.618792,4.327155 3.455498,6.444037 1.867837,2.085752 5.229944,2.085752 1.930098,0 3.704543,-0.933919 1.805576,-0.965049 2.957409,-2.832886 1.182963,-1.867837 1.182963,-4.327156 0,-3.642282 -1.992359,-5.696902 -1.99236,-2.085751 -5.54125,-2.085751 -0.622612,0 -1.930098,0.124522 -1.307486,0.124522 -1.681053,0.124522 -1.712184,0 -2.646103,-0.840526 -0.933918,-0.871657 -0.933918,-2.397058 0,-1.494269 1.120702,-2.397057 1.120702,-0.933918 3.330976,-0.933918 z"  }),
    el('path', {fill: "#3f48cc", stroke: "#3f48cc", d: "M 82.881439,93.454115 v -28.32886 q -7.907176,6.07047 -10.64667,6.07047 -1.307486,0 -2.334797,-1.02731 -0.996179,-1.058441 -0.996179,-2.428188 0,-1.587662 0.996179,-2.334797 0.99618,-0.747134 3.51776,-1.930098 3.766804,-1.774445 6.008209,-3.735674 2.272535,-1.961228 4.015849,-4.389416 1.743315,-2.428188 2.272535,-2.98854 0.529221,-0.560351 1.99236,-0.560351 1.649922,0 2.646102,1.276356 0.99618,1.276355 0.99618,3.517759 v 35.644555 q 0,6.257254 -4.264895,6.257254 -1.898967,0 -3.0508,-1.276355 -1.151833,-1.276356 -1.151833,-3.766805 z"  }),
    el('circle', { fill: "#ecf6fe",  stroke: "#000000", cx: "38", cy: "21", r: "3"}),
    el('circle', { fill: "#ecf6fe",  stroke: "#000000", cx: "38", cy: "35", r: "3"}),
    el('circle', { fill: "#ecf6fe",  stroke: "#000000", cx: "86", cy: "21", r: "3"}),
    el('circle', { fill: "#ecf6fe",  stroke: "#000000", cx: "86", cy: "35", r: "3"}),
    el('path', {fill: "#000000", stroke: "#000000", d: "M38,21 V35 M86,21 V35", })
    );
    var Button = components.Button;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var SelectControl = components.SelectControl;
    var useEffect = element.useEffect;
	blocks.registerBlockType( 'simplegoogleicalenderwidget/simple-ical-block', {
        icon: iconEl,

        transforms: {
    from: [
        {
            type: 'block',
            blocks: [ 'core/legacy-widget' ],
            isMatch: function ( { idBase, instance } ) {
                if ( ! (instance && instance.raw) ) {
                    // Can't transform if raw instance is not shown in REST API.
                    return false;
                }
                return idBase === 'simple_ical_widget';
            },
            transform: function ( { instance } ) {
				if (!(parseInt(instance.raw.layout) > 0))  {instance.raw.layout = 3} 
                return blocks.createBlock( 'simplegoogleicalenderwidget/simple-ical-block', {
                    title: instance.raw.title,
                    calendar_id: instance.raw.calendar_id,
                    event_count: parseInt(instance.raw.event_count),
                    event_period: parseInt(instance.raw.event_period),
                    cache_time: parseInt(instance.raw.cache_time),
                    startwsum : false,
                    layout: parseInt(instance.raw.layout),
                    dateformat_lg: instance.raw.dateformat_lg,
                    dateformat_lgend : instance.raw.dateformat_lgend,
                    dateformat_tsum: instance.raw.dateformat_tsum,
			  		dateformat_tsend : instance.raw.dateformat_tsend,
                    dateformat_tstart: instance.raw.dateformat_tstart,
                    dateformat_tend: instance.raw.dateformat_tend,
                    excerptlength: instance.raw.excerptlength,
                    suffix_lg_class: instance.raw.suffix_lg_class,
                    suffix_lgi_class: instance.raw.suffix_lgi_class,
                    suffix_lgia_class: instance.raw.suffix_lgia_class,
                    allowhtml: instance.raw.allowhtml,
                    after_events: instance.raw.after_events,
                    no_events: instance.raw.no_events,
			  		tag_sum: instance.raw.tag_sum,
                    anchorId: instance.raw.blockid,
			  		className: 'Simple_iCal_Widget',
                } );
            },
        },
    ]
},

		edit: function( props ) {
			useEffect(function() {
 	           if ( ! props.attributes.blockid ) { props.setAttributes( { blockid: 'b' + props.clientId  } );}
            }, []); 
			useEffect(function() {
				if ( props.attributes.clear_cache_now ) {			   
					var x = setTimeout(stopCC, 1000);
               		function stopCC () { props.setAttributes( { clear_cache_now: false  } );}	
 	           }
            }, [props.attributes.clear_cache_now]); 
			useEffect(function() {
				if ( props.attributes.startwsum ) {
					props.setAttributes( { layout: 2 } );			   
					props.setAttributes( { startwsum: false  } );			   
 	           }
            }, [props.attributes.layout]); 
			return 	el(
               'div',
               useBlockProps ({key: 'simple_ical'}),
          el( ServerSideRender, {
                block: 'simplegoogleicalenderwidget/simple-ical-block',
                attributes: props.attributes,
				httpMethod: 'POST'
              }
			 ),
            el( InspectorControls, 
				{key: 'setting'},
			el('div',
   			    {className: 'components-panel__body is-opened'},
                el(
                    TextControl,
                    {   label: __('Title:', 'simple-google-icalendar-widget'),
                        value: props.attributes.title,
                        help: __('Title of this instance of the widget', 'simple-google-icalendar-widget'),
                        onChange: function( value ) { props.setAttributes( { title: value } );},
 
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Calendar ID, or iCal URL:', 'simple-google-icalendar-widget'),
                        value: props.attributes.calendar_id,
                        help: __("The Google calendar ID, or the URL of te iCal file to display, or #example, or comma separated list of ID's. Optional you can add a html-class separated by a semicolon to some or all ID's to distinguish calendars in the lay-out.", 'simple-google-icalendar-widget'),  
                        onChange: function( value ) { props.setAttributes( { calendar_id: value } );},


                    }
                ),
                el(
                    TextControl,
                    {   label: __('Number of events displayed:', 'simple-google-icalendar-widget'),
                        value: props.attributes.event_count,
                        onChange: function( value ) { props.setAttributes( { event_count: Math.max((parseInt(value) || 1),1) } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Number of days after today with events displayed:', 'simple-google-icalendar-widget'),
                        value: props.attributes.event_period,
                        onChange: function( value ) { props.setAttributes( { event_period: Math.max((parseInt(value) || 1),1) } );},
                    }
                ),
                el(
                    SelectControl,
                    {   label: __('Lay-out:', 'simple-google-icalendar-widget'),
                        value: props.attributes.layout,
                        onChange: function( value ) { props.setAttributes( { layout: parseInt(value) } );},
					    options:  [
							        { value: 1, label: __('Startdate higher level', 'simple-google-icalendar-widget') },
							        { value: 2, label: __('Start with summary', 'simple-google-icalendar-widget') },
								    { value: 3, label: __('Old style', 'simple-google-icalendar-widget') }
    							] 
                    }
                ),
				el(
					TextControl,
					{
						label: __('Date format first line:', 'simple-google-icalendar-widget'),
						help: el(
							'a',
							{
								href: 'admin.php?page=simple_ical_info#dateformat-lg',
								target: '_blank',
							},
							__('More info', 'simple-google-icalendar-widget')
						),
						value: props.attributes.dateformat_lg,
						onChange: function(value) { props.setAttributes({ dateformat_lg: value }); },
					}
				),
                el(
                    TextControl,
                    {   label: __('Enddate format first line:', 'simple-google-icalendar-widget'),
                        value: props.attributes.dateformat_lgend,
                        onChange: function( value ) { props.setAttributes( { dateformat_lgend: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format time summary line:', 'simple-google-icalendar-widget'),
                        value: props.attributes.dateformat_tsum,
                        onChange: function( value ) { props.setAttributes( { dateformat_tsum: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format end time summary line:', 'simple-google-icalendar-widget'),
                        value: props.attributes.dateformat_tsend,
                        onChange: function( value ) { props.setAttributes( { dateformat_tsend: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format start time:', 'simple-google-icalendar-widget'),
                        value: props.attributes.dateformat_tstart,
                        onChange: function( value ) { props.setAttributes( { dateformat_tstart: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Time format end time:', 'simple-google-icalendar-widget'),
                        value: props.attributes.dateformat_tend,
                        onChange: function( value ) { props.setAttributes( { dateformat_tend: value } );},
                    }
                ),
                el(
                   'a',
                    {  href: 'admin.php?page=simple_ical_info',
					   target: '_blank',
                    },
					__('Need help?', 'simple-google-icalendar-widget')
                )
            )
            ),
            el( InspectorAdvancedControls,
				{key: 'advancedsetting'},
                el(
                    TextControl,
                    {   label: __('Cache expiration time in minutes:', 'simple-google-icalendar-widget'),
                        value: props.attributes.cache_time,
                        onChange: function( value ) { props.setAttributes( { cache_time: Math.max((parseInt(value) || 1),2) } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Excerpt length, max length of description:', 'simple-google-icalendar-widget'),
                        value: props.attributes.excerptlength,
                        onChange: function( value ) {parsed =  parseInt(value);
                                                     if (isNaN(parsed)) {parsed = ''};
                                                     props.setAttributes( { excerptlength: parsed.toString() } );},
                    }
                ),
                el(
                    SelectControl,
                    {   label: __('Tag for summary:', 'simple-google-icalendar-widget'),
                        value: props.attributes.tag_sum,
						help: el(
							'a',
							{
								href: 'admin.php?page=simple_ical_info#tag-sum',
								target: '_blank',
							},
							__('More info', 'simple-google-icalendar-widget')
						),
                        onChange: function( value ) { props.setAttributes( { tag_sum: value } );},
					    options:  [
							        { value: 'a', label: __('a (link)', 'simple-google-icalendar-widget') },
							        { value: 'b', label: __('b (attention, bold)', 'simple-google-icalendar-widget') },
								    { value: 'div', label: __('div', 'simple-google-icalendar-widget') },
								    { value: 'h4', label: __('h4 (sub header)', 'simple-google-icalendar-widget') },
								    { value: 'h5', label: __('h5 (sub header)', 'simple-google-icalendar-widget') },
								    { value: 'h6', label: __('h6 (sub header)', 'simple-google-icalendar-widget') },
							        { value: 'i', label: __('i (idiomatic, italic)', 'simple-google-icalendar-widget') },
								    { value: 'span', label: __('span', 'simple-google-icalendar-widget') },
								    { value: 'strong', label: __('strong', 'simple-google-icalendar-widget') },
							        { value: 'u', label: __('u (unarticulated, underline )', 'simple-google-icalendar-widget') }
    							] 
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix group class:', 'simple-google-icalendar-widget'),
                        value: props.attributes.suffix_lg_class,
                        help: __('Suffix to add after css-class around the event set, start with space to keep the original class and add another class (list-group).', 'simple-google-icalendar-widget'),
                        onChange: function( value ) { props.setAttributes( { suffix_lg_class: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix event start class:', 'simple-google-icalendar-widget'),
                        value: props.attributes.suffix_lgi_class,
                        help: __('Suffix to add after the css-class around each event occurrence, start with space to keep the original class (list-group-item), and add another class.', 'simple-google-icalendar-widget'),
                        onChange: function( value ) { props.setAttributes( { suffix_lgi_class: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Suffix event details class:', 'simple-google-icalendar-widget'),
                        value: props.attributes.suffix_lgia_class,
                        help: __('Suffix to add after the css-class around the event summary and details, start with space to keep the original class (ical_summary and ical_details) and add another class.', 'simple-google-icalendar-widget'),
                        onChange: function( value ) { props.setAttributes( { suffix_lgia_class: value } );},
                    }
                ),
                el(
                    ToggleControl,
                    {   label: __('Allow safe html in description and summary.', 'simple-google-icalendar-widget'),
                        checked: props.attributes.allowhtml,
                        onChange: function( value ) { props.setAttributes( { allowhtml: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Closing HTML after available events:', 'simple-google-icalendar-widget'),
                        value: props.attributes.after_events,
                        onChange: function( value ) { props.setAttributes( { after_events: value } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('Closing HTML when no events:', 'simple-google-icalendar-widget'),
                        value: props.attributes.no_events,
                        onChange: function( value ) { props.setAttributes( { no_events: value } );},
                    }
                ),
                el(
                    ToggleControl,
                    {   label: __('Clear cache.', 'simple-google-icalendar-widget'),
                        checked: props.attributes.clear_cache_now,
                        onChange: function( value ) { props.setAttributes( { clear_cache_now: value } );},
                    }
                ),
                el(
                    Button,
                    {   text: __('Reset ID.', 'simple-google-icalendar-widget'),
                        label: __('Reset ID, only necessary after duplicating block', 'simple-google-icalendar-widget'),
                        showTooltip: true,
                        variant: 'secondary',
                        onClick: function( ) { props.setAttributes( { blockid: 'b' + props.clientId } );},
                    }
                ),
                el(
                    TextControl,
                    {   label: __('HTML anchor:', 'simple-google-icalendar-widget'),
                        value: props.attributes.anchorId,
                        help: __('HTML anchor for this block. Type one or two words no spaces to create a unique web address for this block, called an "anchor". Then you can link directly to this section on your page. You can als use this ID to make parts of your extra css refer specific for this block', 'simple-google-icalendar-widget'),
                        onChange: function( value ) { props.setAttributes( { anchorId: value } );},
                    }
                )
            )			); 
		},
	} );
}( window.wp.blocks,
   window.wp.i18n,
   window.wp.element,
   window.wp.blockEditor,
   window.wp.components,
   window.wp.serverSideRender
 )
 );