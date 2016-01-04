include('categoria');
include('limite');

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
					{bSortable: false},
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
		dialog( baseUrl + '/admin/perfil/form' );
	});
});

function dialog( url )
{
    var option = {};

    option.title = 'Perfil';
    option.url 	= url;
    option.minWidth = 600;
    option.complete = iniDialogPerfil;
    
    showDialog( option );
}

function iniDialogPerfil()
{
    resizeTab();
    $('#perfil-categorias li img.img-delete-categorias').click( removeLiCategoria );
}

function save ( form )
{
    var obj = {
	callback: function( json ) {
	    if ( json.status )
		history.go( 0 );
	}
    }
    
    $('#tab-data').showTab();
    
    return submitAjax( form, obj );
}

function checkAll( chk )
{
    $('#list-acoes input').attr('checked', !$(chk).attr('checked') );
}