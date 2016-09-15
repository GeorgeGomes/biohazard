<?php namespace Illuminate\Mail\Transport;

use Swift_Transport;
use GuzzleHttp\Client;
use Swift_Mime_Message;
use GuzzleHttp\Post\PostFile;
use Swift_Events_EventListener;
use Log;

require (base_path() . '/vendor/sendgrid-php/vendor/autoload.php');
require (base_path() . '/vendor/sendgrid-php/lib/SendGrid/Email.php');

class SendgridTransport implements Swift_Transport {
	/**
	 * The Sendgrid API key.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * The Sendgrid username.
	 *
	 * @var string
	 */
	protected $username;

	/**
	 * THe Sendgrid API end-point.
	 *
	 * @var string
	 */
	
	/**
	 * Create a new Sendgrid transport instance.
	 *
	 * @param  string  $key
	 * @param  string  $username
	 * @return void
	 */
	public function __construct($key, $username)
	{
		$this->key = $key;
		//$this->setUsername($username);
		
		$this->username = $username;
		//$sendgrid = new SendGrid($this->key);
		
	}

	/**
	 * {@inheritdoc}
	 */
	public function isStarted()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function start()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function stop()
	{
		return true;
	}

	public static function send2($message_body, $subject, $mailFrom, $mailTo, $key, $replyTo = null, $copyEmails = null)// Utiliza driver sendgrid 
	{
		$sendgrid = new SendGrid($key);
		$email = new SendGrid\Email();
		$email
		    ->addTo($mailTo)
		    ->setFrom($mailFrom)
		    ->setSubject($subject)
		    ->setText($message_body)
		    ->setHtml($message_body);

	    if($replyTo)
	    	$email->setReplyTo($replyTo);

	    if($copyEmails)
	    	$email->addCc($copyEmails);

		$response = $sendgrid->send($email);
		Log::info("sendgrid response");
		Log::info(print_r($response, 1));
	}

	/**
	 * {@inheritdoc}
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$client = $this->getHttpClient();

		//echo "<script>var_dump(".serialize($message).")</script>";

		// $sendgrid = new SendGrid($this->key);

		// $email = new SendGrid\Email();

		// $email->addTo('roque.goncalves@codificar.com.br')
		//       ->setFrom("uberclone@codificar.com.br")
		//       ->setSubject($message->getSubject()) //OK
		//       ->setText($message->getBody())
		//       ->setHtml($message->getBody());
		// ;

		// $sendgrid->send($email);




		/*$json_string = array(

	 		'to' => array(
	   			'pedro.silva@codificar.com.br'
	  		),
		  	'category' => 'test_category'
		);*/


		/*--------------------------------------------------------------SENDGRID COM OPTIONS
			$options = array(
			    'turn_off_ssl_verification' => false,
			    'protocol' => 'https',
			    'host' => 'api.sendgrid.com',
			    'endpoint' => '/api/mail.send.json',
			    'port' => null,
			    'url' => null,
			    'raise_exceptions' => false
			);
			$sendgrid = new SendGrid('YOUR_SENDGRID_APIKEY', $options);
		*/




		/*$client->post('https://api.sendgrid.com/api/mail.send.json', [
		    'body' => [
				'api_user'  => $this->username,
			    'api_key'   => $this->key,
			    'x-smtpapi' => json_encode($json_string),
			    'to'        => 'pedro.silva@codificar.com.br',
			    'subject'   => 'test',
			    'html'      => 'testing body',
			    'text'      => 'testing body',
			    'from'      => 'uberclone@codificar.com.br',
			],
		]);*/



		/*$client->post('https://mandrillapp.com/api/1.0/messages/send-raw.json', [
			'body' => [
				'key' => $this->key,
				'raw_message' => (string) $message,
				'async' => false,
			],
		]);*/
	}

	/**
	 * {@inheritdoc}
	 */
	public function registerPlugin(Swift_Events_EventListener $plugin)
	{
		//
	}

	/**
	 * Get the "to" payload field for the API request.
	 *
	 * @param  \Swift_Mime_Message  $message
	 * @return array
	 */
	protected function getTo(Swift_Mime_Message $message)
	{
		$formatted = [];

		$contacts = array_merge(
			(array) $message->getTo(), (array) $message->getCc(), (array) $message->getBcc()
		);

		foreach ($contacts as $address => $display)
		{
			$formatted[] = $display ? $display." <$address>" : $address;
		}

		return implode(',', $formatted);
	}

	/**
	 * Get a new HTTP client instance.
	 *
	 * @return \GuzzleHttp\Client
	 */
	protected function getHttpClient()
	{
		return new Client;
	}

	/**
	 * Get the API key being used by the transport.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Set the API key being used by the transport.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function setKey($key)
	{
		return $this->key = $key;
	}

	/**
	 * Get the user being used by the transport.
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Set the user being used by the transport.
	 *
	 * @param  string  $user
	 * @return void
	 */
	public function setUsername($username)
	{
		return $this->username = $username;
	}

}
