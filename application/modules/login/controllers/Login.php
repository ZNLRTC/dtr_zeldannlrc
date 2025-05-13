<?php

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }



class Login extends CI_Controller {



    public function __construct() {

        parent::__construct();

        $this->load->library('session');

        $this->load->model([
            'users_model', 
            'users_type_model',
            'dtr_model'
        ]);
        
        if($this->session->has_userdata('is_logged_in')){
            $type = $this->session->userdata('is_logged_in');
            header("Location: ".base_url($type['type']));
        }

    }



    public function index() {

        $this->template

            ->title('Login')

            ->set_layout('main')

            ->prepend_metadata('<link href="' . versionAsset('assets_module/landing_main/css/main.css') . '" rel="stylesheet" type="text/css">')

            ->append_metadata('<script src="' . versionAsset('assets_module/landing_main/js/main.js') . '"></script>')

            ->build('index_login');

    }

    public function logout_all(){
        date_default_timezone_set('Asia/Manila');
     
        $all_active = $this->dtr_model->get_all_by_where(['time_out' => NULL]);
        $eod = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> System automatic logged out at '.date('h:i A').'</span>';
        $not_updated = [];
        $row = array(
            'time_out' => date('H:i'),
            'end_of_day' => $eod,
            'date_updated' => date('Y-m-d H:i:s')
        );

        foreach($all_active as $data){
            $this->dtr_model->update($data['id'], $row);
        }

        $this->session->sess_destroy();
        redirect('login');
    }
}