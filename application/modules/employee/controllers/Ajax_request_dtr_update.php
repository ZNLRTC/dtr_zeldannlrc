    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_request_dtr_update extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'dtr_model',
            'users_model',
            'request_dtr_update_model'
        ]);
        //if(!$this->input->is_ajax_request()){ show_404(); }
    }

    public function read_dtr_update_request(){
        $rdtr = $this->request_dtr_update_model->get_row($_POST['rdtr_id']);
        if($rdtr){
            $response = [
                "status"         => "success",
                "dtr_id"         => $rdtr['dtr_id'],
                "message"        => $rdtr['message'],
                "request_status" => $rdtr['status'],
                "date_created"   => $rdtr['date_created'],
                "date_updated"   => $rdtr['date_updated']
            ];
        }else{
            $response = [
                "status"  => "error",
                "message" => "An error has occurred: Unable to read request"
            ];
        }
        echo json_encode($response); 
    }

    public function read_full_dtr_update_request(){
        $rdtr = $this->request_dtr_update_model->read_full_dtr_update_request($_POST['rdtr_id']);
        $ti_wb = $rdtr['time_in_work_base'];
        $bo_wb = $rdtr['break_out_work_base'];

        if($ti_wb == NULL && $bo_wb == NULL){
            $workbase = explode('/', $rdtr['work_base']);
            if(count($workbase) == 2){
                $ti_wb = $workbase[0];
                $bo_wb = $workbase[1];
            }else{
                $ti_wb = $workbase[0];
                $bo_wb = $workbase[0];
            }
            
        }

        if($rdtr){
            $response = [
                "status"                => "success",
                "name"                  => $rdtr['name'],
                "message"               => $rdtr['message'],
                "date"                  => $rdtr['date'],
                "time_in"               => $rdtr['time_in'],
                "break"                 => $rdtr['break'],
                "time_out"              => $rdtr['time_out'],
                "time_in_work_base"     => $ti_wb,
                "break_out_work_base"   => $bo_wb,
                "shift"                 => $rdtr['shift_reason'],
                "end_of_day"            => $rdtr['end_of_day'],
                "request_status"        => $rdtr['status'],
                "reason_denied"         => $rdtr['reason_denied'],
                "overtime"              => $rdtr['overtime']
            ];
        }else{
            $response = [
                "status"  => "error",
                "message" => "An error has occurred: Unable to read request"
            ];
        }
        echo json_encode($response); 
    }

    public function send_request(){
        $this->form_validation
             ->set_rules('message', 'message', 'required')
             ->set_message('required','required');

        if($this->form_validation->run() == FALSE):
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        else:
            try {
                $this->request_dtr_update_model->add(['dtr_id'=>$_POST['dtr_id'],'message'=>$_POST['message']]);
               
                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response['status'] = 'success';
                    $response['message'] = "request sent successfully";
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
        endif;
        echo json_encode($response);
    }

    public function update_status(){
        $user_t = $this->users_model->get_row($_POST['user_id']);
        $input = $this->input->post();


        if($input['status'] == 'approved'){

            $this->form_validation
                ->set_rules('time-in-workbase', 'Time-in Workbase', 'required')
                ->set_rules('break-out-workbase', 'Break-out Workbase', 'required')
                ->set_rules('time-in', 'Time-in', 'required')
                ->set_rules('break-in', 'Break-in', 'required')
                ->set_rules('time-in', 'Time-in', 'required')
                ->set_rules('eod-report', 'End of Day Report', 'required');

            if($input['overtime-in'] != ""){
                $this->form_validation->set_rules('overtime-out', 'Overtime-out', 'required');
            }

            if($this->form_validation->run() == FALSE){
                $response['status'] = 'form-incomplete';
                $response['errors'] = $this->form_validation->error_array();
            }else{
                try{
                    $e_workbase         = [$input['time-in-workbase'], $input['break-out-workbase']];
                    $i_workbase         = implode('/', $e_workbase);
                    $request_dtr_id     = $input['rdtr_id'];
                    $dtr                = $this->request_dtr_update_model->get_row($request_dtr_id);
                    $dtr_id             = $dtr['dtr_id'];
        
                    $row_dtr = array(
                        'time_in'               => $input['time-in'],
                        'time_in_work_base'     => $input['time-in-workbase'],
                        'break'                 => $input['break-in'].'-'.$input['break-out'],
                        'break_out_work_base'   => $input['break-out-workbase'],
                        'time_out'              => $input['time-out'],
                        'work_base'             => $i_workbase,
                        'overtime'              => ($input['overtime-in'] != "") ? $input['overtime-in'].'-'.$input['overtime-out'] : null,
                        'end_of_day'            => $input['eod-report'],
                        'date_updated'          => date('Y-m-d H:i:s')
                    );
        
                    $row_dtr_update = array(
                        'status'            => $input['status'],
                        'reason_denied'     => ($input['status'] == 'denied') ? $input['reason-denied'] : NULL,
                        'date_updated'      => date('Y-m-d H:i:s')
                    );
        
                    $this->dtr_model->update($dtr_id, $row_dtr);
                    $this->request_dtr_update_model->update($request_dtr_id, $row_dtr_update);
                    $count_pending = $this->request_dtr_update_model->count_by_where(['status' => 'pending']);
        
                    if($this->db->trans_status()){
                        $this->db->trans_commit();
                        $response['status']  = 'success';
                        $response['pending_count'] = $count_pending;
                        $response['message'] = "Request updated successfully";
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
        }elseif($input['status'] == 'denied'){
            $this->form_validation->set_rules('reason-denied', 'Reason Denied', 'required');

            if($this->form_validation->run() == FALSE){
                $response['status'] = 'form-incomplete';
                $response['errors'] = $this->form_validation->error_array();
            }else{
                try{
                    $request_dtr_id     = $input['rdtr_id'];
        
                    $row_dtr_update = array(
                        'status'            => $input['status'],
                        'reason_denied'     => ($input['status'] == 'denied') ? $input['reason-denied'] : NULL,
                        'date_updated'      => date('Y-m-d H:i:s')
                    );
        
                    $this->request_dtr_update_model->update($request_dtr_id, $row_dtr_update);
                    $count_pending = $this->request_dtr_update_model->count_by_where(['status' => 'pending']);
        
                    if($this->db->trans_status()){
                        $this->db->trans_commit();
                        $response['status']  = 'success';
                        $response['pending_count'] = $count_pending;
                        $response['message'] = "Request updated successfully";
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
        }
        
        echo json_encode($response);
    }

}