<?php

class TelegramBot
{
	private $token;
	private $chatID;

	public function setToken($token)
	{
		$this->token = $token;
	}

	public function setChatID($chatID)
	{
		$this->chatID = $chatID;
	}

	public function send($text)
	{
		$url = 'https://api.telegram.org/bot' . $this->token . '/sendmessage';
		$data = http_build_query(
			[
				
				
				'disable_web_page_preview' => 'true',
				'parse_mode' => 'HTML',
				'chat_id' => $this->chatID,
				'text'    => $text,
			],
			'',
			'&'
		);





/*		$curl = curl_init();
		curl_setopt_array(
			$curl,
			[
				CURLOPT_URL            => $url,
				CURLOPT_HTTPHEADER     => ['Content-type: application/x-www-form-urlencoded\r\n'],
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_POST           => 1,
				CURLOPT_POSTFIELDS     => $data,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_HTTPPROXYTUNNEL => 1,
				CURLOPT_PROXY => $proxy['host'],
				CURLOPT_PROXYUSERPWD => $proxy['user'].':'.$proxy['pass'],
				CURLOPT_PROXYTYPE => "CURLPROXY_SOCKS5",
			]
		);
		$result = curl_exec($curl);
		var_dump($result);
		curl_close($curl);*/


		$cmd = "curl -k \"".$url."?".$data."\"";

		$result = exec($cmd, $out, $retval);
	
		return $result;
	}
}
