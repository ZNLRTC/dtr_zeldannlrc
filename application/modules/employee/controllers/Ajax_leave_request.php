    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_leave_request extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'users_model',
            'users_type_model',
            'user_temporary_schedule_model',
            'user_schedule_model',
            'request_leave_model',
        ]);
        if(!$this->input->is_ajax_request()){ show_404(); }
    }


    public function fetch_leave_request_by_id(){
        $id = $this->input->post('leave_id');

        echo '<pre>';
        print_r($id);
        exit;
    }

    public function fetch_leave_request(){
        $id = $this->input->post('leave_id');
        $rot_info = $this->request_leave_model->get_row($id);
        $user_info = $this->users_model->get_row($rot_info['user_id']);
        $department = $this->users_type_model->get_row($user_info['user_type']);
        $conformer = $this->users_model->get_row($rot_info['updated_by']);

        
        if($rot_info){
            $response = [
                "response_status" => "success",
                "user_id"         => $rot_info['user_id'],
                "date"            => $rot_info['date'],
                "leave_type"      => $rot_info['leave_type'],
                "details"         => $rot_info['details'],
                "status"          => $rot_info['status'],
                "reason_retracted"          => $rot_info['reason_retracted'],
                "retract_reason_denied"      => $rot_info['retract_reason_denied'],
                "reason_denied"   => $rot_info['reason_denied'],
                "date_created"    => $rot_info['date_created'],
                "date_updated"    => $rot_info['date_updated'],
                "user_name"       => $user_info['name'],
                "user_department" => ucwords($department['user_type']),
                "approved_by_name"=> $conformer['name'],
                "approved_by_id"=> $conformer['id'],
                "salary_deduction" => $rot_info['salary_deduction']
            ];
        }else{
            $response = [
                "response_status"  => "error",
                "message" => "An error has occurred: Unable to read salary grade"
            ];
        }

        echo json_encode($response); 
    }

    public function au_leave_request(){
        $this->form_validation
             ->set_rules('date', 'date', 'required|min_length[10]|callback_validate_date_exist')
             ->set_rules('leave-type', 'leave-type', 'required')
             ->set_rules('details', 'details', 'required')
             ->set_message('min_length','Invalid date')
             ->set_message('required','required');

        $leave_type = NULL;        
        if(isset($_POST['leave-type'])){
            if($_POST['leave-type'] == "Others"){
                $this->form_validation->set_rules('others', 'others', 'required');
                $leave_type = "Others : ".$_POST['others'];
            }else{
                $leave_type = $_POST['leave-type'];
            }
        }

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                if(!isset($_POST['leave_id']))://add
                    $data = [
                        'user_id'    => $_POST['user_id'],
                        'date'       => $_POST['date'],
                        'leave_type' => $leave_type,
                        'details'    => $_POST['details'],
                        'date_created' => date('Y-m-d H:i:s'),
                        'date_updated' => date('Y-m-d H:i:s')
                    ];
                    $add = $this->request_leave_model->add($data);
                    $response = [
                        'message'    => 'Request Sent Successfully',
                        'lr_id'      => $add,
                        'user_id'    => $_POST['user_id'],
                        'date'       => date( "M d, Y (D)",strtotime( $_POST['date'])),
                        'leave_type' => $leave_type,
                        'details'    => $_POST['details']
                    ];
                else://update
                    $data = [
                        'date'       => $_POST['date'],
                        'leave_type' => $leave_type,
                        'details'    => $_POST['details'],
                        'date_updated' => date('Y-m-d H:i:s')
                    ];
                    $this->request_leave_model->update($_POST['leave_id'],$data);
                    $response = [
                        'message'    => 'Leave Updated Successfully',
                        'date'       => date( "M d, Y (D)",strtotime( $_POST['date'])),
                        'leave_type' => $leave_type,
                        'details'    => $_POST['details'],
                        'date_updated' => date('Y-m-d H:i:s')
                    ];
                endif;
               
                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = "success";
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

    public function validate_date_exist(){
        if(!isset($_POST['leave_id'])):
            $duplicate = $this->request_leave_model->get_one_by_where(["user_id"=>$_POST['user_id'],"date"=>$_POST['date']]);
        else:
            $duplicate = $this->request_leave_model->get_one_by_where(["id !="=>$_POST['leave_id'],"date"=>$_POST['date']]);
        endif;

        if($duplicate){
            $this->form_validation->set_message("validate_date_exist", "You already have made a request on this date");
            return false;
        }
        return true;
    }

    public function delete_leave(){
        try {
            $this->request_leave_model->delete($_POST['leave_id']);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Leave Deleted Successfully';
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

    //MIGs start

    private function displayDateRange($fromDate, $toDate = null) {
        $startDate = new DateTime($fromDate);
        $endDate = ($toDate) ? new DateTime($toDate) : null;
        $dates = [];
    
        if ($endDate !== null) {
            $interval = new DateInterval('P1D'); 
            $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
    
            foreach ($dateRange as $date) {
                $dayOfWeek = $date->format('N');
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                    array_push($dates, $date->format('Y-m-d') . PHP_EOL );
                }
            }
        } else {
            $dayOfWeek = $startDate->format('N');
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                array_push($dates, $startDate->format('Y-m-d') . PHP_EOL);
            }
        }
        return $dates;
    }

    public function add_leave(){
        date_default_timezone_set('Asia/Manila');
        $session = $this->session->get_userdata();
        $type = $this->input->post('leave-type');
        $from = $this->input->post('leave-from');
        $to = $this->input->post('leave-to');
        //$count = 1;
        $reason = $this->input->post('leave-reason');

        $this->form_validation
             ->set_rules('leave-type', 'Leave Type', 'required')
             ->set_rules('leave-from', 'Date From', 'required')
             ->set_rules('leave-reason', 'Reason', 'required');

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            $dates = $this->displayDateRange($from, $to);
            $not_added_dates = [];
            foreach($dates as $date){
                $check_date = $this->request_leave_model->get_one_by_where(['user_id' => $session['account_session']['id'], 'date' => $date, 'status' => 'approved']);
                $check_temp_sched = $this->user_temporary_schedule_model->get_one_by_where(['user_id' => $session['account_session']['id'], 'date' => date('Y-m-d', strtotime($date))]);
                
                if($check_temp_sched){
                    $schedule = $check_temp_sched['time'];
                    $exp_sched = explode('-', $schedule);

                    $sched_from = new DateTime(date('h:i a', strtotime($exp_sched[0])));
                    $sched_to = new DateTime(date('h:i a', strtotime($exp_sched[1])));
                    $sched_diff = $sched_from->diff($sched_to);
                    $count = $sched_diff->h >= 8 ? 1 : round(($sched_diff->h / 8), 2);
                }else{
                    $new_date_format = new DateTime($date);
                    $str_date = strtolower($new_date_format->format('l'));
                    $schedule_data = $this->user_schedule_model->get_one_by_where(['user_id' => $session['account_session']['id']]);
                    $schedule = $schedule_data[$str_date];
                    
                    $exp_sched = explode('-', $schedule);
                    
                    $sched_from = new DateTime(date('h:i a', strtotime($exp_sched[0])));
                    $sched_to = new DateTime(date('h:i a', strtotime($exp_sched[1])));
                    $sched_diff = $sched_from->diff($sched_to);
                    $count = $sched_diff->h >= 8 ? 1 : round(($sched_diff->h / 8), 2);
                }

                $row = array(
                    'user_id' => $session['account_session']['id'],
                    'date' => $date,
                    'leave_type' => $type,
                    'leave_count' => $count,    
                    'details' => $reason
                );

                if(!$check_date){
                    $this->request_leave_model->add($row);
                }else{
                    $not_added_dates[] = $date;
                }
                
            }

            $response['status'] = 'success';
            $response['message'] = 'Leave added successfully.';
            $response['not_added_dates'] = $not_added_dates;
        }

        echo json_encode($response);
    }

    public function decline_leave(){
        $session = $this->session->get_userdata();
        $request_id = $this->input->post('request-id');
        $reason = $this->input->post('reason-declined');

        $row = array(
            'updated_by' => $session['account_session']['id'],
            'reason_denied' => $reason,
            'status' => 'denied',
            'date_updated' => date('Y-m-d h:i:s')
        );

        $update = $this->request_leave_model->update($request_id, $row);
        if($update){
            $response['status'] = 'success';
            $response['message'] = 'Declined Successfully';
        }else{
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    public function approve_leave(){

        // echo '<pre>';
        // print_r($this->input->post());
        // exit;

        $session = $this->session->get_userdata();
        $request_id = $this->input->post('request-id');
        $sd_checker = $this->input->post('for-salary-deduction-checker');


        $row = array(
            'updated_by' => $session['account_session']['id'],
            'status' => 'approved',
            'salary_deduction' => isset($sd_checker) ? 1 : 0,
            'date_updated' => date('Y-m-d h:i:s')
        );

        $update = $this->request_leave_model->update($request_id, $row);
        if($update){
            $response['status'] = 'success';
            $response['message'] = 'Approved Successfully';
        }else{
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    public function cancel_leave(){
        {
            $session = $this->session->get_userdata();
            $request_id = $this->input->post('request-id');
    
            $row = array(
                'updated_by' => $session['account_session']['id'],
                'status' => 'cancelled',
                'date_updated' => date('Y-m-d h:i:s')
            );
    
            $update = $this->request_leave_model->update($request_id, $row);
            if($update){
                $response['status'] = 'success';
                $response['message'] = 'Cancelled Successfully';
            }else{
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
    
            echo json_encode($response);
        }
    }

    public function delete_cancelled_leave(){
        {
            $session = $this->session->get_userdata();
            $request_id = $this->input->post('request-id');
    
            $update = $this->request_leave_model->delete($request_id);
            if($update){
                $response['status'] = 'success';
                $response['message'] = 'Deleted Successfully';
            }else{
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
    
            echo json_encode($response);
        }
    }

    public function retract_leave(){
        $id = $this->input->post('request-id');
        $reason = $this->input->post('reason-retracted');

        $row = [
            'reason_retracted' => $reason,
            'status' => 'retraction',
            'date_updated' => date('Y-m-d h:i:s')
        ];

        $update = $this->request_leave_model->update($id, $row);

        if($update){
            $response['status'] = 'success';
            $response['message'] = 'Request to retract successful.';
        }else{
            $response['status'] = 'error';
            $response['message'] = 'Something went wrong. Please contact IT Administrator.';
        }

        echo json_encode($response);
    }

    public function approve_leave_retraction(){
        $session = $this->session->get_userdata();
        $request_id = $this->input->post('request-id');

        $row = array(
            'updated_by' => $session['account_session']['id'],
            'status' => 'retracted',
            'date_updated' => date('Y-m-d h:i:s')
        );

        $update = $this->request_leave_model->update($request_id, $row);
        if($update){
            $response['status'] = 'success';
            $response['message'] = 'Retracted Successfully';
        }else{
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    public function decline_leave_retraction(){
        date_default_timezone_set('Asia/Manila');
        $session = $this->session->get_userdata();
        $request_id = $this->input->post('request-id');
        $reason = $this->input->post('retract-reason-declined');

        $row = array(
            'status' => 'retraction-denied',
            'updated_by' => $session['account_session']['id'],
            'retract_reason_denied' => $reason,
            'date_updated' => date('Y-m-d h:i:s')
        );

        $update = $this->request_leave_model->update($request_id, $row);
        if($update){
            $response['status'] = 'success';
            $response['message'] = 'Decline retract successful';
        }else{
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }
}