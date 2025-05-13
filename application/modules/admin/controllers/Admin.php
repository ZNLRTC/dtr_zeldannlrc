<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Admin extends CI_Controller {

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
            'undertime_logs_model'
        ]);

        $this->load->helper('general_helper');

        if($this->session->has_userdata('is_logged_in')){
            $type = $this->session->userdata('is_logged_in');
            if($type["type"] !== "admin"){
                header("Location: ".base_url($type['type']));
            }
        }else {
            header("Location: ".base_url());
        }
    }

    public function index_dtr_list() {
        $month = $this->input->get('month');
        $year = $this->input->get('year');

        $month ? $month = $month : $month = date('m');
        $year ? $year = $year : $year = date('Y');

        $account_session = $this->session->userdata('account_session');
        $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$account_session['user_type']]);

        $dtr_list_first_record = $this->dtr_model->get_first_ever_record();

        $dtr = ($user_type_name['user_type'] == "admin") ? $this->dtr_model->get_all_by_group_desc($month, $year) : $this->dtr_model->get_all_qc_by_group($month, $year);
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $user_info['user_type_name'] = $user_type_name;
        $user_info['dtr_month'] = get_month_and_year($dtr_list_first_record);
        $user_info['count'] = count($dtr);
        $user_info['dtrs'] = $dtr;
        $user_info['month_year'] = [$month, $year];
        $user_info['dtr_request_count'] = $this->request_dtr_update_model->count_by_where(['status' => 'pending']);

        // echo '<pre>';
        // print_r($user_info);
        // exit;

        $this->template
            ->title('DTR List')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('index_dtr_list', $user_info);
    }

    public function employee_dtr() {

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $employee_id = $this->input->get('user_id');
        $month = $this->input->get('month');
        $year = $this->input->get('year');
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
            ->title('Daily Time Record')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/moment.min.js') . '"></script>')
            
            ->build('employee_dtr', $user_info);
    }

    public function archived_employee() {
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $this->template
            ->title('Archived Employees')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('archived_employees', $user_info);
    }

    public function salary_grade() {
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $this->template
            ->title('Salary Grade')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('salary_grade', $user_info);
    }

    public function overtime_requests() {
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $this->template
            ->title('Overtime Requests')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('overtime_requests', $user_info);
    }

    public function index() {
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_info['user_type']]);

        $dtrs = ($user_type_name['user_type'] == "admin") ? $this->dtr_model->get_all_active_dtr_desc() : $this->dtr_model->get_all_active_qc_dtr();
        foreach ($dtrs as $key => $dtr){
            $schedule = $this->user_schedule_model->get_one_by_where(['user_id' => $dtr['user_id']]);
            $temporary_schedule = $this->user_temporary_schedule_model->get_temp_schedule_for_month($dtr['user_id'], date('Y'), date('m'));
            $temp_schedule = [];

            foreach($temporary_schedule as $sched){
                if($sched['date'] == date('Y-m-d')){
                    array_push($temp_schedule, $sched);
                }
            }

            if(!empty($temp_schedule)){
                $dtrs[$key]['current_schedule'] = $temp_schedule[0];
            }else{
                $dtrs[$key]['current_schedule'] = ['date' => date('Y-m-d'), 'in-out' => $schedule[strtolower(date('l'))], 'workbase' => $schedule[strtolower(date('l')).'_workbase']];
            }
        }

        $user_info['user_type_name'] = $user_type_name;
        $user_info['dtrs'] = $dtrs;

        // echo '<pre>';
        // print_r($user_info);
        // exit;

        $this->template
            ->title('Active DTR')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('active_dtr', $user_info);
    }

    public function employee_list() {
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        
        $this->template
            ->title('Active Employee')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('employee_list', $user_info);
    }

    public function holidays() {
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_info['user_type']]);
        $h_list = $this->holidays_model->get_all_sort_date();

        $user_info['user_type_name'] = $user_type_name;
        $user_info['holiday_lists'] = $h_list;

        // echo '<pre>';
        // print_r($user_info);
        // exit;

        $this->template
            ->title('Holidays')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('holidays', $user_info);
    }

    public function custom_holidays() {
        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $this->template
            ->title('Custom Holidays')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('custom_holidays', $user_info);
    }

    public function leave_requests() {

        $month = $this->input->get('month');
        $year = $this->input->get('year');

        $month ? $month = $month : 0;
        $year ? $year = $year : 0;

        $account_session = $this->session->userdata('account_session');
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $leave_list_first_record = $this->request_leave_model->get_first_ever_record();
        $leave_list_last_record = $this->request_leave_model->get_latest_leave();
        $leave_months = get_month_and_year($leave_list_first_record, $leave_list_last_record);

        $employees = $this->users_model->get_all_employee_names();
        $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_info['user_type']]);
        $leave_requests = ($user_type_name['user_type'] == "admin") ? $this->request_leave_model->get_all_leave($month, $year) : $this->request_leave_model->get_all_qc_leave();

        $user_info['user_type_name'] = $user_type_name;
        $user_info['employees'] = $employees;
        $user_info['leave_requests'] = $leave_requests;
        $user_info['leave_month'] = $leave_months;
        $user_info['month_year'] = [$month, $year];

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
            ->prepend_metadata('<link href="' . versionAsset('plugins/kendo-ui/kendo.bootstrap.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/select2/select2.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/kendo.all.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/pako_deflate.min.js') . '"></script>')
            ->build('leave_requests', $user_info);
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
        // print_r($first_dtr_month);
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
            ->prepend_metadata('<link href="' . versionAsset('plugins/kendo-ui/kendo.bootstrap.min.css') . '" rel="stylesheet" type="text/css">')
            ->append_metadata('<script src="' . versionAsset('assets_module/universal/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/tableExport/tableExport.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/tableExport/libs/FileSaver/FileSaver.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/tableExport/libs/js-xlsx/xlsx.core.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/tableExport/libs/jsPDF/jspdf.umd.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/kendo.all.min.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/kendo-ui/pako_deflate.min.js') . '"></script>')
            ->build('employee_leaves', $user_info);
    }

    public function dtr_update_request() {
        $account_session = $this->session->userdata('account_session');
        if($account_session['user_type'] == 1){
            $branch = 'Baguio City';
        }elseif($account_session['user_type'] == 5){
            $branch = 'Quezon City';
        }

        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $month ? $month = $month : $month = date('m');
        $year ? $year = $year : $year = date('Y');

        $dtr_list_first_record = $this->request_dtr_update_model->get_one_earliest();
        $dtr_list_array = explode(' ', $dtr_list_first_record['date_created']);
        $dtr_earliest_date = [['date' => $dtr_list_array[0]]];
        $user_info = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

        $rdtr = $this->request_dtr_update_model->read_all_requests($month, $year, $branch);

        $user_info['dtr_month'] = get_month_and_year($dtr_earliest_date);
        $user_info['month_year'] = [$month, $year];
        $user_info['rdtr'] = $rdtr;

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
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/main.js') . '"></script>')
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('dtr_update_request', $user_info);
    }

    public function undertimes(){
        $account_session = $this->session->userdata('account_session');
        if($account_session['user_type'] == 1){
            $branch = 'Baguio City';
        }elseif($account_session['user_type'] == 5){
            $branch = 'Quezon City';
        }

        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $month ? $month = $month : $month = date('m');
        $year ? $year = $year : $year = date('Y');

        $dtr_list_first_record = $this->request_dtr_update_model->get_one_earliest();
        $dtr_list_array = explode(' ', $dtr_list_first_record['date_created']);
        $dtr_earliest_date = [['date' => $dtr_list_array[0]]];
        $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$account_session['user_type']]);

        $data = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $data['dtr_month'] = get_month_and_year($dtr_earliest_date);
        $data['month_year'] = [$month, $year];
        $data['user_type_name'] = $user_type_name;

        // echo '<pre>';
        // print_r($data);
        // exit;

        $this->template
            ->title('Undertime Records')
            ->set_layout('user_main')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/main.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/main_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/desktop_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/tablet_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/admin/css/mobile_admin.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('assets_module/universal/css/modal.css') . '" rel="stylesheet" type="text/css">')
            ->prepend_metadata('<link href="' . versionAsset('plugins/datatables/datatables.min.css') . '" rel="stylesheet" type="text/css">')
            
            ->append_metadata('<script src="' . versionAsset('plugins/datatables/datatables.min.js') . '"></script>')
            ->build('undertime', $data);
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
        // print_r($data['months'] );
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

    public function overtime(){
        $account_session = $this->session->userdata('account_session');
        if($account_session['user_type'] == 1){
            $branch = 'Baguio City';
        }elseif($account_session['user_type'] == 5){
            $branch = 'Quezon City';
        }

        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $emp_id = $this->input->get('emp_id');
        $status = $this->input->get('status');

        $month = isset($month) ? $month : date('m');
        $year = isset($year) ? $year : date('Y');
        $emp_id = isset($emp_id) ? $emp_id : NULL;
        $status = isset($status) ? $status : null;
        $months = get_month_and_year([0 => ['date' => '2024-06-01']]);

        $employees = $this->users_model->get_all_by_where_with_sort(['archive' => '0'], 'name', 'asc');
        $ot_open = $this->overtime_logs_model->get_one_by_where(['time_out' => null]);
        $holiday_checker = $this->holidays_model->get_one_by_where(['date' => date('Y-m-d')]);
        $active_ot = $this->overtime_logs_model->get_one_by_where(['time_out' => null]);

        if($emp_id != NULL){
            $overtimes = $this->overtime_logs_model->get_all_by_where_with_sort([
                'user_id' => $emp_id,
                'MONTH(date)' => $month,
                'YEAR(date)' => $year
            ], 'date', 'desc');
        }else{
            $overtimes = $this->overtime_logs_model->get_all_by_where_with_sort([
                'MONTH(date)' => $month,
                'YEAR(date)' => $year
            ], 'date', 'desc');
        }
        

        foreach($overtimes as $key => $ot){
            $user = $this->users_model->get_row($ot['user_id']);
            $updated_by = $this->users_model->get_row($ot['updated_by']);
            $overtimes[$key]['emp_name'] = $user['name']; 
            $overtimes[$key]['updated_by'] = $updated_by['name'];

            if($ot['type'] == 'holiday'){
                $h = $this->holidays_model->get_one_by_where(['date' => $ot['date']]);
                $overtimes[$key]['holiday_desc'] = $h['name'];
            }
        }

        $data = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $data['employees'] = $employees;
        $data['months'] = $months;
        $data['month_year'] = [$month, $year];
        $data['overtimes'] = $overtimes;
        $data['holiday'] = $holiday_checker;
        $data['ot_open'] = $ot_open;
        $data['active_ot'] = $active_ot;
        $data['viewed_emp'] = $emp_id;

        // echo '<pre>';
        // print_r($data['holiday']);
        // exit;

        $this->template
            ->title('Employee Overtime')
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
            ->build('employee_overtime', $data);
    }

    public function undertime(){
        $account_session = $this->session->userdata('account_session');
        if($account_session['user_type'] == 1){
            $branch = 'Baguio City';
        }elseif($account_session['user_type'] == 5){
            $branch = 'Quezon City';
        }

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

        $data = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);
        $data['months'] = $months;
        $data['employees'] = $employees;
        $data['month_year'] = [$month, $year];
        $data['viewed_emp'] = $emp_id;
        $data['undertime_logs'] = $undertimes;

        // echo '<pre>';
        // print_r($overtimes);
        // exit;

        $this->template
            ->title('Employee Undertime')
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
            ->append_metadata('<script src="' . versionAsset('assets_module/admin/js/undertime.js') . '"></script>')
            ->build('employee_undertime', $data);
    }

}