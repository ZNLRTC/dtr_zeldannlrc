<?php

    class Request_leave_model extends Crud {



        public function __construct(){

            parent::__construct('request_leave', 'id');

        }

        public function get_all_leave($month, $year){

            if($month != 0) $this->db->where('MONTH(date)', $month);
            if($year != 0) $this->db->where('YEAR(date)', $year);

            $this->db->select('*')

                ->from('users as t1')
                ->join('request_leave as t2','t1.id = t2.user_id')
                ->order_by("t2.date", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }


        public function get_all_my_leave_desc($user_id, $year){

            $this->db->select('t1.*, t2.name as approved_by_name')
                ->from('request_leave as t1')
                ->join('users as t2', 't1.updated_by = t2.id')
                ->where('user_id', $user_id)
                ->where('YEAR(date)', $year)
                ->order_by('date','desc');
            $query = $this->db->get();
            return $query->result_array();

        }



        public function get_all_qc_leave(){

            $this->db->select('*')

                ->from('users')

                ->join('request_leave','users.id = request_leave.user_id')

                ->where('users.branch = "Quezon City"')

                ->order_by("request_leave.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function count_all_qc_leave_pending(){

            $where = ["users.branch" => "Quezon City", "request_leave.status" => "pending"];

            $this->db->select('*')

                ->from('users')

                ->join('request_leave','users.id = request_leave.user_id')

                ->where($where)

                ->order_by("request_leave.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }



        // public function get_one_by_where_leave($user_id, $date, $date_2) {

        //     $this->db->select('*')

        //         ->from('request_leave')

        //         ->where("(date = '".$date."' OR date = '".$date_2."') AND user_id = ".$user_id);

        //     $query = $this->db->get();

        //     return $query->row_array();

        // }

        //public function get_one_by_where_leave($user_id, $date, $date_2) {
            public function get_one_by_where_leave($user_id, $date) {
                $this->db->select('*')
                    ->from('request_leave')
                    //->where("(date = '".$date."' OR date = '".$date_2."') AND user_id = ".$user_id);
                    ->where("date = '".$date."' AND user_id = ".$user_id);
                $query = $this->db->get();
                return $query->row_array();
            }

        public function get_first_ever_record($user_id = null){
            if(isset($user_id)){
                $this->db->where('user_id', $user_id);
                $this->db->order_by('date', 'asc');
            }

            $this->db->select('date')
                ->from('request_leave')
                ->order_by('id', 'asc')
                ->limit(1);

            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_latest_leave($user_id = null){
            if(isset($user_id)){
                $this->db->where('user_id', $user_id);
            }

            $this->db->select('date')
                ->from('request_leave')
                ->order_by('date', 'desc')
                ->limit(1);

            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_leave_for_current_month($user_id){
            $this->db->select('date')
                ->from('request_leave')
                ->where('MONTH(date)', date('m'))
                ->where('YEAR(date)', date('Y'))
                ->where('user_id', $user_id);
            
            $query = $this->db->get();
            return $query->result_array();
                
        }

        public function count_yearly_leaves($user_id, $year){
            $this->db->select('*')
                ->from('request_leave')
                ->where('user_id', $user_id)
                ->where('YEAR(date)', $year)
                ->where('leave_type !=', 'birthday');
                

            $query = $this->db->get();
            $data = $query->result_array();
            return count($data);
        }

        public function get_all_employee_leaves_with_year($user_id, $year){
            $this->db->select('*')
                ->from('request_leave')
                ->where('user_id', $user_id)
                ->where('YEAR(date)', $year);
                
            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_all_sorted($month, $year, $status, $emp_id){
            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t2.name as employee_name, t3.name as updated_by')
                ->from('request_leave as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'pending')
                ->where('MONTH(t1.date)', $month)
                ->where('YEAR(t1.date)', $year)
                ->order_by('t1.date', 'desc');
            
            $query1 = $this->db->get();
            $pending = $query1->result_array();

            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t2.name as employee_name, t3.name as updated_by')
                ->from('request_leave as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where_in('t1.status', ['approved', 'retraction-denied'])
                ->where('MONTH(t1.date)', $month)
                ->where('YEAR(t1.date)', $year)
                ->order_by('t1.date', 'desc');
            
            $query2 = $this->db->get();
            $approved = $query2->result_array();

            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t2.name as employee_name, t3.name as updated_by')
                ->from('request_leave as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'denied')
                ->where('MONTH(t1.date)', $month)
                ->where('YEAR(t1.date)', $year)
                ->order_by('t1.date', 'desc');
            
            $query3 = $this->db->get();
            $denied = $query3->result_array();

            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t2.name as employee_name, t3.name as updated_by')
                ->from('request_leave as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'cancelled')
                ->where('MONTH(t1.date)', $month)
                ->where('YEAR(t1.date)', $year)
                ->order_by('t1.date', 'desc');
            
            $query4 = $this->db->get();
            $cancelled = $query4->result_array();

            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t2.name as employee_name, t3.name as updated_by')
                ->from('request_leave as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'retraction')
                ->where('MONTH(t1.date)', $month)
                ->where('YEAR(t1.date)', $year)
                ->order_by('t1.date', 'desc');
            
            $query5 = $this->db->get();
            $retraction = $query5->result_array();

            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t2.name as employee_name, t3.name as updated_by')
                ->from('request_leave as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'retracted')
                ->where('MONTH(t1.date)', $month)
                ->where('YEAR(t1.date)', $year)
                ->order_by('t1.date', 'desc');
            
            $query6 = $this->db->get();
            $retracted = $query6->result_array();

            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t2.name as employee_name, t3.name as updated_by')
                ->from('request_leave as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'retraction-denied')
                ->where('MONTH(t1.date)', $month)
                ->where('YEAR(t1.date)', $year)
                ->order_by('t1.date', 'desc');
            
            $query6 = $this->db->get();
            $retraction_denied = $query6->result_array();


            switch($status){
                case 'pending':
                    $all_requests = $pending;
                break;

                case 'approved':
                    $all_requests = $approved;
                break;

                case 'retraction':
                    $all_requests = $retraction;
                break;

                case 'retracted':
                    $all_requests = $retracted;
                break;

                case 'retraction-denied':
                    $all_requests = $retraction_denied;
                break;

                case 'denied':
                    $all_requests = $denied;
                break;

                case 'cancelled':
                    $all_requests = $cancelled;
                break;

                case null:
                    $all_requests = array_merge($pending, $retraction, $approved, $retracted, $denied, $cancelled);
                break;

                default:
                    $all_requests = array_merge($pending, $retraction, $approved, $retracted, $denied, $cancelled);
                break;
            }
            
            return $all_requests;

        }

        public function get_latest_request(){
            $this->db->select('*')
                ->from('request_leave')
                ->order_by('date', 'desc')
                ->limit(1);

            $query = $this->db->get();
            return $query->row_array();
        }

        public function employee_yearly_leaves_count($emp_id, $leave_type, $count = null){
            $this->db->select('*')
                ->from('request_leave')
                ->where('user_id', $emp_id)
                ->where('leave_type', $leave_type)
                ->where('leave_count', $count)
                ->where('YEAR(date)', date('Y'))
                ->where('status', 'approved');
            
                return $this->db->count_all_results();
        }

    }

?>