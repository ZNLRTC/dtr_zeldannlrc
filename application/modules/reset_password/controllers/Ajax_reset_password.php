<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_reset_password extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['users_model', 'users_type_model']);
        if(!$this->input->is_ajax_request()){ show_404(); }
    }
    
    public function index(){
        $this->form_validation
            ->set_rules('new-password', 'new-password', 'required|min_length[8]|callback_validate_confirm_password')
            ->set_rules('confirm-password', 'confirm-password', 'required')
            ->set_message('required', 'required')
            ->set_message('min_length', 'Atleast 8 characters in length');

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                $data = [
                    'password'     => password_hash($_POST['new-password'], PASSWORD_BCRYPT, ['cost' => 12]),
                    'date_updated' => date('Y-m-d h:i:s')
                ];

                $this->users_model->update($_POST['user_id'],$data);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = 'Password Reset Successfully';
                }else{
                    $this->db->trans_rollback();
                    $response['status'] = 'error';
                    $response['message'] = $this->db->error();
                }
            }catch (Exception $e) {
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
        }
        echo json_encode($response); 
    }

    public function validate_confirm_password(){
        if($_POST['new-password'] !== $_POST['confirm-password']){
            $this->form_validation->set_message('validate_confirm_password','Confirm password mismatch');
            return false;
        }
        return true;
    }

}