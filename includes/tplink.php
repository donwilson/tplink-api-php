<?php
	/**
	 * Get an active TP-Link access token if possible, otherwise return false
	 * @param bool $force Optional. Set to true to force retrieval of a new token
	 * @param bool $throw_exception Optional. Set to true to throw exception on error instead of returning false
	 * @return string|false
	 */
	function tplink__get_token($force=false, $throw_exception=false) {
		try {
			if(!defined('TPLINK_API_HOST') || !TPLINK_API_HOST) {
				throw new Exception("TP-Link API Host constant not defined");
			}
			
			if(!defined('TPLINK_TERMID') || !TPLINK_TERMID) {
				throw new Exception("TP-Link Term ID constant not defined");
			}
			
			if(!defined('TPLINK_CLOUD_USERNAME') || !TPLINK_CLOUD_USERNAME) {
				throw new Exception("TP-Link account username constant not defined");
			}
			
			if(!defined('TPLINK_CLOUD_PASSWORD') || !TPLINK_CLOUD_PASSWORD) {
				throw new Exception("TP-Link account password constant not defined");
			}
			
			$client = new GuzzleHttp\Client([
				'base_uri' => TPLINK_API_HOST,
			]);
			
			$response = $client->request('POST', "/", [
				'verify' => false,
				//'headers' => [
				//	'Content-Type' => "application/json",
				//],
				'json' => [
					'method' => "login",
					'params' => [
						'appType' => "Kasa_Android",
						'cloudUserName' => TPLINK_CLOUD_USERNAME,
						'cloudPassword' => TPLINK_CLOUD_PASSWORD,
						'terminalUUID' => TPLINK_TERMID,
					],
				],
			]);
			
			if("200" != $response->getStatusCode()) {
				throw new Exception("Status code not 200: ". $response->getStatusCode() ." (reason: ". $response->getReasonPhrase() ."; body: ". $response->getBody() .")");
			}
			
			$body = $response->getBody();
			
			$raw_data = $body->getContents();
			
			$data = json_decode($raw_data, true);
			
			if(!empty($data['error_code'])) {
				throw new Exception("API Error (code ". $data['error_code'] .")". (!empty($data['msg'])?": ". $data['msg']:""));
			}
			
			if(empty($data['result']['token'])) {
				throw new Exception("No token provided: ". print_r($data, true));
			}
			
			// @TODO record 'regTime', 'email', 'token' for use of $force=false above
			
			return $data['result']['token'];
		} catch(Exception $e) {
			// do nothing with exception atm
			
			if($throw_exception) {
				throw $e;
			}
			
			return false;
		}
	}