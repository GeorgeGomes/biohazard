<?php

use \Illuminate\Mail\Transport\SendgridTransport;

class ContactController extends BaseController {

    /**
     * Send the email from the contact form
	 *
     * @return [html block informing the results]
     */
    public function website() {
		
		$name 		= Input::get('name');
		$mailFrom 	= Input::get('email');
		$phone 		= Input::get('phone');
		$comments 	= Input::get('comments');
		$emailTo 	= 'sac@pifouapp.com.br';
		
		$subject = 'Contato realizado a partir do site Pifou por: ' . $name . '.';
		$vars = array(
			'name'		=>	$name,
			'email'		=>	$mailFrom,
			'phone'		=>	$phone,
			'comments'	=>	$comments,
		);
		
        EmailTemplate::SendByKey('contact_mail_user', $vars, $emailTo, null, null, $mailFrom);
		
		return "<fieldset><div id='success_page'><h4 class='highlight'>Obrigado, <strong>$name</strong>! Sua mensagem foi enviada. Entraremos em contato o mais rápido possível.</h4></div></fieldset>";
    }

	/**
     * Send the email from the contact form
	 *
     * @return [html block informing the results]
     */
    public function website_provider() {
		
		$name = Input::get('name');
		$mailFrom = Input::get('email');
		$phone = Input::get('phone');
		$comments = Input::get('comments');
		$emailTo = 'contato@pifouapp.com.br';
		
		$vars = array(
			'name'		=>	$name,
			'email'		=>	$mailFrom,
			'phone'		=>	$phone,
			'comments'	=>	$comments,
		);

		EmailTemplate::SendByKey('contact_mail_provider', $vars, $emailTo, null, null, $mailFrom);
		
		
		return "<fieldset><div id='success_page'><h4 class='highlight'>Obrigado, <strong>$name</strong>! Sua mensagem foi enviada. Entraremos em contato o mais rápido possível.</h4></div></fieldset>";
    }

     /**
     * Send the email from the contact form
	 *
     * @return [html block informing the results]
     */
    public function preLaunching() {
		
		$emailTo 	= Input::get('email');
		$filename = base_path().'/public/prelaunching.txt';	
		$vars = array(
			'email'		=>	$emailTo
		);

		$bytesWritten = File::append($filename, $emailTo."\n");
		if ($bytesWritten === false)
		{
		    return ['status' => false];
		}
		
        EmailTemplate::SendByKey('prelaunching', $vars, $emailTo);
		
		return ['status' => true];
    }
}
