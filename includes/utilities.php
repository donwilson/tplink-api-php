<?php
	/**
	 * Return the first request value by param or cmd name
	 * @param string $request_param Parameter name if script called from HTTP request
	 * @param string $cmd_shortopt Command shortopt letter
	 * @param string $cmd_longopt Command longopt string
	 * @param mixed $default Return this if the request param wasn't sent
	 * @return mixed
	 */
	function get_request_param_or_cmd_option($request_param="", $cmd_shortopt="", $cmd_longopt="", $default=false) {
		if(("" !== $request_param) && isset($_REQUEST['request_param']) && ("" !== ($raw_value = trim($_REQUEST['request_param'])))) {
			return $raw_value;
		}
		
		if("" !== $cmd_shortopt) {
			$cmd_opts = getopt(trim($cmd_shortopt, ":") .":");
			
			if(isset($cmd_opts[ $cmd_shortopt ]) && ("" !== ($raw_value = trim($cmd_opts[ $cmd_shortopt ])))) {
				return $raw_value;
			}
		}
		
		if("" !== $cmd_longopt) {
			$cmd_opts = getopt("", [trim($cmd_longopt, ":") .":"]);
			
			if(isset($cmd_opts[ $cmd_longopt ]) && ("" !== ($raw_value = trim($cmd_opts[ $cmd_longopt ])))) {
				return $raw_value;
			}
		}
		
		return $default;
	}
	
	/**
	 * Kill the page, send JSON content type and data
	 * @param string $status Optional. Type of status
	 * @param mixed $cargo Optional. Included as 'cargo' assoc key in returned array
	 * @param string $message Optional. Define the included 'message' assoc key in returned array
	 * @return void
	 */
	function die_json($status="success", $cargo=false, $message="") {
		$data = array(
			'status' => strtolower(trim($status)),
		);
		
		if(!empty($cargo) && is_array($cargo)) {
			$data['cargo'] = $cargo;
		}
		
		if("" !== ($message = trim($message))) {
			$data['message'] = $message;
		}
		
		header("Content-Type: application/json");
		
		print json_encode($data);
		
		die;
	}