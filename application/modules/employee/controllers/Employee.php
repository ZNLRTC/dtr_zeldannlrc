<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Employee extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'users_model',
            'users_type_model',
            'salary_grade_model',
            'request_ot_model',
            'dtr_model',
            'request_leave_model',
            'request_ot_model',
            'holidays_model',
            'overtime_logs_model',
            'request_dtr_update_model',
            'request_change_time_model',
            'user_temporary_schedule_model',
            'user_schedule_model',
            'undertime_logs_model'
        ]);

        $this->load->helper('general_helper');

        if($this->session->has_userdata('is_logged_in')){
            $type = $this->session->userdata('is_logged_in');
            if($type["type"] !== "employee"){ header("Location: ".base_url($type['type'])); }
        }else { header("Location: ".base_url()); }
    }

    public function index() {
        date_default_timezone_set('Asia/Manila');

        $month = $this->input->get('month');
        $year = $this->input->get('year');

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');

        $current_leave = [];

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $salary = $this->salary_grade_model->get_one_by_where(['grade_number' => $user_info['salary_grade']]); 
        $leaves_current_month = $this->request_leave_model->get_leave_for_current_month($user_info['id']);
        $dtr_list_first_record = $this->dtr_model->get_first_ever_record($user_info['id']);
        $leave_checker = $this->request_leave_model->get_one_by_where(['user_id' => $account_session['id'], 'date' => date('Y-m-d'), 'status' => 'approved']);
        $holiday_checker = $this->holidays_model->get_one_by_where(['date' => date('Y-m-d')]);

        foreach($leaves_current_month as $leave){
            $date1 = new DateTime($leave['date']);
            $date2 = new DateTime(date('Y-m-d'));

            if($date1 == $date2){
                $current_leave[] = $this->request_leave_model->get_one_by_where(['user_id' => $user_info['id'], 'date' => $leave['date']]);
            }
        }

        $dtrs = $this->dtr_model->get_all_my_dtr_desc($user_info['id'], $month, $year);
        $schedule = $this->user_schedule_model->get_one_by_where(['user_id' => $user_info['id']]);
        $temporary_schedule = $this->user_temporary_schedule_model->get_temp_schedule_for_month($user_info['id'], date('Y'), date('m'));
        $temp_sched_today = $this->user_temporary_schedule_model->get_temp_schedule_today($user_info['id'], date('Y-m-d'));
        $temp_sched = [];
        $current_dtr = $this->dtr_model->get_current_dtr($user_info['id']);

        if($temp_sched_today){
            $current_schedule = $temp_sched_today;
        }else{
            $current_schedule = ['date' => date('Y-m-d'), 'in-out' => $schedule[strtolower(date('l'))], 'workbase' => $schedule[strtolower(date('l')).'_workbase']];
        }
        
        if($current_dtr){
            $schedule = explode('-', $current_dtr['schedule_time']);
            $sched_in = new DateTime($schedule[0]);
            $sched_out = new DateTime($schedule[1]);
            $get_schedule_hours = $sched_in->diff($sched_out);
            $schedule_minutes = $get_schedule_hours->h * 60;
        }else{
            $schedule_minutes = 540;
        }
        
        
        $user_info['dtr_month'] = get_month_and_year($dtr_list_first_record);
        $user_info['current_dtr'] = $current_dtr;
        $user_info['latest_dtr'] = $this->dtr_model->get_latest_dtr($user_info['id']);
        $user_info['dtr_lists'] = $this->dtr_model->get_all_my_dtr_desc($user_info['id'], $month, $year);
        $user_info['month_year'] = [$month, $year];
        $user_info['current_leave'] = $current_leave;
        $user_info['current_schedule'] = $current_schedule;
        $user_info['leave_checker'] = $leave_checker;
        $user_info['holiday_checker'] = $holiday_checker;
        $user_info['hourly_salary'] = null;
        $user_info['schedule_minutes'] = $schedule_minutes;
        $user_info['active_log'] = $this->dtr_model->count_by_where([
                'user_id' => $user_info['id'],
                'time_out' => NULL
            ]);
        
        // echo '<pre>';
        // print_r($schedule_minutes);
        // exit;
        

        $this->template
            ->title('Main')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/employee.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/overtimes.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/moment.min.js') . '"></script>')
            ->build('index_employee', $user_info);
    }

    public function leaves() {
        $year = $this->input->get('year');
        isset($year) ? $year = $year : $year = date('Y');
        $years = [];

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $first_leave = $this->request_leave_model->get_first_ever_record($user_info['id']);
        $first_dtr = $this->dtr_model->get_first_ever_record($account_session['id']);

        $birthday_leave         = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'birthday');
        $vacation_leave         = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'vacation', 1);
        $vacation_leave_half    = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'vacation', 0.5);
        $sick_leave             = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'sick', 1);
        $sick_leave_half        = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'sick', 0.5);
        $special_leave          = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'special', 1);
        $special_leave_half     = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'special', 0.5);

        if(!empty($first_leave)){
            $first_leave_array = explode('-', $first_leave[0]['date']);
            for($i = date('Y'); $i >= $first_leave_array[0]; $i--){
                $years[] = $i;
            }
        }else{
            $years[] = date('Y');
        }

        $all_leaves = $this->request_leave_model->get_all_my_leave_desc($user_info['id'], $year);
        $count_leaves = $this->request_leave_model->count_yearly_leaves($user_info['id'], $year);
        $remaining_leaves = 24 - $count_leaves;

        $first_dtr_month = date('m', strtotime($first_dtr[0]['date']));
        $first_dtr_year = date('Y', strtotime($first_dtr[0]['date']));
        if($first_dtr_year < date('Y')){
            $month1 = 00;
        }else{
            $month1 = $first_dtr_month - 1;
        }

        $undertimes_compensated = $this->undertime_logs_model->get_all_by_where([
            'user_id' => $account_session['id'], 
            'compensated' => 1,
            'YEAR(date)' => date('Y')
        ]);

        $total_compensated = compute_undertime_in_minutes($undertimes_compensated);

        // undertime plus vacation leave
        $ut_vl = $total_compensated + (($vacation_leave * 480) + (($vacation_leave_half * 0.5) * 480));
        $total_vl = $ut_vl / 480;

        $user_info['leaves'] = $all_leaves;
        $user_info['years'] = $years;
        $user_info['accumulated_leaves'] = $count_leaves;
        $user_info['remaining_leaves'] = $remaining_leaves;
        $user_info['selected_year'] = $year;
        $user_info['employee_birthday_leave_count'] = $birthday_leave;
        $user_info['employee_sick_leave_count'] = $sick_leave + ($sick_leave_half * 0.5);
        $user_info['employee_vacation_leave_count'] = round($total_vl, 2);
        $user_info['employee_special_leave_count'] = $special_leave + ($special_leave_half * 0.5);
        $user_info['month1'] = $month1;

        // echo '<pre>';
        // print_r($undertimes_compensated);
        // exit;


        $this->template
            ->title('Leaves')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/leaves.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/moment.min.js') . '"></script>')
            ->build('index_leaves', $user_info);
    }


    public function active() {
        date_default_timezone_set('Asia/Manila');
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        
        $employees = $this->users_model->get_all_employees();
        $do_not_include = [1, 5, 16];
        foreach($employees as $key => $e){
            if(!in_array($e['user_type'], $do_not_include)){
                $temp_sched = $this->user_temporary_schedule_model->get_one_by_where(['user_id' => $e['id'], 'date' => date('Y-m-d'), 'status' => 'active']);
                if($temp_sched){
                    $time = $temp_sched['time'];
                    $workbase = $temp_sched['workbase'];
                }else{
                    $today = strtolower(date('l'));
                    $main_sched = $this->user_schedule_model->get_one_by_where(['user_id' => $e['id']]);
                    $time = $main_sched[$today];
                    $workbase = $main_sched[$today . '_workbase'];
                }

                $leave = $this->request_leave_model->get_one_by_where(['date' => date('Y-m-d'), 'user_id' => $e['id']]);
                if($leave){
                    $employees[$key]['on_leave_today'] = 1;
                    $employees[$key]['on_leave_details'] = $leave['leave_type'] . ' Leave | ' . $leave['details'];
                }else{
                    $employees[$key]['on_leave_today'] = 0;
                }

                $employees[$key]['sched_time'] = $time;
                $employees[$key]['sched_workbase'] = $workbase;

                $e_active_dtr = $this->dtr_model->get_one_by_where(['user_id' => $e['id'], 'time_out' => NULL]);
                if($e_active_dtr){
                    $employees[$key]['active_dtr'] = $e_active_dtr;
                }else{
                    $employees[$key]['active_dtr'] = $this->dtr_model->get_one_by_where(['user_id' => $e['id'], 'date' => date('Y-m-d')]);
                }
            }
        }

        // echo '<pre>';
        // print_r($employees);
        // exit;

        $user_info['employees'] = $employees;

        $this->template
            ->title('Leaves')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/active_dtr.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/moment.min.js') . '"></script>')
            ->build('index_active_dtr_v1', $user_info);
    }

    public function active_beta() {
        date_default_timezone_set('Asia/Manila');
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        
        $employees = $this->users_model->get_all_by_where_with_sort(['archive' => 0,], 'name', 'asc');
        foreach($employees as $key => $e){
            if($e['user_type'] != 1 || $e['user_type'] != 5){
                $temp_sched = $this->user_temporary_schedule_model->get_one_by_where(['user_id' => $e['id'], 'date' => date('Y-m-d'), 'status' => 'active']);
                if($temp_sched){
                    $time = $temp_sched['time'];
                    $workbase = $temp_sched['workbase'];
                }else{
                    $today = strtolower(date('l'));
                    $main_sched = $this->user_schedule_model->get_one_by_where(['user_id' => $e['id']]);
                    $time = $main_sched[$today];
                    $workbase = $main_sched[$today . '_workbase'];
                }

                $leave = $this->request_leave_model->get_one_by_where(['date' => date('Y-m-d'), 'user_id' => $e['id']]);
                if($leave){
                    $employees[$key]['on_leave_today'] = 1;
                    $employees[$key]['on_leave_details'] = $leave['leave_type'] . ' Leave | ' . $leave['details'];
                }else{
                    $employees[$key]['on_leave_today'] = 0;
                }

                $employees[$key]['sched_time'] = $time;
                $employees[$key]['sched_workbase'] = $workbase;

                $e_active_dtr = $this->dtr_model->get_one_by_where(['user_id' => $e['id'], 'time_out' => NULL]);
                if($e_active_dtr){
                    $employees[$key]['active_dtr'] = $e_active_dtr;
                }else{
                    $employees[$key]['active_dtr'] = $this->dtr_model->get_one_by_where(['user_id' => $e['id'], 'date' => date('Y-m-d')]);
                }
            }
        }

        // echo '<pre>';
        // print_r($employees);
        // exit;
        $user_info['employees'] = $employees;

        $this->template
            ->title('Leaves')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/active_dtr.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/moment.min.js') . '"></script>')
            ->build('index_active_dtr_beta', $user_info);
    }

    public function view_emp() {

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $employee_id = $this->input->get('i');
        $month = $this->input->get('m');
        $year = $this->input->get('y');
        $month ? $month = $month : $month = date('m');
        $year ? $year = $year : $year = date('Y');

        if(!$employee_id) show_404();

        $dtr_list_first_record = $this->dtr_model->get_first_ever_record($employee_id);
        $user_dtr = $this->dtr_model->get_all_my_dtr_desc($employee_id, $month, $year);
        $employee_info = $this->users_model->get_row($employee_id);
        $user_type_name = $this->users_type_model->get_one_by_where(['id' => $account_session['id']]);

        $user_info['user_type_name'] = $user_type_name;
        $user_info['employee_info'] = $employee_info;
        $user_info['employee_info']['dtrs'] = $user_dtr;
        $user_info['employee_info']['dtr_month'] = get_month_and_year($dtr_list_first_record);
        $user_info['employee_info']['month_year'] = [$month, $year];

        // echo '<pre>';
        // print_r($user_info);
        // exit;

        $this->template
            ->title('Leaves')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/active_dtr.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/moment.min.js') . '"></script>')
            ->build('index_employee_dtr', $user_info);
    }

    public function employee_leaves() {

        $user_id = $this->input->get('id');
        if(!$user_id) show_404();

        $year = $this->input->get('year');
        isset($year) ? $year = $year : $year = date('Y');
        $years = [];
        

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_info['user_type']]);
        $first_leave = $this->request_leave_model->get_first_ever_record($user_id);
        $last_leave = $this->request_leave_model->get_latest_leave($user_id);
        $leave_months = get_month_and_year($first_leave, $last_leave);
        $all_leaves = $this->request_leave_model->get_all_my_leave_desc($user_id, $year);

        $birthday_leave         = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'birthday');
        $vacation_leave         = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'vacation', 1);
        $vacation_leave_half    = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'vacation', 0.5);
        $sick_leave             = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'sick', 1);
        $sick_leave_half        = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'sick', 0.5);
        $special_leave          = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'special', 1);
        $special_leave_half     = $this->request_leave_model->employee_yearly_leaves_count($user_info['id'], 'special', 0.5);

        if($birthday_leave != 0){
            $remaining_leaves = (24 - count($all_leaves)) + $birthday_leave;
            $accumulated_leaves = count($all_leaves) - $birthday_leave;
        }else{
            $remaining_leaves = 24 - count($all_leaves);
            $accumulated_leaves = count($all_leaves);
        }
        

        if(!empty($first_leave)){
            $first_leave_array = explode('-', $first_leave[0]['date']);
            for($i = date('Y'); $i >= $first_leave_array[0]; $i--){
                $years[] = $i;
            }
        }

        $first_dtr = $this->dtr_model->get_first_ever_record($user_id);
        $first_dtr_month = date('m', strtotime($first_dtr[0]['date']));
        $first_dtr_year = date('Y', strtotime($first_dtr[0]['date']));
        if($first_dtr_year < date('Y')){
            $month1 = 00;
        }else{
            $month1 = $first_dtr_month - 1;
        }

        $count_yearly_leaves = $this->request_leave_model->count_yearly_leaves($user_id, $year);
        $undertimes_compensated = $this->undertime_logs_model->get_all_by_where([
            'user_id' => $user_id, 
            'compensated' => 1,
            'YEAR(date)' => date('Y')
        ]);

        $total_compensated = compute_undertime_in_minutes($undertimes_compensated);

        // undertime plus vacation leave
        $ut_vl = $total_compensated + (($vacation_leave * 480) + (($vacation_leave_half * 0.5) * 480));
        $total_vl = $ut_vl / 480;
        
        $employee_info = $this->users_model->get_row($user_id);
        $user_info['user_type_name'] = $user_type_name;
        $user_info['employee_info'] = $employee_info;
        $user_info['employee_info']['leave_month'] = $leave_months;
        $user_info['employee_info']['years'] = $years;
        $user_info['employee_info']['selected_year'] = $year;
        $user_info['employee_info']['employee_leaves'] = $all_leaves;
        $user_info['employee_info']['employee_leaves_accumulated'] = $accumulated_leaves;
        $user_info['employee_info']['employee_leaves_remaining'] = $remaining_leaves;
        $user_info['employee_info']['employee_birthday_leave_count'] = $birthday_leave;
        $user_info['employee_info']['employee_sick_leave_count'] = $sick_leave + ($sick_leave_half * 0.5);
        $user_info['employee_info']['employee_vacation_leave_count'] = round($total_vl, 2);
        $user_info['employee_info']['employee_special_leave_count'] = $special_leave + ($special_leave_half * 0.5);
        $user_info['month1'] = $month1;

        // echo '<pre>';
        // print_r($user_info);
        // exit;

        $this->template
            ->title('Leave Requests')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/leaves.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/tableExport/tableExport.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/tableExport/libs/FileSaver/FileSaver.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/tableExport/libs/js-xlsx/xlsx.core.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/tableExport/libs/jsPDF/jspdf.umd.min.js') . '"></script>')
            ->build('index_employee_leaves', $user_info);
    }

    public function employee_list() {
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $employees = $this->users_model->get_all_desc_by_id($archive=0);
        
        foreach($employees as $key => $emp){
            $department = $this->users_type_model->get_one_by_where(['id'=>$emp["user_type"]]);
            $employees[$key]['department'] = $department['user_type'];
        }

        $user_info['employees'] = $employees;

        // echo '<pre>';
        // print_r($employees);
        // exit;
        
        $this->template
            ->title('Employees')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/employee_list.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/change_time.js') . '"></script>')
            
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('index_employee_list', $user_info);
    }

    public function leave_requests(){
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $emp_id = $this->input->get('emp_id');
        $status = $this->input->get('status');

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');
        $emp_id = isset($emp_id) ? $emp_id : null;
        $status = isset($status) ? $status : null;

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $requests = $this->request_leave_model->get_all_sorted($month, $year, $status, $emp_id);
        $latest_req_month = $this->request_leave_model->get_latest_request();
        $months = get_month_and_year([0 => ['date' => '2024-01-01']], [0 => ['date' => $latest_req_month['date']]]);
        $employees = $this->users_model->get_all_by_where_with_sort(['archive' => '0'], 'name', 'asc');


        foreach($requests as $key => $req){
            $user = $this->users_model->get_one_by_where(['id' => $req['updated_by']]);
            $requests[$key]['last_update_by'] = $user;
        }


        $user_info['pending_leaves'] = $requests;
        $user_info['months'] = $months;
        $user_info['month_year'] = [$month, $year];
        $user_info['emp_active'] = $emp_id;
        $user_info['employees'] = $employees;
        $user_info['status'] = $status;

        // echo '<pre>';
        // print_r($latest_req_month);
        // exit;


        
        $this->template
            ->title('Employees')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/kendo-ui/kendo.bootstrap.min.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/leaves.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/change_time.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/kendo.all.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/pako_deflate.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('index_leave_requests', $user_info);
    }

    public function overtime_requests(){
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $emp_id = $this->input->get('emp_id');
        $status = $this->input->get('status');

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');
        $emp_id = isset($emp_id) ? $emp_id : null;
        $status = isset($status) ? $status : null;

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $latest_req_month = $this->overtime_logs_model->get_latest_request();
        $months = get_month_and_year([0 => ['date' => '2024-06-01']], [0 => ['date' => date('Y-m-d', strtotime($latest_req_month['date_created']))]]);
        $employees = $this->users_model->get_all_by_where_with_sort(['archive' => '0'], 'name', 'asc');

        $overtimes = $this->overtime_logs_model->get_all_ot_sorted($month, $year, $emp_id, $status);
        $user_info['requests'] = $overtimes;
        $user_info['months'] = $months;
        $user_info['month_year'] = [$month, $year];
        $user_info['emp_active'] = $emp_id;
        $user_info['employees'] = $employees;
        $user_info['status'] = $status;

        // echo '<pre>';
        // print_r($user_info);
        // exit;
        
        $this->template
            ->title('Employees')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/kendo-ui/kendo.bootstrap.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/overtimes.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/kendo.all.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/pako_deflate.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('index_ot_requests', $user_info);
    }

    public function my_change_time_requests(){
        date_default_timezone_set('Asia/Manila');

        $month = $this->input->get('month');
        $year = $this->input->get('year');

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $ctrs = $this->request_change_time_model->get_all_with_user_data($month, $year, $account_session['id']);
        $latest_req_month = $this->request_change_time_model->get_latest_request();
        $months = get_month_and_year([0 => ['date' => '2024-06-01']], [0 => ['date' => date('Y-m-d', strtotime($latest_req_month['date_created']))]]);

        $user_info['change_time_requests'] = $ctrs;
        $user_info['months'] = $months;
        $user_info['month_year'] = [$month, $year];
        

        // echo '<pre>';
        // print_r($user_info);
        // exit;
        
        $this->template
            ->title('Change Time Request')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/change_time.js') . '"></script>')
            
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('index_my_ctr_request', $user_info);
    }

    public function change_time_requests(){
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $emp_id = $this->input->get('emp_id');
        $status = $this->input->get('status');

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');
        $emp_id = isset($emp_id) ? $emp_id : null;
        $status = isset($status) ? $status : null;

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $ctrs = $this->request_change_time_model->get_all_with_user_data($month, $year, $emp_id, $status);
        $latest_req_month = $this->request_change_time_model->get_latest_request();
        $months = get_month_and_year([0 => ['date' => '2024-06-01']], [0 => ['date' => date('Y-m-d', strtotime($latest_req_month['date_created']))]]);
        $employees = $this->users_model->get_all_by_where_with_sort(['archive' => '0'], 'name', 'asc');

        $user_info['change_time_requests'] = $ctrs;
        $user_info['months'] = $months;
        $user_info['month_year'] = [$month, $year];
        $user_info['emp_active'] = $emp_id;
        $user_info['employees'] = $employees;
        $user_info['status'] = $status;

        // echo '<pre>';
        // print_r($ctrs);
        // exit;
        
        $this->template
            ->title('Change Time Request')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/kendo-ui/kendo.bootstrap.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/change_time.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/kendo.all.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/pako_deflate.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('index_ctr_request', $user_info);
    }

    public function my_undertime(){
        $account_session = $this->session->userdata('account_session');
        $user = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $emp_id = $this->input->get('emp_id');
        $status = $this->input->get('status');

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');
        $emp_id = isset($emp_id) ? $emp_id : $account_session['id'];
        $status = isset($status) ? $status : null;
        $employees = $this->users_model->get_all_by_where_with_sort(['archive' => '0'], 'name', 'asc');
        $months = get_month_and_year([0 => ['date' => '2024-06-01']]);

        $undertimes = $this->undertime_logs_model->get_all_by_where_with_sort([
            'user_id' => $emp_id, 
            'status' => 'active',
            'MONTH(date)' => $month,
            'YEAR(date)' => $year
        ], 'date', 'desc');

        $undertimes_utang = $this->undertime_logs_model->get_all_by_where_with_sort([
            'user_id' => $emp_id, 
            'status' => 'active', 
            'leave' => 0,
            'compensated' => 0,
            'salary_deduction' => 0,
            'MONTH(date)' => $month,
            'YEAR(date)' => $year
        ], 'date', 'desc');

        $total_undertime = compute_undertime($undertimes_utang);

        if(in_array($user['user_type'], [1, 8, 9, 10, 11])){
            $viewed_by = 'supervisor';

            if(!$this->input->get('emp_id')){
                $undertimes = $this->undertime_logs_model->get_all_by_where_with_sort([
                    'status' => 'active',
                    'MONTH(date)' => $month,
                    'YEAR(date)' => $year
                ], 'date', 'desc');

            }else{
                $undertimes = $this->undertime_logs_model->get_all_by_where_with_sort([
                    'user_id' => $emp_id, 
                    'status' => 'active',
                    'MONTH(date)' => $month,
                    'YEAR(date)' => $year
                ], 'date', 'desc');
            }

            foreach($undertimes as $key => $ut){
                $undertimes[$key]['emp_info'] = $this->users_model->get_row($ut['user_id']);
            }
        }else{
            $viewed_by = 'employee';
            $undertimes = $this->undertime_logs_model->get_all_by_where_with_sort([
                'user_id' => $emp_id, 
                'status' => 'active'
            ], 'date', 'desc');
        }

        $data = $user;
        $data['undertime_logs'] = $undertimes;
        $data['total_undertime'] = $total_undertime;
        $data['viewed_by'] = $viewed_by;
        $data['employees'] = $employees;
        $data['viewer'] = $emp_id;
        $data['months'] = $months;
        $data['month_year'] = [$month, $year];

        // echo '<pre>';
        // print_r($data);
        // exit;
        
        $this->template
            ->title('Undertime')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/undertime.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('index_my_undertime', $data);
    }

    public function my_overtime(){
        $account_session = $this->session->userdata('account_session');
        $user = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $emp_id = $this->input->get('emp_id');
        $status = $this->input->get('status');

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');
        $emp_id = isset($emp_id) ? $emp_id : $account_session['id'];
        $status = isset($status) ? $status : null;
        $employees = $this->users_model->get_all_by_where_with_sort(['archive' => '0'], 'name', 'asc');
        $months = get_month_and_year([0 => ['date' => '2024-06-01']]);
        $ot_open = $this->overtime_logs_model->get_one_by_where(['time_out' => null, 'user_id' => $account_session['id']]);
        $holiday_checker = $this->holidays_model->get_one_by_where(['date' => date('Y-m-d')]);
        $active_ot = $this->overtime_logs_model->get_one_by_where(['time_out' => null, 'user_id' => $account_session['id']]);

        if(in_array($user['user_type'], [1, 8, 9, 10, 11])){
            $viewed_by = 'supervisor';

            if(!$this->input->get('emp_id')){
                $overtimes = $this->overtime_logs_model->get_all_by_where_with_sort([
                    'MONTH(date)' => $month,
                    'YEAR(date)' => $year
                ], 'date', 'desc');

            }else{
                $overtimes = $this->overtime_logs_model->get_all_by_where_with_sort([
                    'user_id' => $emp_id, 
                    'MONTH(date)' => $month,
                    'YEAR(date)' => $year
                ], 'date', 'desc');
            }

            foreach($overtimes as $key => $ot){
                $overtimes[$key]['emp_info'] = $this->users_model->get_row($ot['user_id']);
                $overtimes[$key]['updated_by'] = $this->users_model->get_row($ot['updated_by']);
            }
        }else{
            $viewed_by = 'employee';
            $overtimes = $this->overtime_logs_model->get_all_by_where_with_sort([
                'user_id' => $emp_id, 
                'MONTH(date)' => $month,
                'YEAR(date)' => $year
            ], 'date', 'desc');
        }

        $data = $user;
        $data['viewed_by'] = $viewed_by;
        $data['employees'] = $employees;
        $data['viewer'] = $emp_id;
        $data['months'] = $months;
        $data['month_year'] = [$month, $year];
        $data['overtimes'] = $overtimes;
        $data['holiday'] = $holiday_checker;
        $data['ot_open'] = $ot_open;
        $data['active_ot'] = $active_ot;
        
        // echo '<pre>';
        // print_r($ot_open);
        // exit;

        $this->template
            ->title('Overtime')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/overtimes.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('index_my_overtime', $data);
    }

    public function view_profile(){
        $emp_id = $this->input->get('id');
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        if(!$emp_id) show_404();

        $account_session = $this->session->userdata('account_session');
        if($account_session['user_type'] == 1){
            $branch = 'Baguio City';
        }elseif($account_session['user_type'] == 5){
            $branch = 'Quezon City';
        }

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');
        $employee = $this->users_model->get_row($emp_id);
        $user_type = $this->users_type_model->get_row($employee['user_type']);
        $first_temp_sched = $this->user_temporary_schedule_model->get_earliers_record($emp_id);

        $data = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $data['employee'] = $employee;
        $data['employee']['schedule'] = $this->user_schedule_model->get_one_by_where(['user_id' => $emp_id]);
        $data['employee']['user_type'] = $user_type['user_type'];
        $data['employee']['temp_schedule'] = $this->user_temporary_schedule_model->get_temp_schedule_for_month($emp_id, $year, $month);
        $data['user_roles'] = $this->users_type_model->get_all();
        $data['months'] = $first_temp_sched != NULL ? get_month_and_year([$first_temp_sched]) : get_month_and_year([['date' => date('Y-m-d')]]);
        $data['current_month_year'] = $month .'-'. $year;

        // echo '<pre>';
        // print_r($data );
        // exit;   

        $this->template
            ->title('Employee Profile')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/employee_profile.js') . '"></script>')
            ->build('employee_profile', $data);
    }
}