$(document).ready(function()
{
    initGridAcesso();
    initGridArquivo();
    
    $('#form-relatorio').submit(
	function()
	{
	    filtraRelatorio(this);
	    
	    return false;
	}
    );
});


var filters = null;

function initGridAcesso()
{
    $('#acesso-grid').each(function(i)
    {
	    var table = $(this),
		    oTable = table.dataTable({
			    aoColumns: [
				    {bSortable: false},
				    {sType: 'string'},
				    {sType: 'string'},	
				    {sType: 'string'},
				    {sType: 'date'},
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
}

function initGridArquivo()
{
    $('#arquivo-grid').each(function(i)
    {
	    var table = $(this),
		    oTable = table.dataTable({
			    aoColumns: [
				    {bSortable: false},
				    {sType: 'string'},
				    {sType: 'string'},	
				    {sType: 'string'},
				    {sType: 'string'},
				    {sType: 'date'},
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
}

function filtraRelatorio( form )
{
    var elemLoading = $(form).find('th').eq(0);
    
    $.ajax({
	url: form.action,
	dataType: 'html',
	type: 'POST',
	data: $( form ).serialize(),
	beforeSend: function ()
	{
	    setStatusGrid( elemLoading, {
		status: 'loading'
	    } );
	    
	    $( form ).removeBlockMessages().blockMessage('Filtrando...', {
		type: 'loading'
	    });	
	},
	success: function ( response )
	{
	    $( form ).removeBlockMessages();
	    
	    $('#div-relatorio').html( response );
	    
	    initGridAcesso();
	    initGridArquivo();
	},
	error: function ()
	{
	    setStatusGrid( elemLoading, {
		status: 'error'
	    } );
	    
	    $( form ).removeBlockMessages();
	    
	    $( form ).blockMessage( 'Erro ao realizar filtros.', {type: 'error' });
	}
    });
    
    return false;
}

function imprimirHtml( form, url )
{
    var newForm = cloneForm( form, url );
    newForm.submit(
	function() 
	{
	    window.open( '','popuprelatorio', 'width=800, height=600 scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');
	    this.target = 'popuprelatorio';
	}
    );
    
    newForm.submit();
    newForm.remove();
}

function imprimirPdf( form, url )
{
    var newForm = cloneForm( form, url );
    
    newForm.submit();
    newForm.remove();
}

function cloneForm( form, url )
{
    var newForm = $('#' + form).clone();
    newForm.attr('action', url);
    newForm.hide();
    $('body').append( newForm );
    
    $('#' + form).find('select').each( 
	function( index, node )
	{
	    newForm.find( 'select' ).eq( index ).val( $(node).val() );
	}
    );
	
    return newForm;
}