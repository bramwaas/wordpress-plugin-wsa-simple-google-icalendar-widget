( function ( blocks, element ) {
    var el = element.createElement;
 
    blocks.registerBlockType( 'wsa/simple-ical-block', {
        edit: function () {
            return el( 'p', {}, 'Hello World (from the editor).' );
        },
        save: function () {
            return el( 'p', {}, 'Hola mundo (from the frontend).' );
        },
    } );
} )( window.wp.blocks,
     window.wp.element
 );