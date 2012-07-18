<?php

/**
* JSON Error Handler
*
* 
* Display error messages using json format
*
* @access	public
* @return	void
*/
if ( ! function_exists('show_json_error'))
{
	function show_json_error($message, $status_code = 500, $information = array())
	{
		set_status_header($status_code);
        header('Content-type: application/json');
        
		$error = new stdClass();
		$error->code = $status_code;
		$error->error = $message;
		
		foreach($information as $key=>$val) {
			$error->$key = $val;
		}
		
		echo json_encode($error);
		exit();
	}
}