function save ( form )
{
    var obj = {
	callback: function( json ) {
	    if ( json.status )
		history.go( 0 );
	}
    }
    
    return submitAjax( form, obj );
}