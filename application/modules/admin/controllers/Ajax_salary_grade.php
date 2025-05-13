    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_salary_grade extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model([
            'users_model',
            'users_type_model',
            'salary_grade_model'
        ]);
        
        if(!$this->input->is_ajax_request()){ show_404(); }
    }

    public function fetch_salary_grade(){
        $sg_info = $this->salary_grade_model->get_row($_POST['sg_id']);
        if($sg_info){
            $response = [
                "status"        => "success",
                "grade_number"  => $sg_info['grade_number'],
                "hourly_rate"   => $sg_info['hourly_rate'],
                "date_created"  => $sg_info['date_created'],
                "date_updated"  => $sg_info['date_updated']
            ];
        }else{
            $response = [
                "status"  => "error",
                "message" => "An error has occurred: Unable to read salary grade"
            ];
        }

        echo json_encode($response); 
    }

    public function fetch_salary_grade_employees(){
        $emp = $this->users_model->get_all_by_where(["salary_grade"=>$_POST['sg_id']]);
        if($emp){
            echo " <table class='table table-striped m-0'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class='text-center'>User Type</th>
                    </tr>
                </thead>
                <tbody>";
                    foreach($emp as $emp){
                        $user_type = $this->users_type_model->get_row($emp['user_type']);
                        echo "
                            <tr>
                                <td>{$emp['name']}</td>
                                <td class='text-center'>{$user_type['user_type']}</td>
                            </tr>
                        ";
                    }
                echo "</tbody>
            </table>";
        }else{
            echo "<div class='text-center'>No employee with this salary grade</div>";
        }
    }

    public function au_salary_grade(){
        $this->form_validation
             ->set_rules("salary-grade","salary-grade","required|numeric|callback_validate_grade_number")
             ->set_rules("hourly-rate","hourly-rate","required|numeric|callback_validate_hourly_rate")
             ->set_message("required","required")
             ->set_message("numeric","Enter numeric value");

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            try {
                $data = [
                    'grade_number'  => $_POST['salary-grade'],
                    'hourly_rate'   => $_POST['hourly-rate'],
                    'date_updated'  => date('Y-m-d h:i:s')
                ];

                $message = 'Salary Grade Updated Successfully'; //Default, since updating is more often than adding;
                if(isset($_POST['sg_id'])){
                    $this->salary_grade_model->update($_POST['sg_id'],$data);
                    $message = 'Salary Grade Updated Successfully';
                    $sg_id = $_POST['sg_id'];
                }else{
                    $sg_id = $this->salary_grade_model->add($data);
                    $message = 'Salary Grade Added Successfully';
                }

                if($this->db->trans_status()){
                    $this->db->trans_commit();
                    $response = [
                        "status"       => 'success',
                        "sg_id"        => $sg_id,
                        "message"      => $message,
                        'grade_number' => $_POST['salary-grade'],
                        'hourly_rate'  => $_POST['hourly-rate']
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

    public function validate_grade_number(){
        if(isset($_POST['sg_id'])){//means update
            $where = ["id !=" => $_POST['sg_id'], "grade_number" => $_POST['salary-grade']];
        }else{//add
            $where = ["grade_number" => $_POST['salary-grade']];
        }
        $this->form_validation->set_message('validate_grade_number','Already exist');
        $count = $this->salary_grade_model->count_by_where($where);
        if($count == 1){ return false; }
        return true;
    }

    public function validate_hourly_rate(){
        if(isset($_POST['sg_id'])){//means update
            $where = ["id !=" => $_POST['sg_id'], "hourly_rate" => $_POST['hourly-rate']];
        }else{//add
            $where = ["hourly_rate" => $_POST['hourly-rate']];
        }
        $this->form_validation->set_message('validate_hourly_rate','Must be unique per grade number');
        $count = $this->salary_grade_model->count_by_where($where);
        if($count == 1){ return false; }
        return true;
    }

    public function delete_salary_grade(){
        try {
            
            $this->salary_grade_model->delete($_POST['sg_id']);

            if($this->db->trans_status()){
                $this->db->trans_commit();
                $response = [
                    "status"       => 'success',
                    "message"      => 'Salary Grade Deleted Successfully'
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