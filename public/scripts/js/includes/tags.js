/**
 * 
 */
function dialogTag()
{
    var dialog = $.modal.current;
    
    dialog.fadeOut();
    
    var option = {};

    option.title 		= 'Tags';
    option.minWidth 	= 500;
    option.url 			= baseUrl + '/arquivo/tags';
    option.complete 	= function () {
	initDialogTag();
    };
    option.onClose 		= function () {
	dialog.fadeIn();
    };
    					
    showDialog( option );
}


/**
 * 
 */
function initDialogTag()
{
    $('#tab-container .tabs').css('height', '');
    $('#tab-container').resetTabContentHeight().equalizeTabContentHeight();
    
    $.modal.current.centerModal( true );
    
    $('#list-tags li').click(
	function()
	{
	    var check = $(this).find('input.tag_sel').eq(0);
		    
	    check.attr('checked', !check.attr('checked') );
		    
	    if ( check.attr('checked') )
		$(this).addClass( 'tag-selected' );
	    else
		$(this).removeClass( 'tag-selected' );
	}
	);
	
    $('#simple-search').keyup(
	function()
	{
	    var text = $(this).val();
	    var regex = new RegExp( text, 'i');
		    
	    $("p.label-tags").each( 
		function()
		{
		    var li = $(this).parent().parent();
				    
		    if ( !text || regex.exec( $(this).text() ) ) 
			li.show();
		    else {
					
			if ( li.find('input.tag_sel').eq(0).attr('checked') )
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
function addTags()
{
    var retorno = false;
    
    $('#list-tags input.tag_sel').each(
	function()
	{
	    if ( $(this).attr('checked') ) {
			
		var valor = eval( '(' + $(this).val() + ')' );
			
		if ( $('#tags-arquivos input.tags-arquivos[value=' + valor.id + ']').length < 1 ) {
			
		    var li = $('<li />');
	
		    var a = $('<a />');
		    a.attr('href', 'javascript:;');
	
		    var img = $('<img />');
		    img.attr('src', baseUrl + '/public/images/icons/fugue/cross-circle.png');
		    img.addClass('img-delete-tags');
		    img.appendTo( a );
		    img.click( removeLiTag );
	
		    var checkbox = $('<input />');
		    checkbox.attr('type', 'hidden');
		    checkbox.attr('name', 'tags[]');
		    checkbox.val( valor.id );
		    checkbox.addClass( 'tags-arquivos' );
		    checkbox.appendTo( a );
	
		    a.append( valor.titulo );
		    a.appendTo( li );
	
		    li.appendTo( $('#tags-arquivos') );
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
function removeLiTag()
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
function saveTag ( form )
{
    var obj = {
	callback: function( json ) {
	    if ( json.status )
		criaTagSalva( json );
	}
    }
    
    return submitAjax( form, obj );
}

/**
 * 
 * @param dados
 */
function criaTagSalva( dados )
{
    var li = $('<li />');

    var a = $('<a />');
    a.attr('href', 'javascript:;');

    var checkbox = $('<input />');
    checkbox.attr('type', 'checkbox');
    checkbox.hide();
    checkbox.val( "{'id': '" + dados.id + "', 'titulo': '" + dados.data.titulo + "'}" );
    checkbox.addClass( 'tag_sel' );
    checkbox.appendTo( a );
    
    var p = $('<p />');
    p.addClass( 'label-tags' );
    p.html( dados.data.titulo );

    a.append( p );
    a.appendTo( li );

    li.prependTo( $('#list-tags') );
    
    $('#tab-listagem').showTab();
    
    li.click(
	function()
	{
	    var check = $(this).find('input.tag_sel').eq(0);
	    
	    check.attr('checked', !check.attr('checked') );
	    
	    if ( check.attr('checked') )
		$(this).addClass( 'tag-selected' );
	    else
		$(this).removeClass( 'tag-selected' );
	}
	);
}

/**
 * 
 */
function checkAllTags()
{
    $('#list-tags li').each( 
	function()
	{
	    if ( !$(this).find('input.tag_sel').eq(0).attr('checked') )
		$(this).trigger('click');
	}
	);
    
}

/**
 * 
 * @returns {Boolean}
 */
function removeAllTags()
{
    if ( !confirm('Deseja remover todos itens?' ) )
	return false;
    
    $('#tags-arquivos li').remove();
    
    return true;
}