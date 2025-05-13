    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_users extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'dtr_model',
            'users_model',
            'users_type_model',
            'user_schedule_model',
            'user_temporary_schedule_model'
        ]);
        if(!$this->input->is_ajax_request()){ show_404(); }
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

    public function fetch_user(){
        $user_info = $this->users_model->get_row($_POST['user_id']);
        $user_schedule = $this->user_schedule_model->get_one_by_where(['user_id' => $this->input->post('user_id')]);
        $schedule_needed = array(
            'monday'                => $user_schedule['monday'],
            'monday_workbase'       => $user_schedule['monday_workbase'],
            'tuesday'               => $user_schedule['tuesday'],
            'tuesday_workbase'      => $user_schedule['tuesday_workbase'],
            'wednesday'             => $user_schedule['wednesday'],
            'wednesday_workbase'    => $user_schedule['wednesday_workbase'],
            'thursday'              => $user_schedule['thursday'],
            'thursday_workbase'     => $user_schedule['thursday_workbase'],
            'friday'                => $user_schedule['friday'],
            'friday_workbase'       => $user_schedule['friday_workbase'],
        );

        $temporary_schedules = $this->user_temporary_schedule_model->get_all_by_where(['user_id' => $this->input->post('user_id'), 'status' => 'active']);

        if($user_info){
            $response = [
                "status"        => "success",
                "profile_pic"   => $user_info['profile_pic'],
                "name"          => $user_info['name'],
                "email"         => $user_info['email'],
                "mobile_number" => $user_info['mobile_number'],
                "gender"        => $user_info['gender'],
                "date_of_birth" => $user_info['date_of_birth'],
                "username"      => $user_info['username'],
                "user_type"     => $user_info['user_type'],
                "schedule"      => $user_info['schedule'],
                "branch"        => $user_info['branch'],
                "salary_grade"  => $user_info['salary_grade'],
                "archive"       => $user_info['archive'],
                "added_by"      => $user_info['added_by'],
                "date_created"  => $user_info['date_created'],
                "date_updated"  => $user_info['date_updated'],
                "user_schedule" => $schedule_needed,
                "temp_sched"    => $temporary_schedules
            ];
        }else{
            $response = [
                "status"  => "error",
                "message" => "An error has occurred: Unable to read user profile"
            ];
        }

        echo json_encode($response); 
    }

    public function add_user(){
        $this->form_validation
             ->set_rules('name', 'name', 'required|callback_validate_name')
             ->set_rules('email', 'email', 'required|valid_email|callback_validate_email')
             ->set_rules('mobile-number', 'mobile-number', 'required|trim')
             ->set_rules('gender', 'gender', 'required')
             ->set_rules('date-of-birth', 'date-of-birth', 'required|callback_validate_date_of_birth')
             ->set_rules('role', 'role', 'required')
             ->set_rules('username', 'username', 'required|callback_validate_username')
             ->set_rules('password', 'password', 'required|min_length[8]')
             ->set_rules('schedule-monday-in', 'monday-in', 'required')
             ->set_rules('schedule-monday-out', 'monday-out', 'required')
             ->set_rules('schedule-tuesday-in', 'tuesday-in', 'required')
             ->set_rules('schedule-tuesday-out', 'tuesday-out', 'required')
             ->set_rules('schedule-wednesday-in', 'wednesday-in', 'required')
             ->set_rules('schedule-wednesday-out', 'wednesday-out', 'required')
             ->set_rules('schedule-thursday-in', 'thursday-in', 'required')
             ->set_rules('schedule-thursday-out', 'thursday-out', 'required')
             ->set_rules('schedule-friday-in', 'friday-in', 'required')
             ->set_rules('schedule-friday-out', 'friday-out', 'required')
             ->set_rules('monday-workbase', 'monday-workbase', 'required')
             ->set_rules('tuesday-workbase', 'tuesday-workbase', 'required')
             ->set_rules('wednesday-workbase', 'wednesday-workbase', 'required')
             ->set_rules('thursday-workbase', 'thursday-workbase', 'required')
             ->set_rules('friday-workbase', 'friday-workbase', 'required')
             ->set_message('required', 'required')
             ->set_message('valid_email', 'Email Invalid')
             ->set_message('min_length', 'Atleast 8 characters in length');
        
        $salary_grade = NULL;
        $schedule     = NULL;

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                $account_session = $this->session->userdata('account_session');
                $data = [
                    'profile_pic'   => $_POST['filename'],
                    'name'          => $_POST['name'],
                    'email'         => $_POST['email'],
                    'mobile_number' => $_POST['mobile-number'],
                    'gender'        => $_POST['gender'],
                    'date_of_birth' => $_POST['date-of-birth'],
                    'branch'        => $_POST['branch'],
                    'user_type'     => $_POST['role'],
                    'schedule'      => $schedule,
                    'salary_grade'  => $salary_grade,
                    'username'      => $_POST['username'],
                    'password'      => password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                    'added_by'      => $account_session['id'],
                    'date_updated'  => date('Y-m-d h:i:s')
                ];

                $url= $_SERVER['REQUEST_URI']; 
                $explode = explode("/",$url);
                $base = $explode[1];
                
                if($_POST['filename']){
                    $data['profile_pic'] = $this->rename_profile($base, $_POST['filename']);
                }

                $user_id = $this->users_model->add($data);

                $data_schedule = [
                    'user_id'               => $user_id,
                    'monday'                => $this->input->post('schedule-monday-in') .'-'. $this->input->post('schedule-monday-out'),
                    'monday_workbase'       => $this->input->post('monday-workbase'),
                    'tuesday'               => $this->input->post('schedule-tuesday-in') .'-'. $this->input->post('schedule-tuesday-out'),
                    'tuesday_workbase'      => $this->input->post('tuesday-workbase'),
                    'wednesday'             => $this->input->post('schedule-wednesday-in') .'-'. $this->input->post('schedule-wednesday-out'),
                    'wednesday_workbase'    => $this->input->post('wednesday-workbase'),
                    'thursday'              => $this->input->post('schedule-thursday-in') .'-'. $this->input->post('schedule-thursday-out'),
                    'thursday_workbase'     => $this->input->post('thursday-workbase'),
                    'friday'                => $this->input->post('schedule-friday-in') .'-'. $this->input->post('schedule-friday-out'),
                    'friday_workbase'       => $this->input->post('friday-workbase'),
                ];

                $schedule_id = $this->user_schedule_model->add($data_schedule);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $type = $this->users_type_model->get_row($_POST['role']);
                    $response = [
                        'status'        => 'success',
                        'message'       => 'User Added Successfully',
                        'user_id'       => $user_id,
                        'name'          => $_POST['name'],
                        'email'         => $_POST['email'],
                        'profile_pic'   => $_POST['filename'],
                        'username'      => $_POST['username'],
                        'mobile_number' => $_POST['mobile-number'],
                        'gender'        => $_POST['gender'],
                        'user_type'     => $type['user_type']
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
        }
        echo json_encode($response); 
    }

    public function update_user(){
        $this->form_validation
             ->set_rules('name', 'name', 'required|callback_validate_name')
             ->set_rules('email', 'email', 'required|valid_email|callback_validate_email')
             ->set_rules('mobile-number', 'mobile-number', 'callback_validate_mobile_number')
             ->set_rules('gender', 'gender', 'required')
             ->set_rules('username', 'username', 'required|callback_validate_username')
             ->set_rules('date-of-birth', 'date-of-birth', 'required|callback_validate_date_of_birth')
             ->set_message('required', 'required')
             ->set_message('valid_email', 'Email Invalid');

        $role         = NULL;
        $salary_grade = NULL;
        $schedule     = NULL;

        if($_POST['user_type'] !== "n/a" && $_POST['user_type'] !== "1"){
            $this->form_validation
                 ->set_rules('role', 'role', 'required')
                 ->set_rules('schedule', 'schedule', 'required');
            $role         = $_POST['role'];
            $salary_grade = $_POST['salary-grade'];
            $schedule     = $_POST['schedule'];

            if($_POST['schedule'] == "fixed"){
                $this->form_validation
                     ->set_rules('time-in', 'time-in', 'required')
                     ->set_rules('time-out', 'time-out', 'required');  
                $schedule = $_POST['schedule']."-".$_POST['time-in']."-".$_POST['time-out'];
            }
        }else{
            $user_info    = $this->users_model->get_row($_POST['user_id']);
            $role         = $user_info['user_type'];
            $salary_grade = $user_info['salary_grade'];
            $schedule     = $user_info['schedule'];
        }

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                $branch = "Baguio City";
                $data = [
                    'profile_pic'   => $_POST['filename'],
                    'name'          => $_POST['name'],
                    'email'         => $_POST['email'],
                    'mobile_number' => $_POST['mobile-number'],
                    'gender'        => $_POST['gender'],
                    'date_of_birth' => $_POST['date-of-birth'],
                    'user_type'     => $role,
                    'username'      => $_POST['username'],
                    'schedule'      => $schedule,
                    'salary_grade'  => $salary_grade,
                    'date_updated'  => date('Y-m-d h:i:s')
                ];
                if(isset($_POST['branch'])){
                    $data['branch'] = $_POST['branch'];
                    $branch         = $_POST['branch']; 
                }

                if($_POST['filename'] !== ""){
                    if($_POST['filename'] !== $_POST['old_profile_name']){
                        $split = explode("-",$_POST['filename']);
                        $url= $_SERVER['REQUEST_URI']; 
                        $explode = explode("/",$url);
                        $base = $explode[1];

                        $data['profile_pic'] = $this->rename_profile($base, $_POST['filename']);

                        if($_POST['old_profile_name'] !== ""){
                            $path = ($_SERVER['HTTP_HOST'] == "localhost") ? $_SERVER['DOCUMENT_ROOT']."/".$base."/assets_module/user_profile/".$_POST['old_profile_name'] : $_SERVER['DOCUMENT_ROOT']."/dtr/assets_module/user_profile/".$_POST['old_profile_name'];
                            unlink($path);
                        }

                        
                    }
                }
                
                $this->users_model->update($_POST['user_id'],$data);
                $user_type = $this->users_type_model->get_one_by_where(["id" => $role]);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response = [
                        'status'        => 'success',
                        'message'       => 'Profile Updated Successfully',
                        'profile_pic'   => $data['profile_pic'],
                        'name'          => $_POST['name'],
                        'email'         => $_POST['email'],
                        'mobile_number' => $_POST['mobile-number'],
                        'gender'        => $_POST['gender'],
                        'user_type'     => $user_type['user_type'],
                        'date_of_birth' => $_POST['date-of-birth'],
                        'username'      => $_POST['username'],
                        'branch'        => $branch
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
        }
        echo json_encode($response); 
    }

    public function update_user_profile_only(){
        $this->form_validation
             ->set_rules('gender', 'Gender', 'required')
             ->set_rules('role', 'Role', 'required')
             ->set_rules('date-of-birth', 'Date-of-birth', 'required|callback_validate_date_of_birth')
             ->set_rules('branch', 'Branch', 'required')
             ->set_message('valid_email', 'Email Invalid');
        
        $user_info = $this->users_model->get_row($this->input->post('user-id'));

        if($this->input->post('name') != $user_info['name']){
            $this->form_validation->set_rules('name', 'Name', 'required|callback_validate_name');
        }else{
            $this->form_validation->set_rules('name', 'Name', 'required');
        }

        if($this->input->post('email') != $user_info['email']){
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_validate_email');
        }else{
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        }

        if($this->input->post('username') != $user_info['username']){
            $this->form_validation->set_rules('username', 'Username', 'required|callback_validate_username');
        }else{
            $this->form_validation->set_rules('username', 'Username', 'required');
        }

        if($this->input->post('mobile-number') != $user_info['mobile_number']){
            $this->form_validation->set_rules('mobile-number', 'Mobile-number', 'callback_validate_mobile_number');
        }

        if($this->form_validation->run() == FALSE){
            $response['status'] = 'form-incomplete';
            $response['errors'] = $this->form_validation->error_array();
        }else{
            try{
                $input = $this->input->post();
                $row = array(
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'mobile_number' => $input['mobile-number'],
                    'gender' => $input['gender'],
                    'date_of_birth' => $input['date-of-birth'],
                    'username' => $input['username'],
                    'user_type' => $input['role'],
                    'branch' => $input['branch']
                );

                $this->users_model->update($input['user-id'], $row);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = 'Update user profile successful';
                }else{
                    $this->db->trans_rollback();
                    $response['status'] = 'error';
                    $response['message'] = 'There was an error while updating profile.';
                }

            }catch(Exception $e){
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
        }

        echo json_encode($response);

    }

    public function update_password(){
        $account_session = $this->session->userdata('account_session');

        if($account_session['id'] == $_POST['user_id']){
            $this->form_validation
                ->set_rules('current-password', 'current-password', 'required|callback_validate_current_password')
                ->set_rules('new-password', 'new-password', 'required|min_length[8]|callback_validate_confirm_password')
                ->set_rules('confirm-password', 'confirm-password', 'required');
        }else{
            $this->form_validation->set_rules('new-password', 'new-password', 'required|min_length[8]');
        }

        $this->form_validation
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
                    'password'      => password_hash($_POST['new-password'], PASSWORD_BCRYPT, ['cost' => 12]),
                    'date_updated'  => date('Y-m-d h:i:s')
                ];

                $this->users_model->update($_POST['user_id'],$data);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = 'Password Updated Successfully';
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

    public function profile_image() {
        $url= $_SERVER['REQUEST_URI']; 
        $explode = explode("/",$url);
        $base = $explode[1];

        $temp = explode(".", $_FILES["file"]["name"]);
        $filename = 'Temporary-'.round(microtime(true)).'.'.end($temp);
        $location = ($_SERVER['HTTP_HOST'] == "localhost") ? $_SERVER['DOCUMENT_ROOT']."/".$base."/assets_module/user_profile/".$filename : $_SERVER['DOCUMENT_ROOT']."/dtr/assets_module/user_profile/".$filename;
        if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
            echo $location;
        }else{
            echo "0";
        }
    }

    public function delete_temp_image() {
        $url= $_SERVER['REQUEST_URI']; 
        $explode = explode("/",$url);
        $base = $explode[1];
        $path = ($_SERVER['HTTP_HOST'] == "localhost") ? $_SERVER['DOCUMENT_ROOT']."/".$base."/assets_module/user_profile/".$_POST['filename'] : $_SERVER['DOCUMENT_ROOT']."/assets_module/user_profile/".$_POST['filename'];
        unlink($path);

        //For dev validation only since above code always return true
        /*if (file_exists($path)){
            if (unlink($path)) { echo "success";
            }else{ echo "fail"; }   
        }else{
            echo "fail";
        }*/
    }

    public function validate_name(){
        if(isset($_POST['user_id'])){//means update
            $where = ["id !=" => $_POST['user_id'], "name" => $_POST['name']];
        }else{//add
            $where = ["name" => $_POST['name']];
        }

        $count = $this->users_model->count_by_where($where);
        if($count == 1){
            $this->form_validation->set_message('validate_name','Name already exist');
            return false;
        }
        return true;
    }

    public function validate_email(){
        if(isset($_POST['user_id'])){//means update
            $where = ["id !=" => $_POST['user_id'], "email" => $_POST['email']];
        }else{//add
            $where = ["email" => $_POST['email']];
        }

        $count = $this->users_model->count_by_where($where);
        if($count == 1){
            $this->form_validation->set_message('validate_email','Email already exist');
            return false;
        }
        return true;
    }

    public function validate_mobile_number(){
        if(!empty($_POST['mobile-number'])){
            if(isset($_POST['user_id'])){//means update
                $where = ["id !=" => $_POST['user_id'], "mobile_number" => $_POST['mobile-number']];
            }else{//add
                $where = ["mobile_number" => $_POST['mobile-number']];
            }

            $count = $this->users_model->count_by_where($where);
            if($count == 1){
                $this->form_validation->set_message('validate_email','Email already exist');
                return false;
            }
        }
        return true;
    }

    public function validate_date_of_birth(){
        if(strlen($_POST['date-of-birth']) < 10){
            $this->form_validation->set_message('validate_date_of_birth','Invalid date of birth');
            return false;
        }else{
            $date_diff = date_diff(date_create($_POST['date-of-birth']),date_create(date('m/d/Y')));
            if($date_diff->format('%y') < 18){
                $this->form_validation->set_message('validate_date_of_birth','A user cannot be a minor');
                return false;
            }
        }
        return true;
    }

    public function validate_username(){
        if(isset($_POST['user_id'])){//means update
            $where = ["id !=" => $_POST['user_id'], "username" => $_POST['username']];
        }else{//add
            $where = ["username" => $_POST['username']];
        }

        $count = $this->users_model->count_by_where($where);
        if($count == 1){
            $this->form_validation->set_message('validate_username','Username already exist');
            return false;
        }
        return true;
    }

    public function validate_current_password(){
        $user_info = $this->users_model->get_one_by_where(['id'=>$_POST['user_id']]);

        if(!password_verify($_POST['current-password'], $user_info['password'])){
            $this->form_validation->set_message('validate_current_password','Invalid password');
            return false;
        }
        return true;
    }

    public function validate_confirm_password(){
        if($_POST['new-password'] !== $_POST['confirm-password']){
            $this->form_validation->set_message('validate_confirm_password','Confirm password mismatch');
            return false;
        }
        return true;
    }

    public function archive_user(){
        try {
            $user_id = $this->input->post('user-id');
            $action = $this->input->post('action');

            $data = [
                'archive'      => $action == 'archive' ? 1 : 0,
                'restrict_account' => $action == 'archive' ? 1 : 0,
                'date_updated' => date('Y-m-d h:i:s')
            ];

            $this->users_model->update($user_id, $data);
            $response_message = $action == 'archive' ? 'User Archived Successfully' : 'User Reactivated Successfully';

            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = $response_message;
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

     public function rename_profile($base,$file){
        $original = ($_SERVER['HTTP_HOST'] == "localhost") ? $_SERVER['DOCUMENT_ROOT']."/".$base."/assets_module/user_profile/".$file : $_SERVER['DOCUMENT_ROOT']."/dtr/assets_module/user_profile/".$file;
        $split = explode("-",$_POST['filename']);
        $new_file_name = ($_SERVER['HTTP_HOST'] == "localhost") ? $_SERVER['DOCUMENT_ROOT']."/".$base."/assets_module/user_profile/".$split[1] : $_SERVER['DOCUMENT_ROOT']."/dtr/assets_module/user_profile/".$split[1];
        $renamed= rename($original, $new_file_name);
        return $split[1];
    }

    public function delete_temp_files(){
        $fileList = glob('assets_module/user_profile/*');
        foreach($fileList as $filename){
            if(is_file($filename)){
                $profile = $this->users_model->get_one_by_where(['profile_pic' => basename($filename)]);
                if(!$profile){
                    $url= $_SERVER['REQUEST_URI']; 
                    $explode = explode("/",$url);
                    $base = $explode[1];
                    $path = ($_SERVER['HTTP_HOST'] == "localhost") ? $_SERVER['DOCUMENT_ROOT']."/".$base."/assets_module/user_profile/".basename($filename) : $_SERVER['DOCUMENT_ROOT']."/dtr/assets_module/user_profile/".basename($filename);
                    unlink($path);
                }
            }   
        }

        $response['status'] = 'success';
        $response['message'] = 'Temp Files Removed Successfully';
        echo json_encode($response); 
    }

    public function update_user_schedule_only(){
        $input = $this->input->post();

        $this->form_validation->set_rules('schedule-monday-in', 'Required', 'required')
                              ->set_rules('schedule-monday-out', 'Required', 'required')
                              ->set_rules('schedule-tuesday-in', 'Required', 'required')
                              ->set_rules('schedule-tuesday-out', 'Required', 'required')
                              ->set_rules('schedule-wednesday-in', 'Required', 'required')
                              ->set_rules('schedule-wednesday-out', 'Required', 'required')
                              ->set_rules('schedule-thursday-in', 'Required', 'required')
                              ->set_rules('schedule-thursday-out', 'Required', 'required')
                              ->set_rules('schedule-friday-in', 'Required', 'required')
                              ->set_rules('schedule-friday-out', 'Required', 'required')
                              ->set_rules('monday-workbase', 'Required', 'required')
                              ->set_rules('tuesday-workbase', 'Required', 'required')
                              ->set_rules('wednesday-workbase', 'Required', 'required')
                              ->set_rules('thursday-workbase', 'Required', 'required')
                              ->set_rules('friday-workbase', 'Required', 'required')
                              ->set_message('required', 'Required');

        if($this->form_validation->run() == FALSE){
            $response['status'] = 'form-incomplete';
            $response['errors'] = $this->form_validation->error_array();
        }else{
            try{

                $row = array(
                    'monday'    => $input['schedule-monday-in'].'-'.$input['schedule-monday-out'],
                    'tuesday'   => $input['schedule-tuesday-in'].'-'.$input['schedule-tuesday-out'],
                    'wednesday' => $input['schedule-wednesday-in'].'-'.$input['schedule-wednesday-out'],
                    'thursday'  => $input['schedule-thursday-in'].'-'.$input['schedule-thursday-out'],
                    'friday'    => $input['schedule-friday-in'].'-'.$input['schedule-friday-out'],
                    'monday_workbase'       => $input['monday-workbase'],
                    'tuesday_workbase'      => $input['tuesday-workbase'],
                    'wednesday_workbase'    => $input['wednesday-workbase'],
                    'thursday_workbase'     => $input['thursday-workbase'],
                    'friday_workbase'       => $input['friday-workbase'],
                );

                $this->user_schedule_model->update_where(['user_id' => $this->input->post('user-id')], $row);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = 'Update schedule successful';
                }else{
                    $this->db->trans_rollback();
                    $response['status'] = 'error';
                    $response['message'] = 'Update schedule failed. Please alert IT admin.';
                }

            }catch(Exception $e){
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
        }
        

        echo json_encode($response);
    }

    public function add_temporary_schedule_only(){
        
        $input = $this->input->post();
        $this->form_validation->set_rules('temp-schedule-date-from', 'Date From', 'required')
                            -> set_rules('temp-schedule-in', 'Time-in', 'required')
                            -> set_rules('temp-schedule-out', 'Time-out', 'required')
                            -> set_rules('temp-workbase', 'Workbase', 'required');
    
        if($this->form_validation->run() == FALSE){
            $response['status'] = 'form-incomplete';
            $response['errors'] = $this->form_validation->error_array();
        }else{
            try{

                $dates = $this->displayDateRange($input['temp-schedule-date-from'], $input['temp-schedule-date-to']);

                foreach($dates as $date){
                    $row = array(
                        'user_id' => $input['user-id'],
                        'date' => date('Y-m-d', strtotime($date)),
                        'time' => $input['temp-schedule-in'].'-'.$input['temp-schedule-out'],
                        'workbase' => $input['temp-workbase']
                    );
    
                    $dtr = $this->dtr_model->get_one_by_where(['user_id' => $input['user-id'], 'date' => date('Y-m-d', strtotime($date))]);
                    $schedule = $this->user_temporary_schedule_model->get_one_by_where(['user_id' => $input['user-id'], 'date' => date('Y-m-d', strtotime($date))]);
                    
                    if($dtr){
                        $this->dtr_model->update($dtr['id'], ['schedule_time' => $input['temp-schedule-in'].'-'.$input['temp-schedule-out'], 'schedule_workbase' => $input['temp-workbase']]);
                    }

                    if($schedule){
                        $this->user_temporary_schedule_model->update($schedule['id'], ['time' => $input['temp-schedule-in'].'-'.$input['temp-schedule-out'], 'workbase' => $input['temp-workbase']]);
                    }else{
                        $id = $this->user_temporary_schedule_model->add($row);
                    }
                }

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = 'Temporary Schedule Added';
                }else{
                    $this->db->trans_rollback();
                    $response['status'] = 'error';
                    $response['message'] = 'Something went wrong. Please contact IT admin';
                }
                
            }catch(Exception $e){
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
        }

        echo json_encode($response);
    }

    public function edit_temporary_schedule(){
        $input = $this->input->post();

        $this->form_validation->set_rules('temp-sched-date', 'Date', 'required')
                            -> set_rules('temp-sched-in', 'Time-in', 'required')
                            -> set_rules('temp-sched-out', 'Time-out', 'required')
                            -> set_rules('temp-sched-workbase', 'Workbase', 'required');
    
        if($this->form_validation->run() == FALSE){
            $response['status'] = 'form-incomplete';
            $response['errors'] = $this->form_validation->error_array();
        }else{
            try{

                $row = array(
                    'date' => date('Y-m-d', strtotime($input['temp-sched-date'])),
                    'time' => $input['temp-sched-in'].'-'.$input['temp-sched-out'],
                    'workbase' => $input['temp-sched-workbase']
                );

                $data = [
                    'date' => $input['temp-sched-date'],
                    'time_in' => $input['temp-sched-in'],
                    'time_out' => $input['temp-sched-out'],
                    'workbase' => $input['temp-sched-workbase']
                ];

                $dtr = $this->dtr_model->get_one_by_where(['user_id' => $input['user-id'], 'date' => date('Y-m-d', strtotime($input['temp-sched-date']))]);
                
                if($dtr){
                    $this->dtr_model->update($dtr['id'], ['schedule_time' => $input['temp-sched-in'].'-'.$input['temp-sched-out'], 'schedule_workbase' => $input['temp-sched-workbase']]);
                }

                $id = $this->user_temporary_schedule_model->update($input['schedule-id'], $row);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = 'Temporary Schedule Edited';
                    $response['data'] = $data;
                }else{
                    $this->db->trans_rollback();
                    $response['status'] = 'error';
                    $response['message'] = 'Something went wrong. Please contact IT admin';
                }
                
            }catch(Exception $e){
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
        }

        echo json_encode($response);
        
    }

    public function cancel_temporary_schedule(){
        $id = $this->input->post('sched-id');
        
        try{
            $this->user_temporary_schedule_model->delete($id);

            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Temporary schedule has been cancelled.';
            }else{
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = 'Something went wrong. Please contact IT administrator.';
            }
        }catch(Exception $e){
            $this->trans_rollback();
            $response['status'] = 'errror';
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    public function update_employee_profile(){
        $input = $this->input->post();
        $user_id        = $input['user-id'];
        $department     = $input['department'];
        $username       = $input['user-name'];
        $email          = $input['email'];
        $mobile_number  = $input['mobile-number'];
        $branch         = $input['branch'];
        $gender         = $input['gender'];
        $name           = $input['name'];

        $this->form_validation->set_rules('department', 'Department', 'required')
                            -> set_rules('user-name', 'Username', 'required')
                            -> set_rules('email', 'Email', 'required')
                            -> set_rules('mobile-number', 'Mobile Number', 'required')
                            -> set_rules('gender', 'Gender', 'required')
                            -> set_rules('name', 'Name', 'required');
    
        if($this->form_validation->run() == FALSE){
            $response['status'] = 'form-incomplete';
            $response['errors'] = $this->form_validation->error_array();
        }else{
            try{

                $row = array(
                    'name' => $name,
                    'email' => $email,
                    'mobile_number' => $mobile_number,
                    'gender' => $gender,
                    'username' => $username,
                    'user_type' => $department,
                    'branch' => $branch,
                    'date_updated' => date('Y-m-d h:i:s')
                );

                $this->users_model->update($user_id, $row);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = 'Profile has been updated!';
                }else{
                    $this->db->trans_rollback();
                    $response['status'] = 'error';
                    $response['message'] = 'Something went wrong. Please contact IT admin';
                }
                
            }catch(Exception $e){
                $this->db->trans_rollback();
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
        }

        echo json_encode($response);

        // echo '<pre>';
        // print_r([$department, $username, $email, $mobile_number, $gender, $name]);
        // exit;

    }
}