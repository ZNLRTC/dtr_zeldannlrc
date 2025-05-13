<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Reset_password extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['users_model', 'users_type_model']);
        if($this->session->has_userdata('is_logged_in')){
            $type = $this->session->userdata('is_logged_in');
            header("Location: ".base_url($type['type']));
        }
    }

    public function index() {
        if(!isset($_GET['id']) || !isset($_GET['token'])){
            show_404();
        }

        $id = $_GET['id'];
        $token = $_GET['token'];
        $user = $this->users_model->get_row($id);
        if(!$user || sha1((string) $user['date_updated']) !== $token){
            show_404();
        }

        $data['id'] = $id;

        $this->template
            ->title('Reset Password')
            ->set_layout('main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/landing_main/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/landing_main/js/main.js') . '"></script>')
            ->build('index_reset_password', $data);
    }
}