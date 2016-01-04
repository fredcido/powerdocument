jQuery(
	function ($) 
	{
		if ( $('.telmask')[0] ) 
			$('.telmask').inputmask('(99) 9999-9999');
		
		if ( $('.datamask')[0] )
			$('.datamask').inputmask({'mask': 'd/m/y'});
		
		if ( $('.hourmask')[0] )
			$('.hourmask').inputmask({'mask': '99:99'});
	}
);


/**
 * 
 */
$(document).ready(
    function() 
    {
	$("#table-action").change(
	    function() {
		if ( $(this).val() )
		    $(this).parent().find('button[type=submit]').enableBt();
		else
		    $(this).parent().find('button[type=submit]').disableBt();
	    }
        );
	
	(function($){
		$.fn.serializeJson = function () 
		{
			var json = {};
		
			jQuery.map(
				$(this).serializeArray(), 
				function ( n, i )
				{
					json[n['name']] = n['value'];
				}
			);
		
			return json;
		};
	})( jQuery );

	
	$.fn.dataTableExt.oStdClasses.sWrapper = 'no-margin last-child';
	$.fn.dataTableExt.oStdClasses.sInfo = 'message no-margin';
	$.fn.dataTableExt.oStdClasses.sLength = 'float-left';
	$.fn.dataTableExt.oStdClasses.sFilter = 'float-right';
	$.fn.dataTableExt.oStdClasses.sPaging = 'sub-hover paging_';
	$.fn.dataTableExt.oStdClasses.sPagePrevEnabled = 'control-prev';
	$.fn.dataTableExt.oStdClasses.sPagePrevDisabled = 'control-prev disabled';
	$.fn.dataTableExt.oStdClasses.sPageNextEnabled = 'control-next';
	$.fn.dataTableExt.oStdClasses.sPageNextDisabled = 'control-next disabled';
	$.fn.dataTableExt.oStdClasses.sPageFirst = 'control-first';
	$.fn.dataTableExt.oStdClasses.sPagePrevious = 'control-prev';
	$.fn.dataTableExt.oStdClasses.sPageNext = 'control-next';
	$.fn.dataTableExt.oStdClasses.sPageLast = 'control-last';
	
	$.fn.updateTabs.enabledHash = false;
        
    }
);



/**
 * 
 * @param check
 */
function checked ( check )
{
    $("input[type='checkbox']").attr('checked', check);
}

function checkChildren( check )
{
    $(check).parent().parent().parent().find('input').attr('checked', $(check).attr('checked') );
}
/**
 * 
 * @param form
 * @returns {Boolean}
 */
function status ( form )
{
    var data 		= [];
    var action 		= $('#table-action').val();
    var elemLoading = $(form).find('th').eq(0);

    $(form).find("input[type='checkbox']:checked").each(
	function ()
	{
	    data.push( $(this).val() );
	}
    );
	
    if ( !data.length ) {
	setStatusGrid( elemLoading );
	return false;
    }
	
    $.ajax({
	url: $(form).attr('action'),
	dataType: 'json',
	type: 'POST',
	data: {
	    data: data, 
	    action: action
	},
	beforeSend: function ()
	{
	    setStatusGrid( elemLoading, {
		status: 'loading'
	    } );
	},
	success: function ( response )
	{
	    if ( response.result ) {
		
		$(form).find("input[type='checkbox']:checked").each(
		    function ()
		    {
			$(this).parent().parent().find('td').eq(1).find('small').remove();
			$(this).parent().parent().find('td').eq(1).append( loadStatusGrid(action) );
		    }
		    );
				
		setStatusGrid( elemLoading, {
		    status: 'success'
		} );
			
		checked( false );
	    }
	},
	error: function ( response )
	{
	    setStatusGrid( elemLoading, {
		status: 'error'
	    } );
	}
    });
	
	
    $('#table-action').val('');
    $('#table-action').parent().find('button').disableBt();
	
    return false;
}

/**
 * 
 * @param th
 * @param status
 */
function setStatusGrid ( th, option )
{
    $(th).find('span').removeClass();
	
    if ( option ) {
	
	switch ( option.status ) {
		
	    case 'success':
		$(th).find('span').addClass('success');
		break;
				
	    case 'error':
		$(th).find('span').addClass('error');
		break;
				
	    case 'loading':
		$(th).find('span').addClass('loading');
		break;
				
	}
		
    }
}

/**
 * Carrega o status na grid
 * 
 * @param action
 * @returns
 */
function loadStatusGrid ( action )
{
    action = parseInt( action );
	
    var icone 	= action ? 'status.png' : 'status-busy.png';
    var text 	= action ? 'Aprovado' : 'Pendente';
    var small 	= $('<small>');
    var img 	= $('<img>');
	
    $(img).addClass('picto');
    $(img).attr('width', '16');
    $(img).attr('height', '16');
    $(img).attr('src', baseUrl + '/public/images/icons/fugue/' + icone);
	
    $(small).append(img);
    $(small).append(text);
	
    return small;
}

/**
 * 
 * @param param
 */
function showDialog ( param )
{
    var option 	= {};

    option.maxWidth 		= 600;
    option.maxHeight 		= 600;
    option.scrolling 		= true;
    option.loadingMessage 	= 'Carregando...';

    for ( i in param ) 
	option[i] = param[i];

    return $.modal( option );	
}

/**
 * 
 * @param form
 * @param obj
 * @return
 */
function submitAjax ( form, obj )
{
    var pars = $(form).serialize();
	
    hideErrorsForm();
	
    $.ajax({
	type: 'POST',
	data: pars,
	dataType: 'json',
	url: form.action,
	beforeSend: function()
	{
	    $(form).removeBlockMessages().blockMessage('Aguarde...', {
		type: 'loading'
	    });			
	},
	success: function ( response )
	{
	    if ( obj && obj.callback )
		execFunction( obj.callback, response );
			
	    $(form).removeBlockMessages();
					
	    $(response.description).each(
		function ( index, value )
		{
		    $(form).blockMessage(value.message,{
			type: value.level
			});
		}
		);
			
	    if ( !response.status ) {

		$(form).find('ul.errors').remove();

		if ( response.errors )
		    showErrorsForm( response.errors );
				
	    }
	    
	    //$.modal.current.centerModal();
	},
	error: function( response )
	{
	    $(form).removeBlockMessages().blockMessage(response.message, {
		type: 'error'
	    });
	}
    });
	
    return false;
}

/**
 * 
 * @param fnName
 * @param params
 */
function execFunction( fnName, params )
{
    // Se existir o callback
    if ( typeof fnName == 'function' ) {
	return fnName( params );
    } else if ( typeof fnName == 'string' ) {

	var fn = window[fnName];
	if ( typeof fn == 'function' )
	    return fn( params );
	else
	    return false;
    }

    return false;
}


/**
 * 
 * @param errors
 */
function showErrorsForm( errors )
{
    for ( index in errors ) {
	
	var ul = $('<ul />');
	ul.addClass('errors');
	ul.attr('id', 'error_' + index );

	$('#' + index).addClass('error');

	$('#' + index).parent().append( ul );

	for ( m in errors[index] ) {
	    var li = $('<li />').html( errors[index][m] );
	    ul.append( li );
	}
		
    }
}

function hideErrorsForm ()
{
    $('*').removeClass('error');
    $('.check-error').remove();
}

function showFileSelected ( value, fakefile )
{
    if ( value.length > 30 ) 
	value = value.substr( 0, 30 ) + '...';
	
    $('#' + fakefile ).val( value );
}

function getUniqueId()
{
    var dateObject = new Date();
    var uniqueId =
    dateObject.getFullYear() + '' +
    dateObject.getMonth() + '' +
    dateObject.getDate() + '' +
    dateObject.getTime();

    return uniqueId;
}

/**
 * 
 * @param {String} str
 */
function ucfirst ( str )
{
	return str.charAt(0).toUpperCase() + str.substr(1).toLowerCase();
}

/**
 * 
 * @param {String} file
 */
function include ( file )
{
	var filename = baseUrl + '/public/scripts/js/includes/' + file + '.js';
	
	document.write(unescape("%3Cscript src='" + filename + "' type='text/javascript'%3E%3C/script%3E"));	
}

function resizeTab ()
{
    var css = {
	marginBottom: '20px',
	height: 'auto'
    };
	
    $('div.tabs').each(
	function ()
	{
	    $(this).css(css);	
	}
	);
	
    $('#abas a').click(
	function()
	{
	    $.modal.current.centerModal();
	}
	);	
	    
    $.modal.current.centerModal();    
}

function remover( url )
{
    if ( !confirm('Deseja realmente remover este item?' ) )
	return false;
    
    $.ajax({
	type: 'POST',
	dataType: 'json',
	url: url,
	beforeSend: function()
	{
	    $('.block-content').removeBlockMessages().blockMessage('Aguarde...', {
		type: 'loading'
	    });			
	},
	success: function ( response )
	{
	   if ( !response.status ) {
	       $('.block-content').removeBlockMessages().blockMessage(response.message, {
		    type: 'error'
		});
	   } else history.go( 0 );
	   
	},
	error: function( response )
	{
	    $('.block-content').removeBlockMessages().blockMessage(response.message, {
		type: 'error'
	    });
	}
    });
    
    return true;
}

function loadCombo( url, selector, callback )
{
    var combo = $( selector );
    
    $.ajax({
	type: 'GET',
	url: baseUrl + url,
	dataType: 'json',
	beforeSend: function()
	{
	    combo.html( '<option value="">Aguarde...</option>' );
	},
	success: function ( response ) 
	{
	    combo.empty();
			
	    if ( response ) {
				
		for ( i in response ) {
		    
		    option = $( '<option />' );
		    option.val( response[i].id );
		    option.html( response[i].name );

		    combo.append( option );
		}
				
		combo.focus();
		combo.removeAttr( 'disabled' );
		
		if ( callback )
		    callback();
	
	    } else combo.attr( 'disabled', true );
	},
	error: function () 
	{
	    combo.html( '<option value="">Erro. Aperte F5.</option>' );
	}
    });
    
    return true;
}