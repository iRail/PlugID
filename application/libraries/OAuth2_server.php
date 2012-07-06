<?php

class OAuth2_server {
	
    function new_client( $data ){
        // get user_id from auth lib?
        $this->load->model('client_model');
        $this->client_model->create( $data->name, $data->callback , $user_id );
        // geeft ofwel client object,
        //       ofwel client_id & client_secret,
    }
    
    function authorize( $client_id ){
        //return $code ;
    }

    function acces_token( $client_id, $code ){
        //return $code ;
    }
}
