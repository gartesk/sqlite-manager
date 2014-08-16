window.onload = function() {
	$$( 'button[name="send"]' ).each( function( item ) {	
		item.observe( 'click' , function() {
			var form = $( 'pm-form-simple' );
			$$( 'input[name="names[]"]' ).each( function( hid ) {
				var pkval = hid.readAttribute( 'value' );
				form.insert( hid );
				$$( 'input[name="' + pkval + '[]"]' ).each( function( elem ) {
					var clone = elem.clone();
					clone.setAttribute( 'type' , 'hidden' );
					form.insert( clone );
				});
			});
		});
	});
}