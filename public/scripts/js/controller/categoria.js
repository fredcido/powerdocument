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
					{sType: 'date'},
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
		dialog( baseUrl + '/admin/categoria/form' );
	});
	
	$('#btn-tree').click(function(){
		dialogTree( baseUrl + '/admin/categoria/tree' );
	});

});

function dialog( url )
{
	var option = {};
	
	option.title 	= 'Categoria';
	option.url 	= url;
	option.complete = resizeTab;

	showDialog( option );
}

function dialogTree( url )
{
	var option = {};
	
	option.title 	= 'Organizar categorias';
	option.url 	= url;
	option.complete = configTree;
	option.width	 = 400;
	option.height    = 300;

	showDialog( option );
}

function configTree()
{
    $( '#categoria-tree' ).on( 'move_node.jstree', 
	function ( e, data ) {
	    
	     container = $( '#categoria-tree' );
	     $.ajax({
		type: 'POST',
		data: {
		    parent: data.parent,
		    id: data.node.id
		},
		dataType: 'json',
		url: baseUrl + '/admin/categoria/reorder/',
		beforeSend: function()
		{
		    $(container).removeBlockMessages().blockMessage('Aguarde...', {
			type: 'loading'
		    });			
		},
		success: function ( response )
		{
		     $(container).removeBlockMessages();
		},
		error: function()
		{
		    $(container).removeBlockMessages().blockMessage( 'Erro ao organizar categoria', {
			type: 'error'
		    });
		}
	    });
	}
    ).jstree(
	{
	    "core" : { 
		"check_callback" : true
	    },
	    "themes" : { "stripes" : true },
	    "plugins" : ["dnd"]
	}
    );
}

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