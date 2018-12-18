<?php
	require_once(__DIR__ ."/config.php");
	
	try {
		if(!defined('TPLINK_API_HOST') || !TPLINK_API_HOST) {
			throw new Exception("TP-Link API Host constant not defined");
		}
		
		$force = !empty($_REQUEST['force']);
		
		if(false === ($tplink_token = tplink__get_token($force, true))) {
			throw new Exception("Failed to retrieve functional account token");
		}
		
		$client = new GuzzleHttp\Client([
			'base_uri' => TPLINK_API_HOST,
		]);
		
		$response = $client->request('POST', "/", [
			'verify' => false,
			//'headers' => [
			//	'Content-Type' => "application/json",
			//],
			'query' => [
				'token' => $tplink_token,
			],
			'json' => [
				'method' => "getDeviceList",
			],
		]);
		
		$body = $response->getBody();
		
		$raw_data = $body->getContents();
		
		$data = json_decode($raw_data, true);
		
		if(!empty($data['error_code'])) {
			throw new Exception("API Error (code ". $data['error_code'] .")". (!empty($data['msg'])?": ". $data['msg']:""));
		}
		
		if(empty($data['result']['deviceList'])) {
			throw new Exception("API Error - Returned empty result (". print_r($data, true) .")");
		}
		
		$devices = [];
		
		foreach($data['result']['deviceList'] as $raw_device) {
			/*{
				"fwVer": "1.5.1 Build 171109 Rel.165709",
				"deviceName": "Smart Wi-Fi Plug",
				"status": 1,
				"alias": "Name Given for Plug in App",
				"deviceType": "IOT.SMARTPLUGSWITCH",
				"appServerUrl": "https:\/\/use1-wap.tplinkcloud.com",
				"deviceModel": "HS100(US)",
				"deviceMac": "12_CHAR_HEX_DUMP",
				"role": 0,
				"isSameRegion": true,
				"hwId": "32_CHAR_HEX_DUMP",
				"fwId": "32_CHAR_HEX_DUMP",
				"oemId": "32_CHAR_HEX_DUMP",
				"deviceId": "40_CHAR_HEX_DUMP",
				"deviceHwVer": "2.0"
			}*/
			
			$device = [
				'id' => (isset($raw_device['deviceId'])?$raw_device['deviceId']:false),
				'name' => (isset($raw_device['alias'])?$raw_device['alias']:false),
				'status' => (isset($raw_device['status'])?$raw_device['status']:false),
				'api_host' => (isset($raw_device['appServerUrl'])?$raw_device['appServerUrl']:false),
				'role' => (isset($raw_device['role'])?$raw_device['role']:false),
				'hardware' => [
					'name' => (isset($raw_device['deviceName'])?$raw_device['deviceName']:false),
					'model' => (isset($raw_device['deviceModel'])?$raw_device['deviceModel']:false),
					'type' => (isset($raw_device['deviceType'])?$raw_device['deviceType']:false),
					'mac' => (isset($raw_device['deviceMac'])?$raw_device['deviceMac']:false),
					'hwId' => (isset($raw_device['hwId'])?$raw_device['hwId']:false),
					'hwVer' => (isset($raw_device['deviceHwVer'])?$raw_device['deviceHwVer']:false),
					'fwId' => (isset($raw_device['fwId'])?$raw_device['fwId']:false),
					'fwVer' => (isset($raw_device['fwVer'])?$raw_device['fwVer']:false),
					'oemId' => (isset($raw_device['oemId'])?$raw_device['oemId']:false),
				],
			];
			
			$devices[] = $device;
		}
		
		die_json('success', [
			'token' => $tplink_token,
			'response' => $devices,
		], "Command completed successfully");
	} catch(Exception $e) {
		die_json('error', [], $e->getMessage());
	}