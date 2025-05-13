    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_leave_request extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'users_model',
            // added below
            'users_type_model',
            'request_leave_model',
        ]);
        if(!$this->input->is_ajax_request()){ show_404(); }
    }

     public function update_leave_status(){
        $reason_denied = NULL;
        if($_POST['reason-denied'] !== ""){ $reason_denied = $_POST['reason-denied']; }

        try{
            $data = [
                "status"        => $_POST['status'],
                "reason_denied" => $reason_denied
            ];

            $this->request_leave_model->update($_POST['leave_id'],$data);

            if($this->db->trans_status()){
                $this->db->trans_commit();
                $pending = $this->request_leave_model->count_by_where(['status' => 'pending']);
                $response = [
                    "status"  => 'success',
                    "message" => 'Status Updated Successfully',
                    "pending" => $pending
                ];
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

    private function table_html($name, $user_id, $leave_ids){

        $table_data = [];
        $table_data_0 = [];
        foreach($leave_ids as $id){

            $leave = $this->request_leave_model->get_row($id);
            ($leave['remarks'] == NULL || $leave['remarks'] == '') ? $remarks = '<span class="opacity-25">No Remarks</span>' : $remarks = $leave['remarks'];

            $tr_0 = '<td>
                        <div class="name">'.$name.'</div>
                    </td>';

            $tr_1 = '<td>
                        <input name="table-row-leave-id" type="hidden" value="'.$id.'">
                        <div class="date w405-hide">'.convert_date($leave["date"]).'</div>
                    </td>';

            $tr_2 = '<td>
                        <div class="leave-type">'.ucwords($leave['leave_type']).' Leave</div>
                    </td>';

            $tr_3 = '<td>
                        <div class="leave-details">'.ucwords($leave['details']).'</div>
                    </td>';
                    
            $tr_4 = '<td>
                        <div class="leave-remarks mobile-hide">'.ucwords($remarks).'</div>
                    </td>';

            $tr_5 = '<td>
                        <div class="action-btn">
                            <a href="'.base_url().'admin/employee_leaves?id='.$user_id.'" class="d-flex justify-content-end"><button class="btn view view-leaves-btn d-flex align-items-center"><i class="fa-solid fa-eye me-1"></i> <span class="mobile-hide">View Leaves</span></button></a>
                        </div>
                    </td>';

            $tr_6 = '<td>
                        <div class="raw-dates">'.$leave["date"].'</div>
                    </td>';

            $tr_7 = '<td>
                        <div class="d-flex align-items-center">
                            <button class="btn edit edit-leaves-btn" data-leave-id="'.$leave['id'].'" data-user-name="'.$name.'">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span class="mobile-hide">Edit</span>
                            </button>

                            <button class="btn cancel cancel-leaves-btn" data-leave-id="'.$leave['id'].'">
                                <i class="fas fa-times-circle"></i>
                                <span class="mobile-hide">Cancel</span>
                            </button>
                        </div>
                    </td>';
            
            $tr_8 = '<td>
                        <div class="action-btn">
                            <a href="'.base_url().'admin/employee_leaves?id='.$user_id.'" class="d-flex justify-content-end">
                                <button class="btn view view-leaves-btn d-flex align-items-center">
                                    <i class="fa-solid fa-eye me-1"></i> 
                                    <span class="mobile-hide">View Leaves</span>
                                </button>
                                <button class="btn edit edit-leaves-btn" data-leave-id="'.$leave['id'].'" data-user-name="'.$name.'">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span class="mobile-hide">Edit</span>
                                </button>

                                <button class="btn cancel cancel-leaves-btn" data-leave-id="'.$leave['id'].'">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="mobile-hide">Cancel</span>
                                </button>
                            </a>
                        </div>
                    </td>';
                    
            
            $table_data[] = [$tr_0, $tr_1, $tr_2, $tr_3, $tr_4, $tr_8, $tr_6];
            $table_data_0[] = [$tr_1, $tr_2, $tr_3, $tr_4, $tr_7];
        }

        // echo '<pre>';
        // print_r($table_data);
        // exit;

        return [$table_data, $table_data_0];
    }

    public function add_leave(){
        $type           = $this->input->post('leave-type');
        $user_id        = $this->input->post('employee-id');
        $leave_from     = $this->input->post('leave-from');
        $leave_to       = $this->input->post('leave-to');
        $reason         = $this->input->post('reason');
        $remarks        = $this->input->post('remarks');
        $day            = $this->input->post('whole-day-radio');

        $this->form_validation
                ->set_rules("leave-type", "Type", "required")
                ->set_rules("employee-id", "Employee", "required")
                ->set_rules("leave-from", "Date From", "required")
                ->set_rules("reason", "Details", "required");
        
        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            $user = $this->users_model->get_row($user_id);
            $dates = $this->displayDateRange($leave_from, $leave_to);
            $leave_ids_added = [];
            $leave_types = [];

            if($type == 'birthday'){
                $fetch_birthday = $this->request_leave_model->get_one_by_where([
                    'user_id' => $user_id,
                    'leave_type' => 'birthday'
                ]);

                if(!$fetch_birthday){
                    $is_first_date = true;
                    foreach($dates as $date){
                        $row = array(
                            'user_id'       => $user_id,
                            'date'          => $date,
                            'leave_type'    => ($is_first_date == true) ? $type : 'vacation',
                            'leave_count'   => $day,
                            'details'       => $reason,
                            'status'        => 'approved',
                            'remarks'       => $remarks
                        );
        
                        $check_leave = $this->request_leave_model->get_all_by_where(['user_id' => $user_id, 'date' => $date]);
                        if(count($check_leave) == 0) {
                            $leave_id = $this->request_leave_model->add($row);
                            $leave_ids_added[] = $leave_id;
                        }

                        $leave_types[] = $type;
                    }

                    $table_data = $this->table_html($user['name'], $user['id'], $leave_ids_added);
                    $response['status'] = 'success';
                    $response['message'] ="Leaves added successfully";
                    $response['table_data'] = $table_data[0];
                    $response['table_data_0'] = $table_data[1];
                    $response['ids'] = $leave_ids_added;
                    $response['leave_type'] = $leave_types;
                }else{
                    $response['status'] = 'error';
                    $response['message'] = 'The current user has already acquired his/her Birthday Leave.';
                }
            }else{
                foreach($dates as $date){
                    $row = array(
                        'user_id'       => $user_id,
                        'date'          => $date,
                        'leave_type'    => $type,
                        'leave_count'   => $day,
                        'details'       => $reason,
                        'status'        => 'approved',
                        'remarks'       => $remarks
                    );

                    $check_leave = $this->request_leave_model->get_all_by_where(['user_id' => $user_id, 'date' => $date]);
                    if(count($check_leave) == 0) {
                        $leave_id = $this->request_leave_model->add($row);
                        $leave_ids_added[] = $leave_id;
                    }
                }

                $table_data = $this->table_html($user['name'], $user['id'], $leave_ids_added);
                $response['status'] = 'success';
                $response['message'] ="Leaves added successfully";
                $response['table_data'] = $table_data[0];
                $response['table_data_0'] = $table_data[1];
                $response['ids'] = $leave_ids_added;
                $response['leave_type'] = $leave_types;
            }
        }

        echo json_encode($response);
        
    }

    public function fetch_leave_info(){
        $id = $this->input->post('id');
        $info = $this->request_leave_model->get_row($id);
        $user = $this->users_model->get_row($info['user_id']);
        $info['user_name'] = $user['name'];

        $response = array(
            'status' => 'success',
            'data' => $info
        );

        echo json_encode($response); 
    }

    public function cancel_leave(){
        
        $this->db->trans_begin();

        try {
            $id = $this->input->post('leave-id');
            $leave = $this->request_leave_model->get_row($id);
            $this->request_leave_model->delete($id);

            if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = 'Cancel of leave successfull';
                    $response['leave_type'] = $leave['leave_type'];
                    $response['data'] = $id;

            }
            else{
                    $this->db->trans_rollback();
                    $response['status'] = 'error';
                    $response['message'] = $this->db->error();
            }
        }catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    public function edit_leave(){
        $this->db->trans_begin();
        try {
            $id             = $this->input->post('leave-id');
            $type           = $this->input->post('leave-type');
            $leave_from     = $this->input->post('leave-from');
            $reason         = $this->input->post('reason');
            $remarks        = $this->input->post('remarks');
            $day            = $this->input->post('whole-day-radio');
            $data           = $this->request_leave_model->get_row($id);

            $row = array(
                'date'          => $leave_from,
                'leave_type'    => $type,
                'leave_count'   => $day,
                'details'       => $reason,
                'remarks'       => $remarks
            );

            $where = array(
                'id' => $id
            );

            $birthdate = $this->request_leave_model->count_by_where(['user_id' => $data['user_id'], 'leave_type' => 'birthday']);

            if ($type == 'birthday' && $birthdate == 0) {
                $this->request_leave_model->update_where($where, $row);
                $leave_data = $this->request_leave_model->get_row($id);

                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $response = [
                        'status'      => 'success',
                        'message'     => 'Update leave successful',
                        'data'        => $leave_data,
                        'birthday'      => $birthdate
                    ];
                } else {
                    $this->db->trans_rollback();
                    $response = [
                        'status'  => 'error',
                        'message' => $this->db->error()
                    ];
                }
            } elseif ($type == 'birthday' && $birthdate > 0) {
                $response = [
                    'status'  => 'error',
                    'message' => 'The current user has already acquired his/her Birthday Leave.',
                    'input' => $this->input->post()
                ];
            } else {
                $this->request_leave_model->update_where($where, $row);
                $leave_data = $this->request_leave_model->get_row($id);

                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $response = [
                        'status'      => 'success',
                        'message'     => 'Update leave successful',
                        'data'        => $leave_data,
                        'birthday'      => $birthdate
                    ];
                } else {
                    $this->db->trans_rollback();
                    $response = [
                        'status'  => 'error',
                        'message' => $this->db->error()
                    ];
                }
            }
        }catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    
    public function fetch_leave_request(){
        $rot_info = $this->request_leave_model->get_row($_POST['leave_id']);
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
                "reason_retracted"   => $rot_info['reason_retracted'],
                "reason_denied"   => $rot_info['reason_denied'],
                "date_created"    => $rot_info['date_created'],
                "date_updated"    => $rot_info['date_updated'],
                "user_name"       => $user_info['name'],
                "user_department" => ucwords($department['user_type']),
                "approved_by_name"=> $conformer['name'],
                "approved_by_id"=> $conformer['id'],
            ];
        }else{
            $response = [
                "response_status"  => "error",
                "message" => "An error has occurred: Unable to read salary grade"
            ];
        }

        echo json_encode($response); 
    }


    
    

}