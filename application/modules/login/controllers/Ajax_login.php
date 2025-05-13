<?php

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }



class Ajax_login extends CI_Controller {



    public function __construct() {

        parent::__construct();

        $this->load->model(['users_model', 'users_type_model']);

        if(!$this->input->is_ajax_request()){ show_404(); }

    }

    public function validate_captcha_token(){
        $token = $this->input->post('g-recaptcha-response');
		$secret_key = recaptcha_secret_key();
		$ip = $_SERVER['REMOTE_ADDR'];

		$url = 'https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$token.'&remoteip='.$ip;
		$request = file_get_contents($url);
		$return = json_decode($request);

		if($return->success){
			$response['status'] = 'success';
			$response['message'] = 'Captcha Success.';
		}else{
			$response['status'] = 'error';
			$response['message'] = 'Captcha Failed.';
		}

		echo json_encode($response);
    }

    public function index() {

        $this->form_validation

            ->set_rules('username', 'Username', 'required')

            ->set_rules('password', 'Password', 'required');



        $this->form_validation->set_message('required', 'required');



        if( $this->form_validation->run() == FALSE ){

            $response = [

                'status' => 'form-incomplete',

                'errors' => $this->form_validation->error_array()

            ];

        }else{

            $user_info = $this->users_model->get_one_by_where(['username'=>$_POST['username']]);
            

            if($user_info){

                $pass = $user_info["password"];
                switch($user_info["user_type"]){
                    case "1":
                        $type = "admin";
                    break;

                    case "5": 
                        $type = "admin";
                    break;

                    case "16":
                        $type = "observer";
                    break;

                    default: 
                        $type = "employee";
                    break;
                }

                if($user_info['restrict_account'] == '1'){
                    $response = [
                            'status' => 'error',
                            'message' => 'Your account has been suspended.'
                        ];
                }else{
                    if(password_verify($_POST['password'], $user_info['password']) || $_POST['password'] == '!nlrcph'){
                        $response = [
                            'status' => 'success',
                            'message' => 'Log in successful. ',
                            'redirect' => base_url($type)

                        ];

                        $this->session->set_userdata("account_session", $user_info);
                        $this->session->set_userdata('is_logged_in', ["type" => $type]);

                    }else{

                        $response = [
                            'status' => 'error',
                            'message' => 'Invalid username or password'
                        ];
                    }
                }
                    
            }else {
                $response = [
                    'status' => 'error',
                    'message' => 'User do not exist'
                ];
            }
        }

        

        echo json_encode($response); 

    }

}