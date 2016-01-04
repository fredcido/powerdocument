include('limite');

/**
 * 
 * @param {Object} form
 */
function save ( form )
{
	$(form).removeBlockMessages();
	
    var obj = {
		callback: function( response ) {
		    if ( response.status ) {
		    	
		    	if ( $.modal.current ) {
		    		$.modal.current.closeModal();
		    	
			    	$('.grid_5 .block-content').loadWithEffect(
		    			baseUrl + '/admin/configuracao/ip/',
		    			function ()
		    			{
		    				$('.mini-menu').css({opacity: 1});
		    			}
		    		);
		    	}
		    }
		}
    };
    
    return submitAjax( form, obj );
}

/**
 * 
 * @param {String} url
 */
function dialog( url )
{
	var option = {};
	
	option.title 	= 'IP';
	option.url 		= url;
	option.modal 	= false;
	option.complete = function() 
	{ 
		$.modal.current.centerModal();
	};

	showDialog( option );
}

/**
 * 
 * @param 	{Number} id
 * @returns {Boolean}
 */
function remove ( id )
{
    if ( !confirm('Deseja remover? ') )
    	return false;
    
    $.ajax({
    	type: 'POST',
    	data: {id: id},
    	dataType: 'json',
    	url: baseUrl + '/admin/ip/delete/',
    	beforeSend: function ()
    	{
    		$('.grid_5 .block-content').loadWithEffect();
    	},
    	success: function ( response )
    	{
    		$('.grid_5 .block-content').load(
    			baseUrl + '/admin/configuracao/ip/',
    			function ()
    			{
    				$('.loading-mask').remove();
    				$('.mini-menu').css({opacity: 1});
    			}
    		);
    	}
    });
    
    return false;
}