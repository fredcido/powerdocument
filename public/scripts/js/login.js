function validateEmail ( email ) 
{
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	
	return !emailReg.test( email ) ? false : true;
}

$(document).ready(function()
{
	$('#password-recovery').submit(function(event)
	{
		event.preventDefault();
		
		var email = $('#recovery-mail').val();
		var error = false;
		
		switch ( true ) {
		
			case ( !email || 0 == email.length ):
				error = true;
				$('#login-block').removeBlockMessages().blockMessage('Por favor informe seu e-mail', {type: 'warning'});
				break;
			
			case ( !validateEmail(email) ):
				error = true;
				$('#login-block').removeBlockMessages().blockMessage('Informe um e-mail v&aacute;lido', {type: 'warning'});
				break;
		
		}
		
		$('#recovery-mail').removeClass( 'error' );
		$('.check-error').remove();
		
		if ( error ) {
			
			$('#recovery-mail').addClass( 'error' );
			
			var span = $('<span>');
			
			$(span).addClass( 'check-error' );
			$(span).css( 'right', '7px' );
			
			$('.relative').append( span );
			
			return false;
			
		} else {
			
			$('#recovery-mail').removeClass( 'error' );
			$('.check-error').remove();
			
		}

		var submitBt = $(this).find('button[type=submit]');
		
		submitBt.disableBt();
		
		var data = {email: email};
			
		$.ajax({
			url: $(this).attr('action'),
			dataType: 'json',
			type: 'POST',
			data: data,
			success: function( data )
			{
				if (data.valid) {
					$('#login-block').removeBlockMessages().blockMessage('Acesse o seu e-mail para redefinir sua senha');
				} else {
					$('#login-block').removeBlockMessages().blockMessage('Seu e-mail n&atilde;o &eacute; um login v&aacute;lido', {type: 'error'});
				}
				
				submitBt.enableBt();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				$('#login-block').removeBlockMessages().blockMessage('Erro ao acessar servidor, tente novamente', {type: 'error'});
				
				submitBt.enableBt();
			}
		});
		
		$('#login-block').removeBlockMessages().blockMessage('Por favor aguarde', {type: 'loading'});
		
	});
	
	$('#login-form').submit(function(event)
	{
		event.preventDefault();
		
		var login = $('#email').val();
		var pass = $('#senha').val();
		
		if (!login || login.length == 0)
		{
			$('#login-block').removeBlockMessages().blockMessage('Por favor informe seu e-mail', {type: 'warning'});
		}
		else if ( !validateEmail(login) )
		{
			$('#login-block').removeBlockMessages().blockMessage('Informe um e-mail v&aacute;lido', {type: 'warning'});
		}
		else if (!pass || pass.length == 0)
		{
			$('#login-block').removeBlockMessages().blockMessage('Por favor informe sua senha', {type: 'warning'});
		}
		else
		{
			var submitBt = $(this).find('button[type=submit]');
			
			submitBt.disableBt();

			var target = $(this).attr('action');
			
			if (!target || target == '')
			{
				target = document.location.href.match(/^([^#]+)/)[1];
			}
			
			var data = {
				//a: $('#a').val(),
				email: login,
				senha: pass,
				'keep-logged': $('#keep-logged').attr('checked') ? 1 : 0
			};
			
			var redirect = $('#redirect');
			
			if (redirect.length > 0)
			{
				data.redirect = redirect.val();
			}
			
			var sendTimer = new Date().getTime();
			
			$.ajax({
				url: target,
				dataType: 'json',
				type: 'POST',
				data: data,
				success: function(data, textStatus, XMLHttpRequest)
				{
					if (data.valid)
					{
						var receiveTimer = new Date().getTime();
						
						if (receiveTimer-sendTimer < 500)
						{
							setTimeout(function()
							{
								document.location.href = data.redirect;
								
							}, 500-(receiveTimer-sendTimer));
						}
						else
						{
							document.location.href = data.redirect;
						}
					}
					else
					{
						$('#login-block').removeBlockMessages().blockMessage(data.error || 'Seu login n&atilde;o pode ser autenticado', {type: 'error'});
						
						submitBt.enableBt();
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					$('#login-block').removeBlockMessages().blockMessage('Erro ao acessar servidor, tente novamente', {type: 'error'});
					
					submitBt.enableBt();
				}
			});
			
			$('#login-block').removeBlockMessages().blockMessage('Por favor aguarde, verificando login...', {type: 'loading'});
		}
	});
});