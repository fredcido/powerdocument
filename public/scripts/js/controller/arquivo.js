include('categoria');
include('tags');

var filter = {
	extensao	: [],
	categoria	: [],
	tag		: [],
	form 		: null,
	status 		: 1
};

var viewType = 'view-list';

$(document).ready(
	function ()
	{
		
		$('#tree-filter .toggle').live('click', 
		    function () 
		    { 
		    	var selector = $(this).parent();
		    	
		    	if ( !$(selector).hasClass('closed') ) 
		    		loadTree( $(selector) );	
		    	else 
		    		$(selector).find('ul').remove();
		    } 
		);
                    		
		$('#tree-filter .folder').click(
			function ()
			{
				$(this).parent().find('.toggle').trigger('click');
			}
		);
		
		$('#check-filter-status').click(
			function ()
			{
				filter.status = $('#check-filter-status').prop('checked') ? 1 : 0;
				
				showFiles();
			}
		);
		
		showInfoMessage( 1 );
		
		$('ul.controls-buttons a.view-type').click(
		    function()
		    {
			$('ul.controls-buttons a.view-type').removeClass('view-selected');
			$(this).addClass( 'view-selected' );
			
			viewType = $(this).attr('id');
			
			showFiles();
		    }
		);
		    
		configGridFiles();
		
		$( '#form-download-arquivo, #form-multiplos-download' ).on( 'submit',
		    function()
		    {
			setTimeout(
			    function()
			    {
				$('#limite-user').load( baseUrl + '/arquivo/percentual/');
			    },
			    2000
			)
		    }
		);
	}
);

/**
 * Carrega dados na arvore de arquivo
 *  
 * @param {Object} selector
 */
function loadTree ( selector )
{
	var ulCount = $(selector).find('ul').length;
	var ul;
	
	if ( !ulCount ) {
		ul = $('<ul>');
		$(selector).append( ul );
	} else {
		ul = $(selector).find('ul').eq( ulCount - 1 );
	}
        
        /**
         * Guarda o tipo do filtro 
         * 
         * @exemple
         * filter = 0; //Extensao
         * filter = 1; //Categoria
         * filter = 2; //Tag 
         */
        var filter = $(selector).find('span').hasClass('child') ? 1 : $(selector).index();
	
	$.ajax({
		type: 'POST',
		data: {
                    filter: filter, 
                    child: $(selector).find('a').attr('rel')
                },
		dataType: 'text',
		url: baseUrl + '/arquivo/tree/',
		beforeSend: function()
		{
			treeValue( ul, 'loading' );
			statusGrid(0, 'loading');
		},
		success: function ( response )
		{
			statusGrid(0, 'success');
			
			if ( !response )
				treeValue( ul, 'empty' );
			else
				ul.html( response );
			
			populaFilterTree();
		},
		error: function( response )
		{
			statusGrid(0, 'error');
		}
	});
}

/**
 * 
 * @param {Object} ul
 * @param {String} status
 */
function treeValue ( ul, status )
{
	var li = $('<li>');
	var span = $('<span>');

	$(span).addClass( status );
	$(span).text( ucfirst(status) );
	
	$(li).append( span );

	$(ul).empty();
	$(ul).append( li );
}

/**
 * Guarda os dados para serem utilizados na visualizacao de arquivos
 * 
 * @param {Object} obj
 * @param {Number} indice
 * @param {Number} value
 */
function chooseFilterTree ( obj, indice, value )
{	
	var result = $.inArray( value, filter[indice] );
	
	if ( -1 == result ) {
		$(obj).addClass('current');
		filter[indice].push( value );
	} else {
		$(obj).removeClass('current');
		filter[indice].splice( result, 1 );
	}
	
	showInfoMessage( 0 );
	
	showFiles();

}

/**
 * Limpa filtro
 * 
 * @param {String} indice
 * @returns
 */
function cleanFilterTree ( indice ) 
{	
	var selector = '#tree-filter > li';
	
	switch ( indice ) {
	
		case 'extensao':
			filter.extensao = [];
			$(selector).eq(0).find('ul > li > a').removeClass('current');
			break;
			
		case 'categoria':
			filter.categoria = [];
			$(selector).eq(1).find('ul > li > a').removeClass('current');
			break;
			
		case 'tag':
			filter.tag = [];
			$(selector).eq(2).find('ul > li > a').removeClass('current');
			break;
			
		default:
			$.each( 
			    filter, 
			    function ( key, value ) 
			    {
				if ( $.isArray( filter[key] ) )
				    filter[key] = []; 
			    } 
			);
			    
			$(selector).find('ul > li > a').removeClass('current');
	
	}
	
	showInfoMessage( 0 );
	
	showFiles();
	
}

/**
 * 
 * @returns
 */
function populaFilterTree ()
{
	var indice = ['extensao', 'categoria', 'tag'];
	
	$('#tree-filter > li').each(
		function ( i )
		{
			$(this).find('ul > li > a').each(
				function ()
				{
					if ( filter[indice[i]].length && -1 != $.inArray( parseInt($(this).attr('rel')), filter[indice[i]] ) ) 
						$(this).addClass('current');
				}
			);
		}
	);
}

/**
 * 
 * @returns
 */
function showFiles ()
{
	filter.form = $('#filter-form').serializeJson();
	
	$.ajax({
		type: 'POST',
		data: {
		    data: filter,
		    viewType: viewType
		},
		dataType: 'text',
		url: baseUrl + '/arquivo/view/',
		beforeSend: function()
		{
			statusGrid(1, 'loading');
		},
		success: function ( response )
		{
			statusGrid(1, 'success');
			
			$('#painel').html( response );

			showInfoMessage( 1 );
			
			$('.with-tip').mouseover(
				function ()
				{
					$(this).showTip();
				}
			).mouseout(
				function () 
				{
					$(this).hideTip();
				}
			);
			    
			$('#limite-user').load( baseUrl + '/arquivo/percentual/');
			
			configGridFiles();
		},
		error: function( response )
		{
			statusGrid(1, 'error');
		}
	});	
}

/**
 * 
 */
function showInfoMessage ( indice )
{
	var count = 0;
	var message;
	
	if ( 1 == indice ) {
	
		count = viewType == 'view-list' ? $('#painel #grid-list-files tbody tr').length : 
			$('#painel .grid > li').length;
		
		switch ( count ) {
		
			case 0:
				message = 'Nenhum arquivo encontrado';
				break;
				
			case 1:
				message = '1 Arquivo encontrado';
				break;
				
			default:
				message = count + ' arquivos encontrados';
		
		}
		
	} else {
		
		count += filter.categoria.length;
		count += filter.extensao.length;
		count += filter.tag.length;
		
		switch ( count ) {
		
			case 0:
				message = 'Nenhum item encontrado';
				break;
				
			case 1:
				message = '1 Item selecionado';
				break;
				
			default:
				message = count + ' Itens selecionados';
		
		}
		
	}
	
	$('.message').eq( indice ).find('li').text( message );
}

/**
 * 
 * @param indice
 * @param status
 * @returns
 */
function statusGrid ( indice, status )
{
	$('.black-cell').eq( indice ).find('span').removeClass().addClass( status );
}

/**
 * 
 * @param {Object} obj
 * @param {Number} id
 * @returns
 */
function refreshStatusFile( obj, id, status )
{
	status = status ? 'bloquear' : 'liberar';
	
	if ( !confirm('Deseja realmente ' + status + ' este item ? ') ) return false;
	
	var path = '/public/images/icons/fugue/';
	var image = ['cross-circle.png', 'tick-circle.png'];
	
	$.ajax({
		type: 'POST',
		data: {id: id},
		dataType: 'json',
		url: baseUrl + '/arquivo/status/',
		beforeSend: function()
		{
			statusGrid(1, 'loading');
		},
		success: function ( response )
		{
			if ( response.result ) {
				
				statusGrid(1, 'success');
				
				$(obj).find('img').attr('src', baseUrl + path + image[response.status]);//
				
				showFiles();
				
			} else statusGrid(1, 'error');
		},
		error: function( response )
		{
			statusGrid(1, 'error');
		}
	});	
	
	return true;
}

/**
 * 
 * @param {String} url
 * @returns
 */
function dialog( url )
{
	var option = {};
	
	option.title 	= 'Arquivo';
	option.url 	= url;
	option.width = 700;
	option.complete = function () { 
		resizeTab();
		
		var css = {
			marginBottom: '20px',
			height: 'auto'
		};
		
		$('#tab-dados').css(css);
		$('#tab-aviso').css(css);
		$('#tab-metatags').css(css);
		$('#tab-categoria').css(css);
		$('#tab-tag').css(css);
		
		configFormUpload();
	};

	showDialog( option );
}

function configFormUpload()
{
    var bar = $( '.upload-progress .bar' );
    var percent = $( '.upload-progress .info' );
    var form = $( '#form-arquivo' );
    var clearProgress = function() 
    { 
	var percentVal = '0%';
	bar.width( percentVal );
	percent.html( percentVal );
    };

    form.ajaxForm({
	beforeSubmit: function()
	{
	    $('#tab-dados').showTab();
	    $( form ).removeBlockMessages();
	    
	    if ( !$('#id').val() ) {
		if ( !$('#arquivo').val() ) {

		    $('#container-upload').addClass('error');
		    $(form).blockMessage('Selecione o arquivo para fazer o upload.', {
			type: 'warning'
		    });

		    return false;

		}
	    }

	    $( '#container-upload' ).removeClass('error');
	},
	dataType: 'json',
        beforeSend: function()
	{
	    clearProgress();
	    $( form ).blockMessage( 'Aguarde...', { type: 'loading' } );
	},
        uploadProgress: function( event, position, total, percentComplete ) 
	{
            var percentVal = percentComplete + '%';
            bar.width( percentVal );
            percent.html( percentVal );
        },
	success: function( json )
	{
	    if ( json.status ) {
			
		$.modal.current.closeModal();
		showFiles();

	    } else {
		
		clearProgress();

		$( form ).removeBlockMessages();
		$( json.description ).each(
			function ( index, value )
			{
			    $( form ).blockMessage( value.message, {type: value.level});
			}
		);

		if ( json.errors )
		    showErrorsForm( json.errors );
	    }
	},
        error: function() {
	    
	    clearProgress();
	    
	    $( form ).removeBlockMessages();
	    $( form ).blockMessage( 'Erro ao realizar upload.', {
		type: 'error'
	    });
        }
    });
}

function openFile ( url )
{
	var option = {};
	
	option.title 	= 'Arquivo';
	option.url	= url;
	option.width 	= 800;
	option.complete = resizeTab;

	showDialog( option );	
}

/**
 * 
 * @param {Object} form
 * @returns
 */
function save ( form )
{
     $(form).removeBlockMessages();
	    
	    
    if ( !$('#id').val() ) {
	if ( !$('#arquivo').val() ) {
    	
	    $('#container-upload').addClass('error');
	    $(form).blockMessage('Selecione o arquivo para fazer o upload.', {
		type: 'warning'
	    });
    	
	    return false;
    	
	}
    }
    
    $('#container-upload').removeClass('error');
    
    createProgress();

    $.modal.current.fadeOut();
        
    return true;
}

function createProgress()
{
    var imgNome = $('#arquivo').val();
    var imgHash = $('#loading_hash').val();
    
    imgNome = imgNome ? imgNome : $('#nome').val();
    
    var span = $( '<span />' );
    span.addClass( 'button' );
    span.attr( 'id', imgHash );
    span.html( imgNome );
    
    var spanProgress = $('<span />');
    spanProgress.addClass( 'progress-bar' );
    spanProgress.appendTo( span );
    
    var spanStripes = $('<span />');
    spanStripes.addClass( 'orange with-stripes' );
    spanStripes.appendTo( spanProgress );
    
    span.appendTo( $('#progress-container') );
    
    span.click( function(){ $(this).remove() } );
}

/**
 * 
 * @param iframe
 * @returns
 */
function callBackUpload( iframe )
{
	var div = $(iframe).contents().find('div');
    
	if ( div.length ) {
	
		var json = eval('(' + div.eq(0).html() + ')' );
	
		if ( json.status ) {
			
			$.modal.current.closeModal();
			
			showFiles();

		} else {
	    
			$.modal.current.fadeIn();
	    
			$('#form-arquivo').removeBlockMessages();
			
			$(json.description).each(
				function ( index, value )
				{
					$('#form-arquivo').blockMessage( value.message, {type: value.level});
				}
			);
		    
			if ( json.errors )
				showErrorsForm( json.errors );
		}
		
		$('#' + json.data.loading_hash ).remove();
	}
    
	return true;
}

function downloadArquivo( id )
{
    if ( !id )
	return false;
    
   $.ajax({
	url: baseUrl + '/arquivo/valida-download/',
	dataType: 'json',
	type: 'POST',
	data: {arquivo: id},
	beforeSend: function()
	{ 
	     $('#form-download-arquivo').removeBlockMessages().blockMessage( 'Aguarde..', {type: 'loading'} ); 
	},
	success: function ( response )
	{
	    $('#form-download-arquivo').removeBlockMessages();
	    
	    if ( response.valid ) {
		
		$('#arquivo_id').val( id );
		$('#form-download-arquivo').submit();
		
	    } else {
		
		$('#form-download-arquivo').blockMessage( response.message, {type: 'error'});
	    }
	},
	error: function()
	{
	    $('#form-download-arquivo').removeBlockMessages().blockMessage( 'Erro ao realizar download.', {type: 'error'}); 
	}
    });
    
    return true;
}

function downloadMultiplos()
{
   var arquivos = new Array();
   $('input.check-arquivos').each(
	function()
	{
	    if ( $(this).attr('checked') )
		arquivos.push( $(this).val() );
	}
    );
	
   $('.block-content').removeBlockMessages();
	
    if ( !arquivos.length )
	return false;
    
    $.ajax({
	url: baseUrl + '/arquivo/valida-download-multiplos/',
	dataType: 'json',
	type: 'POST',
	data: {arquivos: arquivos},
	beforeSend: function() {statusGrid(1, 'loading');},
	success: function ( response )
	{
	    if ( response.valid ) {
		
		$('#form-multiplos-download').submit();
		statusGrid(1, 'success');
		
	    } else {
		
		statusGrid(1, 'error');
		$('.block-content').blockMessage( response.message, {type: 'error'});
	    }
	},
	error: function() {statusGrid(1, 'error');}
    });
    
    return true;
}

function excluir( id )
{
    if ( !id )
	return false;
    
    if ( !confirm( 'Deseja realmente remover este item?' ) )
	return false;
    
    $.ajax({
	url: baseUrl + '/arquivo/delete/id/' + id,
	dataType: 'json',
	type: 'POST',
	beforeSend: function() {statusGrid(1, 'loading');},
	success: function ( response )
	{
	    if ( response.valid ) {
		
		showFiles();
		statusGrid(1, 'success');
		
	    } else {
		
		statusGrid(1, 'error');
		$('.block-content').blockMessage( response.message, {type: 'error'});
	    }
	},
	error: function() {statusGrid(1, 'error');}
    });
    
    return true;
}

function checkAll( chk )
{
    $('#list-perfis input').attr('checked', !$(chk).attr('checked') );
}

function configGridFiles()
{
    $('#grid-list-files').each(function(i)
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
				    {bSortable: false}
			    ],

			    //sDom: '<"block-controls"<"controls-buttons"p>>rti<"block-footer clearfix"lf>',

			    sDom: '<"block-controls">rti<"block-footer clearfix"lf>',
			    
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