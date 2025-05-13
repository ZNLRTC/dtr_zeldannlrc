<?php

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }



class Ajax_send_registration_link extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['send_registration_link_emails_model']);
    }



    public function index(){

        $emails = $this->send_registration_link_emails_model->get_all_with_limit(50);

        foreach($emails as $email){
            $clean_arr = explode(' ', $email['email']);
            $clean_email = implode('', $clean_arr);
            $status = $this->send_email($clean_email);
            $this->send_registration_link_emails_model->update($email['id'], ['status' => $status['status']]);
            echo $status['message'];
        }
    }

    function send_email($email){
        $from = 'support@zeldannlrc.com';
        //$from = 'pocyoymiguel@gmail.com';
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
        //     'smtp_user' => 'pocyoymiguel@gmail.com', // Your Gmail email address
        //     'smtp_pass' => 'fqvfimfqlpwrsraw',   // Your Gmail password
        //     'mailtype' => 'html',
        //     'charset'  => 'iso-8859-1',
        //     'wordwrap' => TRUE,
        //     'smtp_crypto' => 'tls', // Enable TLS encryption
        //     'newline' => "\r\n"    // Use double quotes for \r\n
        // );

        $message = "
            <p>Hi there!</p>
            <p>Exciting news! As Nordic Language Review Training Center keeps growing and getting even better, we're taking a step to streamline our Training processes. This helps us ensure we have all the information we need to give each applicant a thorough review.</p>

            <p>Basically, as we are reviewing your profiles, we have noticed that there are information missing. So, to help us gather this information quickly and securely, we'd love for you to complete our brief Training Registration. You can access it easily by clicking on this <a href='https://nlrc.ph/guest/register?email=".$email."'>link</a>.</p>

            <p>Don't worry, any information you provide is completely confidential and will only be used internally to verify your application. We take your privacy seriously!</p>

            <p>Ready to take the next step in your language learning adventure? Let's get started!</p>

            <p><b>Best Regards</b>,<br>ZNLRTC Support</p>
        ";

        $this->load->library('email');
        $this->email->initialize($config);

        $this->email->from($from, 'no-reply@zeldannlrc.com');
        $this->email->to($email);
        //$this->email->to('znlrtc@gmail.com');
        //$this->email->bcc('miguelluciano202201@yahoo.com');
        $this->email->bcc('znlrtc@gmail.com');
        $this->email->subject('RE: Account Registration');
        $this->email->message($message);

        if ($this->email->send()) {
            $response['status'] = 'success';
            $response['message'] = 'Email successfully sent to '.$email;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error: ' . $this->email->print_debugger();
        }

        return($response); 
    }

}