/**
 * 
 */
function dialogLimite()
{
	var option = {};
	
	var dialog = $.modal.current;
	
	if ( dialog )
	    dialog.fadeOut();
	
	option.title 		= 'Limite';
	option.url		= baseUrl + '/admin/limite/dialog/';
	option.minWidth 	= 600;
	option.complete 	= function() 
	{			
		$('#tab-form').css({height: 'auto'});
		$('#tab-lista').css({height: 'auto'});
		
		$.modal.current.centerModal();
	};
	option.onClose		= function ()
	{
	    if ( dialog )
		dialog.fadeIn();
	}

	showDialog( option );	
}

/**
 * 
 * @param {Number} obj
 */
function setLimite ( id )
{
	$('#html-limite').loadWithEffect( baseUrl + '/admin/limite/selected/id/' + id );	
	$('#limite_id').val(id);
	
	$.modal.current.closeModal();
}

/**
 * 
 * @param form
 * @returns
 */
function saveLimite ( form )
{
	$(form).removeBlockMessages();
	
    var obj = {
		callback: function( response ) {
			if ( !response.errors ) {
				$('#tab-lista').load( 
					baseUrl + '/admin/limite/list/', 
					function () 
					{ 
						$('#abas li:first a').trigger('click');
					}
				);
			}
		}
    };
    
    return submitAjax( form, obj );	
}