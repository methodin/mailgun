<?php
namespace Methodin;
/* 
 * Simple Mailgun integration
 */
class Mailgun {
	/*
	 * @var string The api key from mailgun
	 */
	protected $api_key 	= '';
	/*
	 * @var string The domain name
	 */
	protected $domain	= '';

	/*
	 * @param string $apiKey The api key from mailgun account
	 * @param string $domain The domain name
	 */
	public function __construct($apiKey, $domain) {
		$this->apiKey 	= $apiKey;
		$this->domain	= $domain;
	}

	/*
	 * Sends a new message to the specific user(s)
	 * @param array $to The list of recipients
	 * @param string $from The from email address
	 * @param string $subject The email subject
	 * @param string $body The content of the email
	 */
	public function sendMessage(array $to, $from, $subject, $body) {
		$this->post('messages', array(
			'to'		=> $to,
			'from'		=> $from,
			'subject'	=> $subject,
			'text'		=> $body
		));
	}

	/*
	 * Performs a post request to the mailgun api
	 * @param string $url The api call to make
	 * @param array $params The post fields to send to mailgun
	 */
	protected function post($url, array $params) {
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
		curl_setopt($ch, CURLOPT_URL, 
			sprintf('https://api.mailgun.net/v2/%s/%s', $this->domain, $url));
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_USERPWD, "api:{$this->apiKey}");

		$result 	= curl_exec($ch);
		$http_code 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code != 200)
			throw new \Exception("Mailgun Error: " . strip_tags($result));
	}
}
