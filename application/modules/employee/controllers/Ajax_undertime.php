    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_undertime extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'dtr_model',
            'undertime_logs_model',
            'request_leave_model',
            'undertime_logs_model'
        ]);
        if(!$this->input->is_ajax_request()){ show_404(); }
    }

    public function compute_undertime_remaining($emp_id)
    {
        $undertimes_utang = $this->undertime_logs_model->get_all_by_where([
            'user_id' => $emp_id, 
            'status' => 'active', 
            'leave' => 0,
            'compensated' => 0,
            'salary_deduction' => 0
        ]);

        return compute_undertime($undertimes_utang);
    }

    public function is_leave(){
        $post = $this->input->post();
        $ut = $this->undertime_logs_model->get_row($post['id']);
        $this->undertime_logs_model->update($post['id'], ['leave' => $post['status']]);

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['message'] = "Updated successfully";
            $response['remaining'] = $this->compute_undertime_remaining($ut['user_id']);
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }

        echo json_encode($response);
    }

    public function is_compensated(){
        $post = $this->input->post();
        $ut = $this->undertime_logs_model->get_row($post['id']);
        $this->undertime_logs_model->update($post['id'], ['compensated' => $post['status']]);

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['message'] = "Updated successfully";
            $response['remaining'] = $this->compute_undertime_remaining($ut['user_id']);
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }

        echo json_encode($response);
    }

    public function for_salary_deduction(){
        $post = $this->input->post();
        $ut = $this->undertime_logs_model->get_row($post['id']);
        $this->undertime_logs_model->update($post['id'], ['salary_deduction' => $post['status']]);

        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response['status'] = 'success';
            $response['message'] = "Updated successfully";
            $response['remaining'] = $this->compute_undertime_remaining($ut['user_id']);
        }else{
            $this->db->trans_rollback();
            $response['status'] = 'error';
            $response['message'] = $this->db->error();
        }

        echo json_encode($response);
    }

    public function add_undertime(){
        $account_session = $this->session->userdata('account_session');
        $employee = $this->input->post('employee');
        $date = $this->input->post('date');
        $sched_time_in = $this->input->post('sched-time-in');
        $sched_time_out = $this->input->post('sched-time-out');
        $time_in = $this->input->post('time-in');
        $break_in = $this->input->post('break-in');
        $break_out = $this->input->post('break-out');
        $time_out = $this->input->post('time-out');
        $sched_in = new DateTime($sched_time_in);
        $sched_out = new DateTime($sched_time_out);
        $get_schedule_hours = $sched_in->diff($sched_out);
        $schedule_minutes = $get_schedule_hours->h * 60;
        $dtr = $this->dtr_model->get_one_by_where(['user_id' => $employee, 'date' => $date]);

        $this->form_validation
            ->set_rules('employee', 'Employee', 'required|trim')
            ->set_rules('date', 'Date', 'required|trim')
            ->set_rules('sched-time-in', 'Time-in Schedule', 'required|trim')
            ->set_rules('sched-time-out', 'Time-out Schedule', 'required|trim')
            ->set_rules('time-in', 'Time-in', 'required|trim')
            ->set_rules('time-out', 'Time-out', 'required|trim');

        if(!$dtr){
            $response = [
                'status' => 'dtr-not-found',
                'message' => 'There is no DTR for selected Employee and Date.'
            ];
        }elseif($this->form_validation->run() == FALSE ){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{

            try{

                if(($break_in == NULL || $break_in == "") && ($break_out == NULL || $break_out == "")){
                    $break_in = NULL;
                    $break_out = NULL;
                }
                
                $worked_hrs = compute_total_worked_hours($date, $time_in, $time_out, $break_in, $break_out, $sched_time_in, $sched_time_out, $dtr['schedule_workbase'], $schedule_minutes);
                $ut_checker = $this->undertime_logs_model->get_one_by_where(['user_id' => $employee,'date' => $date]);

                if($ut_checker){
                    $response['status'] = 'existing-record';
                    $response['message'] = 'Cannot add record. There is an existing record for entered date.';
                    $response['data'] = $ut_checker;

                    echo json_encode($response);
                    exit;
                }

                $row = array(
                    'user_id'       => $employee,
                    'date'          => $date,
                    'time'          => '-'.$worked_hrs['total_under_time'],
                    'updated_by'    => $account_session['id']
                );

                $id = $this->undertime_logs_model->add($row);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = "success";
                    $response['message'] = "Added Successfully";
                    $response['data'] = $this->undertime_logs_model->get_row($id);
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

    public function delete_undertime(){
        $id = $this->input->post('id');

        // echo '<pre>';
        // print_r($id);
        // exit;

        try {
            $this->undertime_logs_model->delete($id);
                
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response['status'] = 'success';
                $response['message'] = 'Undertime Deleted Successfully';
                $response['id'] = $id;
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