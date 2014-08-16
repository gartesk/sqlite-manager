function add_input() {
    var new_input = document.createElement( 'div' );
	var num = document.getElementById( 'inputi' ).getElementsByTagName( 'div' ).length;
    new_input.innerHTML = '<br>Field №' + num + '<input type="button" onclick="del_input()" value="Remove" name="d_btn" style="margin-left: 8px; margin-bottom: 4px;">'
		+ '<br><input type="text" name="all_input[]" value style="margin-right: 4px;">'
		+ '<select name="all_type[]"><option>INTEGER</option><option>REAL</option><option>TEXT</option><option>NUMERIC</option></select>'
		+ '<label style="margin-left: 8px;">pk <input type="checkbox" name="all_check[]" value="' + num + '"></label>';
    document.getElementById( 'inputi' ).appendChild( new_input );
}
function del_input() {
    document.getElementById( 'inputi' ).removeChild( document.getElementById( 'inputi' ).getElementsByTagName( 'div' )[ document.getElementById( 'inputi' ).getElementsByTagName( 'div' ).length - 1] );
}