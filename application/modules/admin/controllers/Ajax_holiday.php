    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_holiday extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model(['holidays_model']);
        if(!$this->input->is_ajax_request()){ show_404(); }
    }

    public function fetch_holiday(){
        $h_info = $this->holidays_model->get_row($_POST['h_id']);
        if($h_info){
            $response = [
                "status"       => "success",
                "name"         => $h_info['name'],
                "date"         => $h_info['date'],
                "type"         => $h_info['type'],
                "fdc_type"     => $h_info['fdc_type'],
                "date_created" => $h_info['date_created'],
                "date_updated" => $h_info['date_updated']
            ];
        }else{
            $response = [
                "status"  => "error",
                "message" => "An error has occurred: Unable to read salary grade"
            ];
        }

        echo json_encode($response); 
    }

    public function add_custom_holiday(){
        $this->form_validation
             ->set_rules("name","name","required")
             ->set_rules("date","date","required|callback_validate_date")
             ->set_rules("type","type","required")
             ->set_message("required","required");

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                $data = [
                    'name'     => $_POST['name'],
                    'date'     => $_POST['date'],
                    'type'     => $_POST['type'],
                    'fdc_type' => "custom"
                ];
                $this->holidays_model->add($data);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response = [
                        "status"       => 'success',
                        "message"      => 'Custom Holiday Added Successfully'
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

    public function update_custom_holiday(){
        $this->form_validation
             ->set_rules("name","name","required")
             ->set_rules("date","date","required|callback_validate_date_update")
             ->set_rules("type","type","required")
             ->set_message("required","required");

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                $data = [
                    'name'         => $_POST['name'],
                    'date'         => $_POST['date'],
                    'type'         => $_POST['type'],
                    'date_updated' => date('Y-m-d h:i:s')
                ];
                $this->holidays_model->update($_POST['h_id'],$data);

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response = [
                        "status"  => 'success',
                        "message" => 'Custom Holiday Updated Successfully',
                        "name"    => $_POST['name'],
                        "date"    => date( "M d, Y (D)",strtotime( $_POST['date'])),
                        "type"    => ($_POST['type'] == "regular" ? "Regular Holiday" : "Special Non-working Holiday")
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

    public function validate_date(){
        $where = ["date" => $_POST['date']];
        $count = $this->holidays_model->count_by_where($where);
        if($count == 1){
            $this->form_validation->set_message('validate_date','Already exist');
            return false; 
        }
        return true;
    }

    public function validate_date_update(){
        $where = ["id !=" => $_POST['h_id'],"date" => $_POST['date']];
        $count = $this->holidays_model->count_by_where($where);
        if($count == 1){
            $this->form_validation->set_message('validate_date_update','Already exist');
            return false; 
        }
        return true;
    }

    public function delete_custom_holiday(){
        try {
            
            $this->holidays_model->delete($_POST['h_id']);

            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response = [
                    "status"       => 'success',
                    "message"      => 'Deletion Successful'
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

    public function update_dynamic_holiday(){
        $easter =  date("Y-m-d", easter_date(date("Y")));
        $dy_array = [
            ["Maundy Thursday", date('Y-m-d', strtotime($easter. ' - 3 days'))],
            ["Good Friday", date('Y-m-d', strtotime($easter.' - 2 days'))],
            ["Black Saturday", date('Y-m-d', strtotime($easter." - 1 day"))],
            ["Easter Sunday", date('Y-m-d', strtotime($easter))]
        ];
        $dy = $this->holidays_model->get_all_by_where(['fdc_type' => "dynamic"]);
        $data = [];
        foreach($dy as $dy){
            for($a=0;$a<count($dy_array);$a++){
                if($dy["name"] == $dy_array[$a][0]){
                    array_push($data,['id'=>$dy['id'],'date'=>$dy_array[$a][1]]);
                }
            }
        }
        try {
            $this->holidays_model->update_dynamic_holidays($data);

            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response = [
                    "status"  => 'success',
                    "message" => 'Update Successful'
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
}