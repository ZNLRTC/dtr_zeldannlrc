    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_overtime extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'dtr_model',
            'overtime_logs_model',
            'request_leave_model',
            'users_model',
            'users_type_model'
        ]);
        if(!$this->input->is_ajax_request()){ show_404(); }
    }

    public function save_overtime(){
        $user_id = $this->input->post('user-id');
        $workbase = $this->input->post('workbase');
        $shift = $this->input->post('shift');
        $action = $this->input->post('action');

        $account_session = $this->session->userdata('account_session');
        $timezone = new DateTimeZone('Asia/Manila');
        $now = new DateTime('now', $timezone);
        $final_date = $now->format('Y-m-d');
        $final_time = $now->format('H:i');
        $type = $shift == 'holiday' ? 'holiday' : 'regular';

        // echo '<pre>';
        // print_r($this->input->post());
        // exit;

        
        try{
            switch($action){
                case 'ot-time-in':
                    $row = array(
                        'user_id'           => $user_id,
                        'updated_by'        => $account_session['id'],
                        'date'              => $final_date,
                        'type'              => $type,
                        'shift'             => $shift,
                        'workbase'          => $workbase,
                        'time_in_work_base'  => $workbase,
                        'time_in'           => $final_time
                    );
                break; 

                case 'ot-time-out':
                    $row = array(
                        'time_out' => $final_time
                    );
                break;
            }
            
            $dtr_checker = $this->overtime_logs_model->get_one_by_where(['date' => $final_date, 'shift' => $shift, 'user_id' => $user_id]);
            if(!$dtr_checker){
                $id = $this->overtime_logs_model->add($row);
            }

            if($this->db->trans_status()){
                $this->db->trans_rollback();
                $response['status'] = 'success';
                $response['message'] = 'Added successfully';
            }else{
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = $this->db->error();
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        
    }

    public function save_overtime_to(){
        $report = $this->input->post('report');
        $id = $this->input->post('id');
        
        $timezone = new DateTimeZone('Asia/Manila');
        $now = new DateTime('now', $timezone);
        $final_date = $now->format('Y-m-d');
        $final_time = $now->format('H:i');

        $row = array(
            'time_out' => $final_time,
            'eod' => $report,
            'date_updated' => $now->format('Y-m-d H:i:s')
        );

        try{
            $this->overtime_logs_model->update($id, $row);

            if($this->db->trans_status()){
                $this->db->trans_rollback();
                $response['status'] = 'success';
                $response['message'] = 'Updated successfully';
            }else{
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = $this->db->error();
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    public function fetch_ot_info(){
        $id = $this->input->post('request-id');
        $data = $this->overtime_logs_model->get_row($id);

        $response['status'] = 'success';
        $response['task'] = $data['eod'];

        echo json_encode($response);
    }

    public function fetch_ot_denied(){

        $id = $this->input->post('id');
        $data = $this->overtime_logs_model->get_row($id);
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

        $rot_info = $this->overtime_logs_model->get_row($_POST['id']);
        $user_info = $this->users_model->get_row($rot_info['user_id']);
        $department = $this->users_type_model->get_row($user_info['user_type']);
        $conformer = $this->users_model->get_row($rot_info['updated_by']);

        // echo '<pre>';
        // print_r([$rot_info, $dtr]);
        // exit;

        if($rot_info){
            $response = [
                "response_status" => "success",
                "user_id"         => $rot_info['user_id'],
                "date"            => $rot_info['date'],
                "time"            => $rot_info['time_in'] .'-'. $rot_info['time_out'],
                "leave_type"      => $rot_info['type'],
                "details"         => $rot_info['eod'],
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

    public function approve_ot_request(){
        try {
            $id = $this->input->post('request-id');
            $ot_data = $this->overtime_logs_model->get_row($id);
            $account_session = $this->session->userdata('account_session');

            $this->overtime_logs_model->update($id, ['updated_by' => $account_session["id"], 'status' => 'approved']);
  
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

            $this->overtime_logs_model->update($id, $row);
                
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
            $ot_data = $this->overtime_logs_model->get_row($id);
            $account_session = $this->session->userdata('account_session');

            $this->overtime_logs_model->update($id, ['updated_by' => $account_session["id"], 'status' => 'cancelled']);
                
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
            $this->overtime_logs_model->delete($id);
                
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

    public function is_paid(){
        $id = $this->input->post('id');
        $is_paid = $this->input->post('is-paid');

        $row = array(
            'paid' => $is_paid,
            'date_updated' => date('Y-m-d H:i:s')
        );

        try {
            $this->overtime_logs_model->update($id, $row);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['row'] = $row;
                $response['message'] = 'Updated Successfully';
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

    public function break_in_out(){
        date_default_timezone_set('Asia/Manila');
        $user_id = $this->input->post('user-id');
        $id = $this->input->post('ot-id');
        $action = $this->input->post('action');
        $workbase = $this->input->post('workbase');

        switch($action){
            case 'ot-break-in':
                $row = array(
                    'break_in' => date('H:i'),
                    'date_updated' => date('Y-m-d H:i:s')
                );
            break;

            case 'ot-break-out':
                $ot_info = $this->overtime_logs_model->get_row($id);
                if($ot_info['workbase'] == $workbase){
                    $workbase_main = $workbase;
                }else{
                    $workbase_main = $ot_info['workbase'] .'/'. $workbase;
                }
                $row = array(
                    'workbase' => $workbase_main,
                    'break_out' => date('H:i'),
                    'break_out_work_base' => $workbase,
                    'date_updated' => date('Y-m-d H:i:s')
                );
            break;
        }

        try {
            $this->overtime_logs_model->update($id, $row);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Updated Successfully';
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