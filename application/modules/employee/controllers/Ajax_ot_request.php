    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_ot_request extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'dtr_model',
            'users_model',
            'users_type_model',
            'request_ot_model'
        ]);
        if(!$this->input->is_ajax_request()){ show_404(); }
    }

    public function ot_au_request(){
        date_default_timezone_set('Asia/Manila');
        try {
            if(isset($_POST['user_id'])){//add
                $data = [
                    'user_id'      => $_POST['user_id'],
                    'task'         => $_POST['task'],
                    'type'         => $_POST['ot-type'],
                    'date'         => $_POST['date'],
                    'time'         => $_POST['start'].'-'.$_POST['end'],
                    'date_created' => date('Y-m-d H:i:s'),
                    'date_updated' => date('Y-m-d H:i:s')
                ];

                if($_POST["ot-type"] == "Document Checking"): $data['status']   = NULL; endif;
                $add = $this->request_ot_model->add($data);

                $task        = explode(PHP_EOL, $_POST['task']);
                $return_task = "";
                for($a=0;$a<count($task);$a++){ $return_task .= "&#x2022; ".$task[$a]."<br>"; }

                $response = [
                    'message' => "Request Sent Successfully",
                    'otr_id'  => $add,
                    'date'    => date( "M d, Y (D)",strtotime($_POST['date'])),
                    'task'    => $return_task,
                    'time'    => ($_POST['ot-type'] == "Document Checking") ? date( "h:i a",strtotime($_POST['start']))." - ".date( "h:i a",strtotime($_POST['end'])) : ""
                ];            
            }else{//update
                
                if(isset($_POST['ot-type']) && $_POST['ot-type'] == "Document Checking"):
                    $data = [
                        'task'         => $_POST['task'],
                        'type'         => $_POST['ot-type'],
                        'date'         => $_POST['date'],
                        'time'         => $_POST['start'].'-'.$_POST['end'],
                        'status'       => NULL,
                        'date_updated' => date('Y-m-d h:i:s')
                    ];
                elseif(isset($_POST['ot-type']) && $_POST['ot-type'] == "Regular Overtime"):
                    $data = [
                        'task'         => $_POST['task'],
                        'type'         => $_POST['ot-type'],
                        'date'         => $_POST['date'],
                        'time'         => NULL,
                        'status'       => "pending",
                        'date_updated' => date('Y-m-d h:i:s')
                    ];
                endif;

                $this->request_ot_model->update($_POST['otr_id'],$data);

                $task_input = explode(PHP_EOL, $_POST['task']);
                $task = "";
                for($a=0;$a<count($task_input);$a++){
                    $task .= "&#x2022; ".$task_input[$a]."<br>";
                }
                $read_update = $this->request_ot_model->get_row($_POST['otr_id']);

                $response['message']   = "Request Updated Successfully";
                $response['date']      = date( "M d, Y (D)",strtotime( $_POST['date']));
                $response['task']      = $task;
                $response['ot_status'] = $read_update['status'];
                $response['time']      = ($_POST['ot-type'] == "Document Checking") ? date( "h:i a",strtotime($_POST['start']))." - ".date( "h:i a",strtotime($_POST['end'])) : "";
            }

            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
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

    public function delete_request(){
        try {
            $this->request_ot_model->delete($_POST['otr_id']);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Request Deleted Successfully';
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

    //Added Migs
    public function fetch_ot_info(){
        $id = $this->input->post('request-id');
        $data = $this->request_ot_model->get_row($id);

        $response['status'] = 'success';
        $response['task'] = $data['task'];

        echo json_encode($response);
    }

    public function approve_ot_request(){
        try {
            $id = $this->input->post('request-id');
            $ot_data = $this->request_ot_model->get_row($id);
            $account_session = $this->session->userdata('account_session');

            $this->dtr_model->update($ot_data['dtr_id'], ['overtime' => $ot_data['time']]);
            $this->request_ot_model->update($id, ['updated_by' => $account_session["id"], 'status' => 'approved']);
            
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Approved Successfully';
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

    public function fetch_ot_data(){

        $id = $this->input->post('id');
        $data = $this->request_ot_model->get_row($id);
        $user = $this->users_model->get_row($data['updated_by']);

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['reason_denied'] = $data['reason_denied'];
            $response['denied_by'] = $user['name'];
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }

        echo json_encode($response);
    }

    public function fetch_leave_request(){

        $rot_info = $this->request_ot_model->get_row($_POST['id']);
        $dtr = $this->dtr_model->get_row($rot_info['dtr_id']);
        $user_info = $this->users_model->get_row($dtr['user_id']);
        $department = $this->users_type_model->get_row($user_info['user_type']);
        $conformer = $this->users_model->get_row($rot_info['updated_by']);

        // echo '<pre>';
        // print_r([$rot_info, $dtr]);
        // exit;

        if($rot_info){
            $response = [
                "response_status" => "success",
                "user_id"         => $dtr['user_id'],
                "date"            => $dtr['date'],
                "time"            => $rot_info['time'],
                "leave_type"      => $rot_info['type'],
                "details"         => $rot_info['task'],
                "status"          => $rot_info['status'],
                "reason_denied"   => $rot_info['reason_denied'],
                "date_created"    => $rot_info['date_created'],
                "date_updated"    => $rot_info['date_updated'],
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

    public function decline_request(){
        
        try {
            $id = $this->input->post('request-id');
            $reason = $this->input->post('reason-declined');
            $account_session = $this->session->userdata('account_session');

            $row = array(
                'updated_by'        => $account_session['id'],
                'status'            => 'denied',
                'reason_denied'     => $reason,
                'date_updated'      => date('Y-m-d h:i:s')
            );

            $this->request_ot_model->update($id, $row);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Denied Successfully';
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

    public function cancel_ot_request(){
        try {
            $id = $this->input->post('request-id');
            $ot_data = $this->request_ot_model->get_row($id);
            $account_session = $this->session->userdata('account_session');

            $this->dtr_model->update($ot_data['dtr_id'], ['overtime' => $ot_data['time']]);
            $this->request_ot_model->update($id, ['updated_by' => $account_session["id"], 'status' => 'cancelled']);
            
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Cancelled Successfully';
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

    public function delete_ot_request(){
        
        try {
            $id = $this->input->post('request-id');
            $this->request_ot_model->delete($id);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Deleted Successfully';
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
}