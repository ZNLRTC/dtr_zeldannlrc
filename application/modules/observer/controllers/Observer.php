<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Observer extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'users_model',
            'users_type_model',
            'salary_grade_model',
            'request_ot_model',
            'dtr_model',
            'holidays_model',
            'overtime_logs_model',
            'request_leave_model',
            'request_dtr_update_model',
            'user_schedule_model',
            'user_temporary_schedule_model',
            'undertime_logs_model',
        ]);

        $this->load->helper('general_helper');

        if($this->session->has_userdata('is_logged_in')){
            $type = $this->session->userdata('is_logged_in');
            if($type["type"] !== "observer"){
                header("Location: ".base_url($type['type']));
            }
        }else {
            header("Location: ".base_url());
        }
    }

    public function index() {
        date_default_timezone_set('Asia/Manila');
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        
        $employees = $this->users_model->get_all_employees_for_observer();
        foreach($employees as $key => $e){
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

            $user_type = $this->users_type_model->get_row($e['user_type']);
            $employees[$key]['user_type_info'] = $user_type;
        }

        // echo '<pre>';
        // print_r($employees);
        // exit;

        $user_info['employees'] = $employees;

        $this->template
            ->title('Employee Daily Time Record')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/active_dtr.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('observer_index', $user_info);
    }

    public function view() {
        date_default_timezone_set('Asia/Manila');

        $id = $this->input->get('id');
        $user = $this->users_model->get_row($id);
        $user_type = $this->users_type_model->get_row($user['user_type']);
        $fixed_schedule = $this->user_schedule_model->get_one_by_where(['user_id' => $id]);
        $temp_schedule = $this->user_temporary_schedule_model->get_temp_schedule_for_month($id, date('Y'), date('m'));
        $leaves = $this->request_leave_model->get_all_my_leave_desc($id, date('Y'));

        foreach($temp_schedule as $key => $l){
            $sched = $this->user_schedule_model->get_one_by_where(['user_id' => $id]);
            $day = strtolower(date('l', strtotime($l['date'])));
            $temp_schedule[$key]['from_time'] = $sched[$day];
            $temp_schedule[$key]['from_workbase'] = $sched[$day . '_workbase'];
        }

        $user['user_type_info'] = $user_type;
        $user['fixed_schedule'] = $fixed_schedule;
        $user['temp_schedule'] = $temp_schedule;
        $user['leaves'] = $leaves;
        

        // echo '<pre>';
        // print_r($user);
        // exit;

        $this->template
            ->title('Employee Profile')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/main_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/desktop_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/tablet_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/employee/css/mobile_employee.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/employee/js/active_dtr.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('observer_view_employee', $user);
    }


}