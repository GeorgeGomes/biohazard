<?php
use \Illuminate\Mail\Transport\SendgridTransport;

class EmailTemplate extends \Eloquent {
	protected $fillable = array('id', 'subject', 'key','copy_emails','from');

	protected $table = 'email_template';

	public static function SendByKey($key, $vars, $emailTo, $subject = null,  $replyTo = null){
		try {

			$emailTemplate = EmailTemplate::where('key', $key)->first();

			if(!$subject){
				$subject = $emailTemplate->subject;
			}
			
			$emailFrom = $emailTemplate->from;
			if(!$emailFrom){
				$emailFrom = Settings::getAdminEmail();
			}
			if(Config::get('mail.driver') == 'sendgrid'){

				SendgridTransport::send2(View::make('emails.'.$key, array('vars' => $vars)), $subject, $emailFrom, $emailTo, Config::get('services.sendgrid.secret'), $replyTo, $emailTemplate->copy_emails);
			}else{
				Mail::send('emails.invoice', array('vars' => $vars), function ($message) use ($email, $subject) {
					$message->to($emailTo)->subject($subject);
					$message->replyTo($replyTo);
					$message->bcc($emailTemplate->copy_emails);
				});
			}
		} catch (Exception $e) {
			Log::error($e);
		}
	}
}