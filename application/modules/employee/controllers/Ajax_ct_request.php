    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_ct_request extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'dtr_model',
            'users_model',
            'users_type_model',
            'request_ot_model',
            'request_change_time_model'
        ]);
        if(!$this->input->is_ajax_request()){ show_404(); }
    }

    public function get_request_data(){
        $data = $this->request_change_time_model->get_row($this->input->post('id'));
        $user = $this->users_model->get_row($data['user_id']);
        $updated_by = $this->users_model->get_row($data['updated_by']);
        $data['user'] = $user['name'];
        $data['updated_by'] = $updated_by['name'];

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['message'] = 'Fetched successfully';
            $response['data'] = $data;
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
            $response['data'] = null;
        }
        echo json_encode($response); 
    }

    public function add_request(){
        date_default_timezone_set('Asia/Manila');
        $account_session = $this->session->userdata('account_session');
        $row = array(
            'user_id' => $account_session['id'],
            'details' => $this->input->post('ctr-reason'),
            'status' => 'pending',
            'updated_by' => $account_session['id'],
            'date_created' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s')
        );

        try {
            $id = $this->request_change_time_model->add($row);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Added Successfully';
                $response['datetime'] = date('Y-m-d H:i:s');
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
        echo json_encode($response); 
    }

    public function approve_request(){
        date_default_timezone_set('Asia/Manila');
        $account_session = $this->session->userdata('account_session');
        $id = $this->input->post('request-id');
        $this->request_change_time_model->update($id, [
            'status' => 'approved',
            'updated_by' => $account_session["id"], 
            'date_updated' => date('Y-m-d h:i:s')
        ]);

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['message'] = 'Updated successfully';
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }
        echo json_encode($response); 
    }

    public function decline_request(){
        date_default_timezone_set('Asia/Manila');
        $account_session = $this->session->userdata('account_session');
        $id = $this->input->post('request-id');

        $this->request_change_time_model->update($id, [
            'status' => 'denied',
            'reason_denied' => $this->input->post('reason-declined'),
            'updated_by' => $account_session["id"], 
            'date_updated' => date('Y-m-d h:i:s')
        ]);

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['message'] = 'Denied successfully';
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }
        echo json_encode($response); 
    }

    public function fetch_ct_request(){

        // echo '<pre>';
        // print_r($this->input->post());
        // exit;

        $ct_info = $this->request_change_time_model->get_row($_POST['id']);
        $user_info = $this->users_model->get_row($ct_info['user_id']);
        $department = $this->users_type_model->get_row($user_info['user_type']);
        $conformer = $this->users_model->get_row($ct_info['updated_by']);

        if($ct_info){
            $response = [
                "response_status" => "success",
                "user_id"         => $ct_info['user_id'],
                "date"            => null,
                "time"            => null,
                "leave_type"      => null,
                "details"         => $ct_info['details'],
                "status"          => $ct_info['status'],
                "reason_denied"   => $ct_info['reason_denied'],
                "date_created"    => $ct_info['date_created'],
                "date_updated"    => $ct_info['date_updated'],
                "user_name"       => $user_info['name'],
                "user_department" => ucwords($department['user_type']),
                "approved_by_name"=> $conformer['name'],
                "approved_by_id"  => $conformer['id'],
            ];
        }else{
            $response = [
                "response_status"  => "error",
                "message" => "An error has occurred: Unable to read salary grade"
            ];
        }

        echo json_encode($response); 
    }

    public function cancel_ct_request(){
        date_default_timezone_set('Asia/Manila');
        $account_session = $this->session->userdata('account_session');
        $id = $this->input->post('request-id');
        $this->request_change_time_model->update($id, [
            'status' => 'cancelled',
            'updated_by' => $account_session["id"], 
            'date_updated' => date('Y-m-d h:i:s')
        ]);

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['message'] = 'Cancelled successfully';
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }
        echo json_encode($response); 
    }

    public function delete_ct_request(){
        $id = $this->input->post('request-id');
        $this->request_change_time_model->delete($id);

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['message'] = 'Deleted successfully';
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }
        echo json_encode($response); 
    }


}