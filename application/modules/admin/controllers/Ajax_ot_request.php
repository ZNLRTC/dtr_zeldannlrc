    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_ot_request extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'users_model',
            'request_ot_model',
        ]);
        
        if(!$this->input->is_ajax_request()){ show_404(); }
    }

    public function fetch_ot_request(){
        $rot_info = $this->request_ot_model->get_row($_POST['rot_id']);
        if($rot_info){
            $response = [
                "response_status" => "success",
                "user_id"         => $rot_info['user_id'],
                "type"            => $rot_info['type'],
                "task"            => $rot_info['task'],
                "date"            => $rot_info['date'],
                "time"            => $rot_info['time'],
                "status"          => $rot_info['status'],
                "reason_denied"   => $rot_info['reason_denied'],
                "date_created"    => $rot_info['date_created'],
                "date_updated"    => $rot_info['date_updated']
            ];
        }else{
            $response = [
                "response_status"  => "error",
                "message" => "An error has occurred: Unable to read salary grade"
            ];
        }

        echo json_encode($response); 
    }

    public function update_ot_request(){
        $reason_denied = NULL;
        if($_POST['reason-denied'] !== ""){ $reason_denied = $_POST['reason-denied']; }

        try{
            $data = [
                "status"        => $_POST['status'],
                "reason_denied" => $reason_denied,
                "date_updated"  => date('Y-m-d h:i:s')
            ];

            $this->request_ot_model->update($_POST['rot_id'],$data);

            if($this->db->trans_status()){
                $this->db->trans_commit();
                $pending = $this->request_ot_model->count_by_where(['status' => 'pending']);
                $response = [
                    "status"  => 'success',
                    "message" => 'Request Updated Successfully',
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

    public function otr_details(){
        $otr  = $this->request_ot_model->get_row($_POST['otr_id']);
        $user = $this->users_model->get_row($otr['user_id']);
        echo '
            <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Name: </label></div>
                <div class="col-md-7"><div class="form-control">'.$user['name'].'</div></div>
            </div>

            <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Date: </label></div>
                <div class="col-md-7"><div class="form-control">'.date( "M d, Y (D)",strtotime($otr['date'])).'</div></div>
            </div>

            <div class="row"><div class="col-md-10 offset-md-1"><hr></div></div>

            <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label class="w-100">Tasks: </label></div>
                <div class="col-md-7"><div class="form-control">';
                    $task = explode(PHP_EOL, $otr['task']);
                    for($a=0;$a<count($task);$a++){
                        echo "&#x2022; ".$task[$a]."<br>";
                    }
                echo'</div></div>
            </div>

            <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label class="w-100">Status: </label></div>
                <div class="col-md-7">';
                    if($otr['status'] == "pending"):
                        echo "<span class='t-green'>Pending</span>";
                    elseif($otr['status'] == "approved"):
                        echo "<span class='t-blue'>Approved</span>";
                    elseif($otr['status'] == "denied"):
                        echo "<span class='t-red'>Denied</span>";
                    endif;
                echo'</div>
            </div>
        ';
    }
}