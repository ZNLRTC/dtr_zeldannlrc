<?php

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }



class Ajax_forgot_password extends CI_Controller {



    public function __construct() {

        parent::__construct();

        $this->load->model(['users_model', 'users_type_model']);
        //$this->load->library(['phpmailer/phpmailer']);

        if(!$this->input->is_ajax_request()){ show_404(); }

    }



    public function index(){

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        $this->form_validation

            ->set_message('required', 'required')

            ->set_message('valid_email', 'invalid email');



        if( $this->form_validation->run() == FALSE ){

            $response = [

                'status' => 'form-incomplete',

                'errors' => $this->form_validation->error_array()

            ];

        }else{

            $user = $this->users_model->get_one_by_where(['email'=>$_POST['email']]);

            if(!$user){
                $response = array(
                    'status' => 'error',
                    'message' => 'Email does not exist on the system.'
                );
            }else{

                $from = 'support@zeldannlrc.com';
                $config = [
                    'protocol' => 'sendmail',
                    'mailpath' => '/usr/sbin/sendmail',
                    'mailtype' => 'html',
                    'charset'  => 'iso-8859-1',
                    'wordwrap' => TRUE
                ];

                // $config = array(
                //     'protocol' => 'smtp',
                //     'smtp_host' => 'smtp.gmail.com',
                //     'smtp_port' => 587,
                //     'smtp_user' => 'znlrtc@gmail.com', // Your Gmail email address
                //     'smtp_pass' => 'jvtgnuncxrmajcri',   // Your Gmail password
                //     'mailtype' => 'html',
                //     'charset'  => 'iso-8859-1',
                //     'wordwrap' => TRUE,
                //     'smtp_crypto' => 'tls', // Enable TLS encryption
                //     'newline' => "\r\n"    // Use double quotes for \r\n
                // );

                $message = '
                    <p><b>'.date("d/m/Y").'</b></p>
                    <p>Hi '.ucwords($user['name']).',
                    <p>You have requested for password change with your account.</p>
                    <p>Click <a href="' . base_url() . 'reset_password?id=' . $user["id"] . '&token=' . urlencode(sha1((string) $user["date_updated"])) . '" target="_blank">here</a> to upadate your password.</p><br><br>
                    <p><b>Zeldan Nordic Languages Review Center</b></p>
                ';

                $this->load->library('email');
                $this->email->initialize($config);

                $this->email->from($from, 'NLRC IT Support');
                $this->email->to($user['email']);
                $this->email->bcc('miguelluciano202201@yahoo.com');
                $this->email->subject('RE: Account Recovery');
                $this->email->message($message);

                if ($this->email->send()) {
                    $response['status'] = 'success';
				    $response['message'] = 'Email successfully sent.';
                } else {
                    $response['status'] = 'error';
				    $response['message'] = 'Error: ' . $this->email->print_debugger();
                }
            }

        }

        echo json_encode($response); 

    }

}