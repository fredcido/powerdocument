$(document).ready(function()
    {
	$('.sortable').each(function(i)
	{
	    var table = $(this),
	    oTable = table.dataTable({
		aoColumns: [
		    { bSortable: false },	
		    { sType: 'string' },
		    { sType: 'string' },
		    { sType: 'string' },
		    { sType: 'string' },
		    { sType: 'string' },
		    { bSortable: false }	
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
	    dialog( baseUrl + '/cliente/form' );
	});

    });

function dialog( url )
{
    var option = {};
	
    option.title = 'Cliente';
    option.url 	= url;
    option.width = 700;
    option.complete = function()
    {
    	resizeTab();
    };

    showDialog( option );
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