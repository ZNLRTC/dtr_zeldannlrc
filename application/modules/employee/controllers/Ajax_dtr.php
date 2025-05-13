    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_dtr extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'users_model',
            'dtr_model',
            'salary_grade_model',
            'holidays_model',
            'overtime_logs_model',
            'request_ot_model',
            'request_leave_model',
            'request_dtr_update_model',
            'user_temporary_schedule_model',
            'undertime_logs_model',
            'user_schedule_model'
        ]);
        //if(!$this->input->is_ajax_request()){ show_404(); }
        require_once APPPATH . 'libraries/tcpdf/tcpdf.php';
    }

    public function save_undertime(){
        date_default_timezone_set('Asia/Manila');
        $account_session = $this->session->userdata('account_session');
        $dtr = $this->dtr_model->get_latest_dtr($account_session['id']);
        $dtr_list = $dtr[0];

        $schedule = explode('-', $dtr_list['schedule_time']);
        $get_in = date('H:i:s', strtotime($dtr_list['time_in']));
        $get_out = date('H:i:s', strtotime($dtr_list['time_out']));
        if($dtr_list['break'] != NULL){
            $get_break = explode('-', $dtr_list['break']);
            $get_exp_break_in = date('H:i:s', strtotime($get_break[0]));
            $get_exp_break_out = date('H:i:s', strtotime($get_break[1]));
        }else{
            $get_exp_break_in = NULL;
            $get_exp_break_out = NULL;
        }
        
        $get_schedule_in = date('H:i:s', strtotime($schedule[0]));
        $get_schedule_out = date('H:i:s', strtotime($schedule[1]));
        $sched_in = new DateTime($get_schedule_in);
        $sched_out = new DateTime($get_schedule_out);
        $get_schedule_hours = $sched_in->diff($sched_out);
        $schedule_minutes = $get_schedule_hours->h * 60;

        $compute_hour_total = compute_total_worked_hours($dtr_list['date'], $get_in, $get_out, $get_exp_break_in, $get_exp_break_out, $get_schedule_in, $get_schedule_out, $dtr_list['schedule_workbase'], $schedule_minutes);
        $exp_date_created = $dtr_list['date'];

        // echo '<pre>';
        // print_r($compute_hour_total);
        // exit;
        if($compute_hour_total['total_under_time'] != '0:0'){
            $date_checker = $this->undertime_logs_model->count_by_where(['user_id' => $account_session['id'], 'date' => $exp_date_created]);
            if($date_checker == 0){
                $log_time_after_eod = $this->undertime_logs_model->add(array(
                    'user_id' => $account_session['id'],
                    'date' => $exp_date_created,
                    'time' => '-'.$compute_hour_total['total_under_time'],
                    'updated_by' => $account_session['id']
                ));

                $response['message'] = 'Undertime saved successfully.';
            }else{
                $response['message'] = 'Undertime already saved for current day.';
            }
        }else{
            $response['message'] = 'No undertime saved.';
        }

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';    
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }

        echo json_encode($response); 
    }

    public function read_dtr(){
        $dtr = $this->dtr_model->get_row($_POST['dtr_id']);
        if($dtr){
            $response = [
                "status"       => "success",
                "user_id"      => $dtr['user_id'],
                "date"         => $dtr['date'],
                "date_input"   => date( "Y-m-d",strtotime( $dtr['date'])),
                "time_in"      => $dtr['time_in'],
                "break"        => $dtr['break'],
                "time_out"     => $dtr['time_out'],
                "work_base"    => $dtr['work_base'],
                "overtime"     => $dtr['overtime'],
                "per_hour"     => $dtr['per_hour'],
                "end_of_day"   => $dtr['end_of_day'],
                "date_created" => $dtr['date_created'],
                "date_updated" => $dtr['date_updated']
            ];
        }else{
            $response = [
                "status"  => "error",
                "message" => "An error has occurred: Unable to read dtr"
            ];
        }

        echo json_encode($response); 
    }

    public function get_user_eod(){
        $id = $this->input->post('user_id');
        $date = $this->input->post('eod_date');
        $eod = $this->dtr_model->get_user_eod_record($id, $date);
        $new_eod = explode("\n", $eod->end_of_day);

        if($eod->end_of_day == null){
            $final_eod = '<span class="opacity-50"><i>No Records found.</i></span>';
        }else{
            $final_eod = implode("<br>", $new_eod);
        }
        
        $response['status'] = 'success';
        $response['eod'] = $final_eod;
        echo json_encode($response);
    }

    public function add_dtr(){
        $this->form_validation
             ->set_rules('time-in', 'time-in', 'required|callback_validate_time_in')
             ->set_rules('work-base', 'work-base', 'required')
             ->set_message('min_length','Invalid date')
             ->set_message('required','required');

        $shift_reason = NULL;
        if(isset($_POST['cb-moved-shift'])){
            $this->form_validation->set_rules('shift-reason', 'shift-reason', 'required');
            $shift_reason = $_POST['shift-reason'];
            if($_POST['shift-reason'] == "Others"){
                $this->form_validation->set_rules('others', 'others', 'required');
                $shift_reason = "Others : ".$_POST['others'];
            }
        }

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                $data = [
                    'user_id'      => $_POST['user_id'],
                    'date'         => $_POST['date'],
                    'time_in'      => $_POST['time-in'],
                    'work_base'    => $_POST['work-base'],
                    'shift_reason' => $shift_reason,
                    'per_hour'     => $_POST['per_hour'],
                    'date_updated' => date('Y-m-d h:i:s')
                ];

                $add = $this->dtr_model->add($data);
                $dtr = $this->dtr_model->get_row($add);
                $dtr_date = explode(' ', $dtr['date_created']);
               
                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $compare  = $this->holidays_model->get_all_by_where(["date"=>date( "Y-m-d",strtotime( $_POST['date']))]);
                    $overtime = $this->request_ot_model->get_one_by_where(['date' => $_POST['date'],'user_id' => $_POST['user_id']]);

                    $response = [
                        'check_holiday' => $compare,
                        'overtime'      => $overtime,
                        'status'        => 'success',
                        'message'       => 'DTR created Successfully',
                        'dtr_id'        => $add,
                        'user_id'       => $_POST['user_id'],
                        'date'          => date( "M d, Y (D)",strtotime( $_POST['date'])),
                        'time_in'       => date( "h:i a",strtotime($_POST['time-in'])),
                        'work_base'     => $_POST['work-base'],
                        'shift_reason'  => ($shift_reason == NULL) ? "No" : $shift_reason,
                        'date_created'  => $dtr_date[0]
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

    public function add_dtr_beta(){
        $user_id = $this->input->post('user-id');
        $date = $this->input->post('date');
        $work_base = $this->input->post('work-base');
        $salary = $this->input->post('per-hour');
        $time_in = $this->input->post('time-in');

        //kunin lang yung oras at minuto
        $array_time = explode(':', $time_in);
        $get_hour_min = [$array_time[0], $array_time[1]];
        $final_time_in = implode(':', $get_hour_min);

        try {
            $row = array(
                'user_id'       => $user_id,
                'date'          => $date,
                'time_in'       => $final_time_in,
                'time_in_work_base' => $work_base,
                'work_base'     => $work_base,
                'per_hour'      => $salary
            ); 

            $dtr_id = $this->dtr_model->add($row);

            if($this->db->trans_status()){
                //$this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Time-in Successfull';
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

    public function time_logs(){
        $user_id = $this->input->post('user_id');
        $action = $this->input->post('action');
        $dtr_id = $this->input->post('dtr_id');
        $eod_report = $this->input->post('eod_report');
        $work_base = $this->input->post('dtr-work-base');
        $sched_in_out = $this->input->post('sched-in-out');
        $sched_workbase = $this->input->post('sched-workbase');

        //fix date for database
        $timezone = new DateTimeZone('Asia/Manila');
        $now = new DateTime('now', $timezone);
        $final_date = $now->format('Y-m-d');
        $final_time = $now->format('H:i');

        $this->form_validation
             ->set_rules('dtr-work-base', 'Work-base', 'required')
             ->set_message('required','Required');

        // echo '<pre>';
        // print_r($this->input->post());
        // exit;

        if($this->form_validation->run() == FALSE && ($action == 'time-in' || $action == 'break-out')){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
            
                switch($action){
                    case 'time-in':
                        $row = array(
                            'user_id' => $user_id,
                            'schedule_time' => $sched_in_out,
                            'schedule_workbase' => $sched_workbase,
                            'date' => $final_date,
                            'time_in' => $final_time,
                            'time_in_work_base' => $work_base,
                            'work_base' => $work_base,
                            'paid' => 'no',
                            'per_hour' => '84'
                        );

                        if($sched_workbase != 'WFH/Office' && $sched_workbase != $work_base){
                            $row['followed_schedule'] = 0;
                        }

                        $message = 'Time-in Successfull';
                        $response['id'] = $this->dtr_model->add($row);
                    break;

                    case 'break-in':
                        $row = array(
                            'break' => $final_time
                        );

                        $message = 'Break-in Successfull';
                        $this->dtr_model->update($dtr_id, $row);
                    break;

                    case 'break-out':
                        $dtr_info = $this->dtr_model->get_row($dtr_id);
                        $break_in = $dtr_info['break'];

                        $t_break_in = new DateTime($break_in);
                        $t_break_out = new DateTime($final_time);
                        $break = $t_break_in->diff($t_break_out);
                        $total_break = ($break->h * 60) + $break->i;

                        if($total_break < 30){ //30 minutes break minimum
                            $response = [
                                'status' => 'break-less-minimum',
                                'break_rendered' => $total_break
                            ];

                            echo json_encode($response);
                            exit;
                        }
                        
                        $break = $break_in .'-'. $final_time;
                        $row = array(
                            'break' => $break,
                            'break_out_work_base' => $this->input->post('dtr-work-base')
                        );

                        if($sched_workbase != 'WFH/Office' && $this->input->post('dtr-work-base') != $sched_workbase){
                            $row['followed_schedule'] = 0;
                        }

                        $message = 'Break-Out Successfull';
                        $this->dtr_model->update($dtr_id, $row);
                    break;

                    case 'time-out':
                        $overtime = $this->input->post('overtime');


                        if($overtime[0] != "" && $overtime[1] != ""){
                            $time = implode('-', $overtime);
                        }else{
                            $time = NULL;
                        }
                        

                        $row = array(
                            'time_out' => $final_time,
                            'overtime' => $time,
                            'end_of_day' => $eod_report,
                            'date_updated' => $now->format('Y-m-d H:i:s')
                        );

                        $message = 'Time-Out Successfull';
                        $this->dtr_model->update($dtr_id, $row);
                    break;

                }

                if($this->db->trans_status()){
                    $this->db->trans_rollback();
                    $response['status'] = 'success';
                    $response['message'] = $message;
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

    

    public function check_time_log_date(){
        $user_id = $this->input->post('user_id');
        $date = $this->input->post('date');
        
        //fix date for database
        $timezone = new DateTimeZone('Asia/Manila');
        $now = new DateTime($date, $timezone);
        $final_date = $now->format('Y-m-d');
        $count = $this->dtr_model->count_by_where(['user_id' => $user_id, 'date' => $final_date]);

        $response['status'] = $count == 0 ? 'success' : 'error';

        echo json_encode($response);

    }

    public function update_dtr(){
        $this->form_validation
             ->set_rules('date', 'date', 'required|min_length[10]')
             ->set_rules('time-in', 'time-in', 'required')
             ->set_rules('time-out', 'time-out', 'required')
             ->set_rules('work-base', 'work-base', 'required')
             ->set_rules('end-of-day', 'end-of-day', 'required')
             ->set_message('min_length','Invalid date')
             ->set_message('required','required');

        $shift_reason = NULL;
        if(isset($_POST['cb-moved-shift'])){
            $this->form_validation->set_rules('shift-reason', 'shift-reason', 'required');
            $shift_reason = $_POST['shift-reason'];
            if($_POST['shift-reason'] == "Others"){
                $this->form_validation->set_rules('others', 'others', 'required');
                $shift_reason = "Others : ".$_POST['others'];
            }
        }

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                $break_time = $_POST['break_time'];
                if($break_time == ""){ $break_time = NULL;}

                date_default_timezone_set('Asia/Manila');
                $data = [
                    'date'         => $_POST['date'],
                    'time_in'      => $_POST['time-in'],
                    'break'        => $break_time,
                    'time_out'     => $_POST['time-out'],
                    'work_base'    => $_POST['work-base'],
                    'end_of_day'   => $_POST['end-of-day'],
                    'overtime'     => ($_POST['ot-in'])?  $_POST['ot-in']."-".$_POST['ot-out']: NULL,
                    'date_updated' => date("Y-m-d H:i:s")
                ];

                $this->dtr_model->update($_POST['dtr_id'], $data);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['break'] = "No break";
                    if($break_time !== NULL){
                        $break = explode("-",$_POST["break_time"]);
                        $response['break'] = date( "h:i a",strtotime( $break[0]))." - ".date( "h:i a",strtotime( $break[1]));
                    }

                    $hour_diff = strtotime($_POST['time-out'])-strtotime($_POST['time-in']);
                    $hours = date('H:i:s', $hour_diff);
                    $hms = explode(":", $hours);
                    if($_SERVER['HTTP_HOST'] == "localhost"): $total = $hms[0] + ($hms[1]/60) - 1;
                    else: $total = $hms[0] + ($hms[1]/60);
                    endif;

                    //Break
                    $break_diff = 0;
                    if($break_time !== NULL){
                        $break = explode("-",$break_time);
                        $bhour_diff = strtotime($break[1])-strtotime($break[0]);
                        $bhours = date('H:i:s', $bhour_diff);
                        $bhms = explode(":", $bhours);
                        if($_SERVER['HTTP_HOST'] == "localhost"): $break_diff = $bhms[0] + ($bhms[1]/60) - 1;
                        else: $break_diff = $bhms[0] + ($bhms[1]/60);
                        endif;
                    }

                    $compare  = $this->holidays_model->get_all_by_where(["date"=>date( "Y-m-d",strtotime( $_POST['date']))]);

                    $response['check_holiday'] = $compare;
                    $response['overtime']      = ($_POST['ot-in'])? date( "h:i a",strtotime( $_POST['ot-in']))." - ".date( "h:i a",strtotime( $_POST['ot-out'])): NULL;
                    $response['status']        = 'success';
                    $response['message']       = "DTR updated Successfully";
                    $response['date']          = date( "M d, Y (D)",strtotime( $_POST['date']));
                    $response['date_day']      = date("D",strtotime( $_POST['date']));
                    $response['time']          = date( "h:i a",strtotime( $_POST['time-in']))." - ".date( "h:i a",strtotime( $_POST['time-out']));
                    $response['work_base']     = $_POST['work-base'];
                    $response['shift_reason']  = ($shift_reason) ? $shift_reason : "no";
                    $response['total_hours']   = round(($total-$break_diff),2);
                    $response['date_updated']  = date('M d, Y h:i a',strtotime($data['date_updated']));
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

    public function validate_time_in(){
        $same_date = $this->dtr_model->get_same_date($_POST['user_id'],$_POST['date']);
        if($same_date):
            $num = count($same_date)-1;
            if(strtotime($same_date[$num]['time_out']) >= strtotime($_POST['time-in'])):
                $this->form_validation->set_message('validate_time_in','Should not be less than your previous time-out on the given date');
                return false;
            endif;
            return true;
        endif;
        return true;
    }

    public function cancel_dtr(){
        try {
            $this->dtr_model->delete($_POST['dtr_id']);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Time Record Deleted Successfully';
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

    public function download_employee_dtr(){
        if($this->session->has_userdata('start-date') && $this->session->has_userdata('end-date')):
            $start = date( "Ymd",strtotime($this->session->userdata('start-date')));
            $end   = date( "Ymd",strtotime($this->session->userdata('end-date') ." + 1 day"));
            $user_id = $this->session->userdata('employee-id');

            /*$start = date( "Ymd",strtotime("06/01/2023"));
            $end   = date( "Ymd",strtotime("06/30/2023 + 1day"));
            $user_id = "58";*/

            $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
            $pdf->SetCreator(PDF_CREATOR);  
            $pdf->SetTitle("Employee DTR List");  
            $pdf->SetHeaderData(base_url("assets/img/nlrc-logo-2.png"), '100px', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
            $pdf->SetDefaultMonospacedFont('helvetica'); 
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
            $pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
            $pdf->setPrintHeader(false);  
            $pdf->setPrintFooter(false);  
            $pdf->SetAutoPageBreak(TRUE, 10);  
            $pdf->SetFont('helvetica', '', 12);  
            $pdf->AddPage();  
            $content = '';  
            $content .= '
                <style>
                    .logo { width: 120px; }
                    .text-left { text-align: left; }
                    .text-right { text-align: right; }
                    .text-center { text-align: center; }
                    .bg-blue { background-color: #0c72ba; color: white; }
                    .bg-gray { background-color: gray; color: white; }
                    .bg-mute-green { background-color: #9bf09b; }
                    .bg-green { background-color: green; color: white; }
                    .bg-mute-orange { background-color: #EB9C5C; color: white; }
                    .bg-dark-gray { background-color: #5a5a5a; color: white; }
                    .t-8 {font-size: 8px !important; }
                    .t-approved { color: #039487; }
                    .t-pending { color: green; }
                    .t-denied { color: red; }
                    table { font-size: 8px;}
                    table tr td { border-bottom: 1px solid white;}
                </style>
                <table>
                <thead>
                    <tr class="logo-con">
                        <td width="50%"><!--img class="logo" src="'.base_url("assets/img/nlrc-logo-2.png").'"--></td>
                        <td width="50%" class="text-right">
                            <b>Employee DTR List</b><br>
                            <span class="t-8">(From '.date( "M d, Y",strtotime($start)).' to '.date( "M d, Y",strtotime($end." - 1 day")).')</span>
                        </td>
                    </tr>
                    <tr><td></td></tr>
                </thead>
                <tbody>
            ';
            $user = $this->users_model->get_row($user_id);
            $content .= '
                <tr class="bg-blue">
                    <td width="40%"><b>'.$user['name'].'</b></td>
                    <td width="35%"><b>Schedule: '.$user['schedule'].'</b></td>
                    <td width="25%" class="text-right"><b>Hourly Rate: ';
                        $sg = $this->salary_grade_model->get_one_by_where(['grade_number' => $user['salary_grade']]);
                        $content .= $sg['hourly_rate'].'/hr</b></td>
                </tr>
                <tr>
                    <td width="10%"><strong>Date(m/d/y)</strong></td>
                    <td width="8%" class="text-center"><strong>In</strong></td>
                    <td width="16%" class="text-center"><strong>Break</strong></td>
                    <td width="8%" class="text-center"><strong>Out</strong></td>
                    <td width="22%"><strong>Holiday</strong></td>
                    <td width="6%" class="text-center"><strong>Shift</strong></td>
                    <td width="9%" class="text-center"><strong>OT</strong></td>
                    <td width="6%" class="text-center"><strong>Leave</strong></td>
                    <td width="10%"><strong>Place</strong></td>
                    <td width="5%" class="text-right"><strong>hrs</strong></td>
                </tr>
            ';
            $total_hours_per_row    = 0;
            $break_time_per_row     = 0;
            $regular_break_per_row  = 0;
            $total_hours            = 0;
            $total_night_diff       = 0;
            $total_night_diff_break = 0;
            $total_break_hours      = 0;
            $total_days             = 0;
            $total_payable          = 0;
            $nd_per_row             = 0;
            $ndb_per_row            = 0;
            $regular_hour_per_row   = 0;
            $payable_per_row =0;
            $period = new DatePeriod(
                new DateTime(date( "Ymd",strtotime($start))),
                new DateInterval('P1D'),
                new DateTime(date( "Ymd",strtotime($end)))
            );

            foreach ($period as $key => $value) {
                $date       = $value->format('m/d/Y');
                $lr         = $this->request_leave_model->get_one_by_where(['user_id' => $user_id, "date" => $date]);
                $otr        = $this->request_ot_model->get_ot($user['id'],$date);
                $check_date = $this->dtr_model->get_one_by_where(["user_id" => $user_id, "date" => $date]);
                $h_list     = $this->holidays_model->get_all();
                $leave      = ($lr && $lr['status'] !== "denied") ? "bg-mute-orange" : "";
                $sat_sun    = ($value->format('D') == "Sat" || $value->format('D') == "Sun") ? "bg-mute-green" : "";

                //Determine if day is holiday
                $date_compare = date("Y-m-d",strtotime($date));
                $holiday      = "";
                foreach($h_list as $h_list):
                    if($h_list['date'] == $date_compare):
                        if($h_list['type'] == "regular"): $holiday = "Regular";
                        elseif($h_list['type'] == "special"): $holiday = "Special Non-working";
                        else: $holiday = "Special Working";
                        endif;
                        break;
                    endif;
                endforeach;

                $time_in    = "";
                $break_time = "";
                $time_out   = "";
                $work_base  = "";
                if($check_date):
                    //Time-in
                    $time_in = date( "h:i a",strtotime($check_date['time_in']));
                    $total_days++;

                    //Calculate Break time
                    if($check_date['break']){
                        $break     = explode("-",$check_date['break']);
                        $hour_diff = strtotime($break[1])-strtotime($break[0]);
                        $hours     = date('H:i:s', $hour_diff);
                        $hms       = explode(":", $hours);
                        if($_SERVER['HTTP_HOST'] == "localhost"): $total = $hms[0] + ($hms[1]/60) - 1;
                        else: $total = $hms[0] + ($hms[1]/60);
                        endif;
                        $total_break_hours += $total;
                        $break_time_per_row += $total;

                        if(strtotime($break[1]) > strtotime("19:00")){
                            $ndb_out = strtotime($break[1])-strtotime("19:00");
                            $ndb_in = strtotime($break[0])-strtotime("19:00");
                            $ndb_out_hours = date('H:i:s', $ndb_out);
                            $ndb_in_hours = date('H:i:s', $ndb_in);
                            $ndb_hms_out = explode(":", $ndb_out_hours);
                            $ndb_hms_in = explode(":", $ndb_in_hours);
                            if($_SERVER['HTTP_HOST'] == "localhost"):
                                $ndb_per_row_out = $ndb_hms_out[0] + ($ndb_hms_out[1]/60) - 1;
                                $ndb_per_row_in = $ndb_hms_in[0] + ($ndb_hms_in[1]/60) - 1;
                            else:
                                $ndb_per_row_out = $ndb_hms_out[0] + ($ndb_hms_out[1]/60);
                                $ndb_per_row_in = $ndb_hms_in[0] + ($ndb_hms_in[1]/60);
                            endif;
                            $ndb_per_row= $ndb_per_row_out - $ndb_per_row_in;
                            if($ndb_per_row > 0){
                                $total_night_diff_break += $ndb_per_row;
                            }
                        }

                        $regular_break_per_row = $break_time_per_row - $ndb_per_row;
                        $break_time = date( "h:i a",strtotime($break[0]))." - ".date( "h:i a",strtotime($break[1]));
                    }
                    
                    //Time-out
                    if($check_date['time_out']){
                        $hour_diff = strtotime($check_date['time_out'])-strtotime($check_date['time_in']);
                        $hours = date('H:i:s', $hour_diff);
                        $hms = explode(":", $hours);
                        if($_SERVER['HTTP_HOST'] == "localhost"):$total = $hms[0] + ($hms[1]/60) - 1;
                        else:$total = $hms[0] + ($hms[1]/60);
                        endif;
                        $total_hours += $total;
                        $total_hours_per_row = $total - $break_time_per_row;
                        if(strtotime($check_date['time_out']) > strtotime("19:00")){
                            $nd = strtotime($check_date['time_out'])-strtotime("19:00");
                            $nd_hours = date('H:i:s', $nd);
                            $nd_hms = explode(":", $nd_hours);
                            if($_SERVER['HTTP_HOST'] == "localhost"): $nd_per_row = $nd_hms[0] + ($nd_hms[1]/60) - 1;
                            else: $nd_per_row = $nd_hms[0] + ($nd_hms[1]/60);
                            endif;

                            if($nd_per_row > 0){
                                $nd_per_row       -= $ndb_per_row;
                                $total_night_diff += $nd_per_row;
                            }
                        }

                        $regular_hour_per_row = $total_hours_per_row - $nd_per_row;
                        $time_out = date( "h:i a",strtotime($check_date['time_out']));
                    }

                    //Compute payable
                    if((!empty($holiday) || !empty($sat_sun) )):
                        if($otr && $total_hours_per_row > 8):
                            $total_payable += 8;
                            $payable_per_row = 8;
                        elseif($otr && $total_hours_per_row <= 8):
                            $total_payable += $total_hours_per_row;
                            $payable_per_row = $total_hours_per_row;
                        endif;
                    else: 
                        if(($total_hours_per_row > 8 && $total_hours_per_row < 10) || ($total_hours_per_row >= 10 && empty($otr))):
                            $total_payable += 8;
                            $payable_per_row = 8;
                            $deduction = $total_hours_per_row - 8;

                        elseif(($total_hours_per_row <= 8) || ($total_hours_per_row >= 10 && $otr)):
                            $total_payable += $total_hours_per_row;
                            $payable_per_row = $total_hours_per_row;
                        endif;
                    endif;
                    $work_base = $check_date['work_base'];
                endif;

                $content .= '<tr class="'.$sat_sun.' '.$leave.'">';
                    $content .= '<td width="10%">'.$date.'</td>';
                    $content .= '<td width="8%" class="text-center">'.$time_in.'</td>
                    <td width="16%" class="text-center">'.$break_time.'</td>
                    <td width="8%" class="text-center">'.$time_out.'</td>
                    <td width="22%">'.$holiday.'</td>
                    <td width="6%" class="text-center">';
                        if($check_date){ if($check_date['shift_reason']){ $content .= "<i>shift</i>"; } }
                    $content .= '</td>
                    <td width="9%">';
                        if($otr):
                            if($otr['type'] !== "Document Checking"):
                                $content .='<i class="'; 
                                    if($otr['status'] == "pending"): $content .='t-pending'; 
                                    elseif($otr['status'] == "approved"): $content .='t-approved';
                                    else: $content .='t-denied';
                                    endif;
                                $content .= '">'.$otr['status'].'</i>';
                            else:
                                $content .= '<i class="t-approved">DocCheck</i>';
                            endif;
                        endif;
                    $content .= '</td>
                    <td width="6%" class="text-center">';
                        if($leave !== ""){ $content .= '<i>Leave</i>'; }
                    $content .= '   </td>
                    <td width="10%">'.$work_base.'</td>
                    <td width="5%" class="text-right">';
                        $hours_worked = round($total_hours_per_row,2);
                        if($hours_worked > 0){ $content .= $hours_worked; }
                    $content .= '</td>
                </tr>';
                $total_hours_per_row   = 0;
                $break_time_per_row    = 0;
                $regular_break_per_row = 0;
                $ndb_per_row           = 0;
                $payable_per_row       = 0;
                $nd_per_row            = 0;
                $regular_hour_per_row  = 0;
            }
            $content .= '<tr>
                <td width="60%" class="bg-dark-gray">Work Days: '.$total_days.' days</td>
                <!--td width="21%" class="bg-dark-gray">Night Diff.: '.round($total_night_diff - $total_night_diff_break, 2).' hrs</td>
                <td width="22%" class="bg-dark-gray">Regular Hours: '.round(($total_hours-$total_break_hours)-($total_night_diff - $total_night_diff_break), 2).'</td-->
                <td width="20%" class="bg-dark-gray">Total hours: '.round($total_hours-$total_break_hours,2).' hrs</td>
                <td width="20%" class="bg-dark-gray">Total Payable: '.round($total_payable,2).'</td>
            </tr>
            <tr>
                <td width="25%" class="bg-dark-gray"><b>Payable regular days</b></td>
                <td width="15%" class="bg-dark-gray">Regular:</td>
                <td width="20%" class="bg-dark-gray">Regular night diff:</td>
                <td width="20%" class="bg-dark-gray">Regular OT:</td>
                <td width="20%" class="bg-dark-gray">Night diff OT:</td>
            </tr>
            <tr>
                <td width="25%" class="bg-dark-gray"><b>Payable holidays/weekends</b></td>
                <td width="15%" class="bg-dark-gray">Regular:</td>
                <td width="60%" class="bg-dark-gray">Night diff:</td>
            </tr>';

            $content .= "</tbody></table>";  
            $pdf->writeHTML($content);
            ob_end_clean();
            $pdf->Output('Employee Dtr List.pdf', 'I');

            $this->session->unset_userdata('start-date');
            $this->session->unset_userdata('end-date');
            $this->session->unset_userdata('employee-id');
        else:
            show_404();
        endif;
    }

    private function compute_ot_time($in, $out) {
        // Convert input times to DateTime objects
        $in_time = DateTime::createFromFormat('H:i', $in);
        $out_time = DateTime::createFromFormat('H:i', $out);
    
        // Check if DateTime objects were created successfully
        if ($in_time === false || $out_time === false) {
            return false;
        }

        // Calculate the difference in hours
        $interval = $in_time->diff($out_time);
        $hours = $interval->h + ($interval->i / 60); // Convert minutes to fractional hours
    
        // Return false if the difference is less than 2 hours
        if ($hours < 2) {
            return false;
        }
    
        // Return the difference in hours
        return $hours;
    }

    public function request_overtime(){
        $account_session = $this->session->userdata('account_session');
        $in = $this->input->post('ot-in');
        $out = $this->input->post('ot-out');
        $task = $this->input->post('ot-reason');
        $id = $this->input->post('dtr-id');
        $holiday = $this->input->post('holiday-ot');
        $ot_hour = $this->compute_ot_time($in, $out);

        $this->form_validation
             ->set_rules("ot-in","Time in","required")
             ->set_rules("ot-out","Time out","required")
             ->set_rules("ot-reason","type","required");
        
        // echo '<pre>';
        // print_r($ot_hour);
        // exit;

        if($this->form_validation->run() == FALSE || !$ot_hour){

            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];

            if(!$ot_hour){
                $response['errors']['invalid-ot'] = 'Did not meet minimum OT hours.';
            }

        }else{
            $ot_type = isset($holiday) ? 'holiday' : 'regular';
            $row = array(
                'dtr_id'        => $id,
                'updated_by'    => $account_session['id'],
                'type'          => $ot_type,
                'task'          => $task,
                'time'          => implode('-', [$in, $out])
            );

            $check_dtr_id = $this->request_ot_model->get_all_by_where(['dtr_id' => $id, 'status' => 'pending']);
            if(!empty($check_dtr_id)){
                $response['status'] = 'ongoing-pending-request';
                $response['message'] = 'Request overtime error. You still have pending request for this DTR.';
            }else{
                $id = $this->request_ot_model->add($row);
                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response = [
                        "status"  => 'success',
                        "message" => 'Request added successfully.',
                    ];
                }else{
                    $this->db->trans_rollback();
                    $response['status'] = 'error';
                    $response['message'] = $this->db->error();
                }
            }
        }
        echo json_encode($response);
    }

    public function move_dtr_to_ot(){
        date_default_timezone_set('Asia/Manila');
        $id = $this->input->post('id');

        $dtr = $this->dtr_model->get_row($id);
        if($dtr['time_out'] == NULL){
            $response = array(
                'status' => 'on-going-dtr',
            );

            echo json_encode($response);
            exit;
        }else{
            $bibo = explode('-', $dtr['break']);

            $row = array(
                'user_id' => $dtr['user_id'],
                'updated_by' => $dtr['user_id'],
                'date' => $dtr['date'],
                'type' => 'holiday',
                'shift' => 'holiday',
                'workbase' => $dtr['schedule_workbase'],
                'time_in' => $dtr['time_in'],
                'time_in_work_base' => $dtr['time_in_work_base'],
                'break_in' => $bibo[0],
                'break_out' => $bibo[1],
                'break_out_work_base' => $dtr['break_out_work_base'],
                'time_out' => $dtr['time_out'],
                'eod' => $dtr['end_of_day'],
                'status' => 'pending',
                'date_created' => date('Y-m-d h:i:s'),
                'date_updated' => date('Y-m-d h:i:s')
            );

            $ot_log_checker = $this->overtime_logs_model->get_one_by_where([
                'user_id' => $dtr['user_id'], 
                'date' => $dtr['date']
            ]);
            
            if(!$ot_log_checker || $ot_log_checker['type'] != 'holiday') {
                $ot_log = $this->overtime_logs_model->add($row);
                if($ot_log) {
                    $this->dtr_model->delete($id);
                }
                
                $response = ['status' => 'success'];
            } else {
                
                $response = ['status' => 'existing-ot-log'];
            }
            
        }
        echo json_encode($response);
    }
}