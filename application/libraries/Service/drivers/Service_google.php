<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
* @license AGPLv3
* @author Jens Segers <jens at iRail.be>
* @author Hannes Van De Vreken <hannes at iRail.be>
* @author Lennart Martens <lennart at iRail.be>
*/

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

//me
class Service_facebook extends CI_Driver {

	private $ci ;

	function __construct(){
		parent::__construct('google');
		$this->service_name = 'google' ;
		$this->ci->load->library('OAuth2_client', array('service' => $this->service_name), $this->service_name);
	}

	function user_id() {
		//Getting user_id and verifying the token -> see 
		//https://developers.google.com/accounts/docs/OAuth2UserAgent#validatetoken
		$json = $this->ci->{$this->service_name}->api('oauth2/v1/tokeninfo');
		$result = json_decode($json);
		return $result->user_id;
	}
}