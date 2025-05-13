<?php

    class Request_ot_model extends Crud {



        public function __construct(){

            parent::__construct('request_ot', 'id');

        }



        public function get_all_ot(){

            $this->db->select('*')

                ->from('users')

                ->join('request_ot','users.id = request_ot.user_id')

                ->order_by("request_ot.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function get_all_my_request_desc($user_id){

            $this->db->select('*')

                ->from('request_ot')

                ->where('user_id='.$user_id)

                ->order_by("date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function get_ot($user_id,$date){

            $where = ['user_id' => $user_id, "date" => $date];

            $this->db->select('*')

                ->from('request_ot')

                ->where($where)

                ->order_by("date_created", "desc");

            $query = $this->db->get();

            return $query->row_array();

        }



        public function get_ot_report($user_id,$date,$date_2){

            $this->db->select('*')

                ->from('request_ot')

                ->where("(date = '".$date."' OR date = '".$date_2."') AND user_id = ".$user_id)

                ->order_by("date_created", "desc");

            $query = $this->db->get();

            return $query->row_array();

        }



        public function get_all_qc_ot(){

            $this->db->select('*')

                ->from('users')

                ->join('request_ot','users.id = request_ot.user_id')

                ->where('users.branch = "Quezon City"')

                ->order_by("request_ot.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function count_all_qc_pending(){

            $where = ["users.branch" => "Quezon City", "request_ot.status" => "pending"];

            $this->db->select('*')

                ->from('users')

                ->join('request_ot','users.id = request_ot.user_id')

                ->where($where)

                ->order_by("request_ot.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }

        public function get_all_ot_sorted($month, $year, $emp_id, $status){

            if($emp_id != null){
                $this->db->where('t2.user_id', $emp_id);
            }

            $this->db->select('t1.*, t1.id as req_id, t2.date, t3.name, t4.name as updated_ni')
                ->from('request_ot as t1')
                ->join('dtr as t2', 't1.dtr_id = t2.id')
                ->join('users as t3', 't2.user_id = t3.id')
                ->join('users as t4', 't1.updated_by = t4.id')
                ->where('t1.status', 'pending')
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');
            $query_pending = $this->db->get();
            $pending = $query_pending->result_array();

            if($emp_id != null){
                $this->db->where('t2.user_id', $emp_id);
            }
            $this->db->select('t1.*, t1.id as req_id, t2.date, t3.name, t4.name as updated_ni')
                ->from('request_ot as t1')
                ->join('dtr as t2', 't1.dtr_id = t2.id')
                ->join('users as t3', 't2.user_id = t3.id')
                ->join('users as t4', 't1.updated_by = t4.id')
                ->where('t1.status', 'approved')
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');
            $query_approved = $this->db->get();
            $approved = $query_approved->result_array();

            if($emp_id != null){
                $this->db->where('t2.user_id', $emp_id);
            }
            $this->db->select('t1.*, t1.id as req_id, t2.date, t3.name, t4.name as updated_ni')
                ->from('request_ot t1')
                ->join('dtr as t2', 't1.dtr_id = t2.id')
                ->join('users as t3', 't2.user_id = t3.id')
                ->join('users as t4', 't1.updated_by = t4.id')
                ->where('t1.status', 'denied')
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');
            $query_denied = $this->db->get();
            $denied = $query_denied->result_array();

            if($emp_id != null){
                $this->db->where('t2.user_id', $emp_id);
            }
            $this->db->select('t1.*, t1.id as req_id, t2.date, t3.name, t4.name as updated_ni')
                ->from('request_ot t1')
                ->join('dtr as t2', 't1.dtr_id = t2.id')
                ->join('users as t3', 't2.user_id = t3.id')
                ->join('users as t4', 't1.updated_by = t4.id')
                ->where('t1.status', 'cancelled')
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');
            $query_cancelled = $this->db->get();
            $cancelled = $query_cancelled->result_array();

            switch($status){
                case 'pending':
                    $all_requests = $pending;
                break;

                case 'approved':
                    $all_requests = $approved;
                break;

                case 'denied':
                    $all_requests = $denied;
                break;

                case 'cancelled':
                    $all_requests = $cancelled;
                break;

                case null:
                    $all_requests = array_merge($pending, $approved, $denied, $cancelled);
                break;

                default:
                    $all_requests = array_merge($pending, $approved, $denied, $cancelled);
                break;
            }
            return $all_requests;
        }

        public function get_latest_request(){
            $this->db->select('*')
                ->from('request_ot')
                ->order_by('date_created', 'desc')
                ->limit(1);
    
            $query = $this->db->get();
            return $query->row_array();
        }

    }

    

?>