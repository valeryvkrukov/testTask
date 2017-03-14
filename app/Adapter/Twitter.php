<?php 
namespace Test\App\Adapter;

use Test\App\Adapter;

class Twitter extends Adapter
{
	const API_URL = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	protected $accessToken;
	protected $accessTokenSecret;
	protected $consumerKey;
	protected $consumerSecret;
	
	protected function loadConfig()
	{
		parent::loadConfig();
		if (isset($this->config['twitter'])) {
			foreach (['accessToken', 'accessTokenSecret', 'consumerKey', 'consumerSecret'] as $field) {
				if (isset($this->config['twitter'][$field])) {
					$this->$field = $this->config['twitter'][$field];
				} else {
					throw new \Exception('Twitter adapter error: option "' . $field . '" is required');
				}
			}
		} else {
			throw new \Exception('Twitter adapter is not configured');
		}
	}
	
	public function loadResource($config)
	{
		$settings = [
			'count' => 25,
			'include_rts' => 0,
			'screen_name' => $config['screen_name'],
			'oauth_consumer_key' => $this->consumerKey,
			'oauth_token' => $this->accessToken,
			'oauth_timestamp' => time(),
			'oauth_nonce' => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_version' => '1.0',
		];
		$baseInfo = $this->buildBaseString($settings);
		$compositeKey = rawurlencode($this->consumerSecret) . '&' . rawurlencode($this->accessTokenSecret);
		$signature = base64_encode(hash_hmac('sha1', $baseInfo, $compositeKey, true));
		$settings['oauth_signature'] = $signature;
		try {
			$c = curl_init();
			curl_setopt_array($c, [
				CURLOPT_HTTPHEADER => [$this->buildAuthorizationHeader($settings), 'Expect:'],
				CURLOPT_HEADER => false,
				CURLOPT_URL => self::API_URL . '?screen_name=' . $settings['screen_name'] . '&count=' . $settings['count'] . '&include_rts=' . $settings['include_rts'],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
			]);
			$data = curl_exec($c);
			curl_close($c);
			$data = json_decode($data);
		} catch (\Exception $e) {
			return [
				'code' => $e->getCode(),
				'message' => $e->getMessage(),
				'status' => 'error',
			];
		}
		$this->setMapping([
			'id' => 'id_str',
			'text' => 'text',
			'date' => 'created_at',
		]);
		return $this->normalizeFeed($data);
	}
	
	protected function buildBaseString($params) {
		$values = [];
		ksort($params);
		foreach ($params as $key => $value) {
			$values[] = $key . '=' . rawurlencode($value);
		}
		return 'GET&' . rawurlencode(self::API_URL) . '&' . rawurlencode(implode('&', $values));
	}
	
	protected function buildAuthorizationHeader($params) {
		$values = [];
		foreach ($params as $key => $value) {
			$values[] = $key . '="' . rawurlencode($value) . '"';
		}
		return 'Authorization: OAuth ' . implode(', ', $values);
	}
}