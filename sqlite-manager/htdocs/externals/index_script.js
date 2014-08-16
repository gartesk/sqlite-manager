window.onload = function() {
	$$( '.add-checks' ).each( function( item ) {
		item.observe( 'click' , function() {
			var ctables = '&tables=';
			var noparams = true;
			$$( '.check' ).each( function( box ) {
				if( box.checked ) {
					ctables += box.readAttribute( 'value' );
					ctables += ',';
					noparams = false;
				}
			});
			if( !noparams )
				ctables = ctables.substring( 0 , ctables.length - 1 );
			var hr = item.readAttribute( 'href' );
			hr += ctables;
			item.setAttribute( 'href' , hr );
		});
	});
}