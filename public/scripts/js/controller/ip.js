$(document).ready(function()
{
	$('.sortable').each(function(i)
	{
		var table = $(this),
		oTable = table.dataTable({
			aoColumns: [
				{bSortable: false},
				{sType: 'string'},	
				{sType: 'string'},
				{bSortable: false}
			],
			
			sDom: '<"block-controls"<"controls-buttons"p>>rti<"block-footer clearfix"lf>',
			
			fnDrawCallback: function()
			{
				this.parent().applyTemplateSetup();
			},
			fnInitComplete: function()
			{
				this.parent().applyTemplateSetup();
			}
		});
		
		table.find('thead .sort-up').click(function(event)
		{
			event.preventDefault();
			
			var column = $(this).closest('th'),
				columnIndex = column.parent().children().index(column.get(0));
			
			oTable.fnSort([[columnIndex, 'asc']]);
			
			return false;
		});
		table.find('thead .sort-down').click(function(event)
		{
			event.preventDefault();
			
			var column = $(this).closest('th'),
				columnIndex = column.parent().children().index(column.get(0));
			
			oTable.fnSort([[columnIndex, 'desc']]);
			
			return false;
		});
	});
	
	$('#novo').click(function(){
		dialog( baseUrl + '/admin/ip/form' );
	});	
});

/**
 * 
 * @param 	{Object} form
 * @returns
 */
function save ( form )
{   
	$(form).removeBlockMessages();
	
    var obj = {
		callback: function( json ) {
		    if ( json.status )
			history.go( 0 );
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
	option.complete	    = function() 
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
    
    var th = $('#table_form').find('th').eq(0);

    $.ajax({
    	type: 'POST',
    	data: {id: id},
    	dataType: 'json',
    	url: baseUrl + '/admin/ip/delete/',
    	beforeSend: function ()
    	{
    		setStatusGrid( th, {status: 'loading'} );
    	},
    	success: function ( response )
    	{
    		history.go(0);
    	},
    	error: function ()
    	{
    		setStatusGrid( th, {status: 'error'} );
    	}
    });
}