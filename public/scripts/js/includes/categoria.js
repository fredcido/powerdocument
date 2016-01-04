/**
 * 
 */
function dialogCategoria()
{
    var dialog = $.modal.current;
    
    dialog.fadeOut();
    
    var option = {};

    option.title 		= 'Categorias';
    option.minWidth 	= 500;
    option.url 			= baseUrl + '/admin/perfil/categorias';
    option.complete 	= function () {
	initDialogCategoria();
    };
    option.onClose 		= function () {
	dialog.fadeIn();
    };
    					
    showDialog( option );
}

function dialogDefaultCategoria()
{
    var dialog = $.modal.current;
    
    dialog.fadeOut();
    
    var option = {};

    option.title 	= 'Categorias';
    option.minWidth 	= 500;
    option.url 		= baseUrl + '/arquivo/categorias';
    option.complete 	= function () {
	initDialogCategoria();
    };
    option.onClose 		= function () {
	dialog.fadeIn();
    };
    					
    showDialog( option );
}

/**
 * 
 */
function initDialogCategoria()
{
    $('#tab-container .tabs').css('height', '');
    $('#tab-container').resetTabContentHeight().equalizeTabContentHeight();
    
    $.modal.current.centerModal( true );
    
    $('#list-categorias li').click(
	function()
	{
	    var check = $(this).find('input.categoria_sel').eq(0);
		    
	    check.attr('checked', !check.attr('checked') );
		    
	    if ( check.attr('checked') )
		$(this).addClass( 'categoria-selected' );
	    else
		$(this).removeClass( 'categoria-selected' );
	}
	);
	
    $('#simple-search').keyup(
	function()
	{
	    var text = $(this).val();
	    var regex = new RegExp( text, 'i');
		    
	    $("p.label-categorias").each( 
		function()
		{
		    var li = $(this).parent().parent();
				    
		    if ( !text || regex.exec( $(this).text() ) ) 
			li.show();
		    else {
					
			if ( li.find('input.categoria_sel').eq(0).attr('checked') )
			    li.trigger('click');
					
			li.hide();
		    }
		}
		);
	}
	);
}

/**
 * 
 */
function addCategorias()
{
    var retorno = false;
    
    $('#list-categorias input.categoria_sel').each(
	function()
	{
	    if ( $(this).attr('checked') ) {
			
		var valor = eval( '(' + $(this).val() + ')' );
			
		if ( $('#perfil-categorias input.categorias-perfil[value=' + valor.id + ']').length < 1 ) {
			
		    var li = $('<li />');
	
		    var a = $('<a />');
		    a.attr('href', 'javascript:;');
	
		    var img = $('<img />');
		    img.attr('src', baseUrl + '/public/images/icons/fugue/cross-circle.png');
		    img.addClass('img-delete-categorias');
		    img.appendTo( a );
		    img.click( removeLiCategoria );
	
		    var checkbox = $('<input />');
		    checkbox.attr('type', 'hidden');
		    checkbox.attr('name', 'categorias[]');
		    checkbox.val( valor.id );
		    checkbox.addClass( 'categorias-perfil' );
		    checkbox.appendTo( a );
	
		    a.append( valor.nome );
		    a.appendTo( li );
	
		    li.appendTo( $('#perfil-categorias') );
		}
			
		retorno = true;
	    }
	    
	    return true;
	}
    );
	
    if ( retorno )
	$.modal.current.closeModal();
}

/**
 * 
 * @returns {Boolean}
 */
function removeLiCategoria()
{
    if ( !confirm( 'Deseja remover item?' ) ) return false;
    
    $(this).parent().parent().remove();
    return true;
}

/**
 * 
 * @param form
 * @returns
 */
function saveCategoria ( form )
{
    var obj = {
	callback: function( json ) {
	    if ( json.status )
		criaCategoriaSalva( json );
	}
    }
    
    return submitAjax( form, obj );
}

/**
 * 
 * @param dados
 */
function criaCategoriaSalva( dados )
{
    var li = $('<li />');

    var a = $('<a />');
    a.attr('href', 'javascript:;');

    var checkbox = $('<input />');
    checkbox.attr('type', 'checkbox');
    checkbox.hide();
    checkbox.val( "{'id': '" + dados.id + "', 'nome': '" + dados.data.nome + "'}" );
    checkbox.addClass( 'categoria_sel' );
    checkbox.appendTo( a );
    
    var p = $('<p />');
    p.addClass( 'label-categorias' );
    p.html( dados.data.nome );

    a.append( p );
    a.appendTo( li );

    li.prependTo( $('#list-categorias') );
    
    $('#tab-listagem').showTab();
    
    li.click(
	function()
	{
	    var check = $(this).find('input.categoria_sel').eq(0);
	    
	    check.attr('checked', !check.attr('checked') );
	    
	    if ( check.attr('checked') )
		$(this).addClass( 'categoria-selected' );
	    else
		$(this).removeClass( 'categoria-selected' );
	}
	);
}

/**
 * 
 */
function checkAllCategorias()
{
    $('#list-categorias li').each( 
	function()
	{
	    if ( !$(this).find('input.categoria_sel').eq(0).attr('checked') )
		$(this).trigger('click');
	}
	);
    
}

/**
 * 
 * @returns {Boolean}
 */
function removeAllCategorias()
{
    if ( !confirm('Deseja remover todos itens?' ) )
	return false;
    
    $('#perfil-categorias li').remove();
    
    return true;
}