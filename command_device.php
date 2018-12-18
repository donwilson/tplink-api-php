<?php
	require_once(__DIR__ ."/config.php");
	
	function get_known_device_action_request($device_id, $device_action) {
		switch(strtolower(trim($device_action))) {
			case 'turn_on':
				return [
					'method' => "passthrough",
					'params' => [
						'deviceId' => $device_id,
						'requestData' => json_encode([
							'system' => [
								'set_relay_state' => [
									'state' => 1,
								],
							],
						]),
					],
				];
			break;
			
			case 'turn_off':
				return [
					'method' => "passthrough",
					'params' => [
						'deviceId' => $device_id,
						'requestData' => json_encode([
							'system' => [
								'set_relay_state' => [
									'state' => 0,
								],
							],
						]),
					],
				];
			break;
		}
		
		return false;
	}
	
	try {
		if(!defined('TPLINK_API_HOST') || !TPLINK_API_HOST) {
			throw new Exception("TP-Link API Host constant not defined");
		}
		
		// device ID
		if(false === ($device_id = get_request_param_or_cmd_option("device", "d", "device", false))) {
			throw new Exception("Device ID not specified");
		}
		
		// action
		if(false === ($device_action = get_request_param_or_cmd_option("action", "a", "action", false))) {
			throw new Exception("No valid device action specified");
		}
		
		if(false === ($tplink_command_request_body = get_known_device_action_request($device_id, $device_action))) {
			throw new Exception("Device action specified is invalid");
		}
		
		$force = !empty($_REQUEST['force']);
		
		if(false === ($tplink_token = tplink__get_token($force))) {
			throw new Exception("Failed to retrieve functional account token");
		}
		
		$client = new GuzzleHttp\Client([
			'base_uri' => "https://use1-wap.tplinkcloud.com/",   // @TMP
		]);
		
		$response = $client->request('POST', "/", [
			'verify' => false,
			'query' => [
				'token' => $tplink_token,
			],
			'json' => $tplink_command_request_body,
		]);
		
		$body = $response->getBody();
		$raw_data = $body->getContents();
		$data = json_decode($raw_data, true);
		
		if(!empty($data['error_code'])) {
			throw new Exception("API Error (code ". $data['error_code'] .")". (!empty($data['msg'])?": ". $data['msg']:""));
		}
		
		die_json('success', [
			'device' => $device_id,
			'action' => $device_action,
		], "Command completed successfully");
	} catch(Exception $e) {
		die_json('error', [], $e->getMessage());
	}