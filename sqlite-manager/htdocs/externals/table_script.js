window.onload = function() {
	$$( '.add-checks' ).each( function( item ) {
		item.observe( 'click' , function() {
			var cvals = '&values=';
			var noparams = true;
			$$( '.check' ).each( function( box ) {
				if( box.checked ) {
					cvals += box.readAttribute( 'value' );
					cvals += ',';
					noparams = false;
				}
			});
			if( !noparams )
				cvals = cvals.substring( 0 , cvals.length - 1 );
			var hr = item.readAttribute( 'href' );
			hr += cvals;
			item.setAttribute( 'href' , hr );
		});
	});
}