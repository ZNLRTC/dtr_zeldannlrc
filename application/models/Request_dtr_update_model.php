<?php
    class Request_dtr_update_model extends Crud {

        public function __construct(){
            parent::__construct('request_dtr_update', 'id');
        }

        public function get_one_latest($dtr_id = null){
            $this->db->select('*')
                ->from('request_dtr_update')
                ->where('dtr_id ='.$dtr_id)
                ->order_by("date_created", "desc")
                ->limit(1);
            $query = $this->db->get();
            return $query->row_array();
        }

        public function get_one_earliest(){
            $this->db->select('*')
                ->from('request_dtr_update')
                ->order_by("date_created", "asc")
                ->limit(1);
            $query = $this->db->get();
            return $query->row_array();
        }

        public function read_all_requests($month, $year, $branch = null) {

            if(isset($branch)) $this->db->where('t3.branch', $branch);

            $this->db->select('t1.*, t2.date, t2.time_in, t2.break, t2.time_out, t3.name, t3.id as user_id, t3.branch')
                ->from('request_dtr_update as t1')
                ->join('dtr as t2', 't2.id = t1.dtr_id')
                ->join('users as t3', 't3.id = t2.user_id')
                ->where('YEAR(t2.date)', $year)
                ->where('MONTH(t2.date)', $month)
                ->order_by("CASE t1.status WHEN 'pending' THEN 1 ELSE 2 END", 'ASC')
                ->order_by("t1.date_created", "desc");
                    
            $query = $this->db->get();
            return $query->result_array();
        }

        public function read_full_dtr_update_request($rdtr_id){
            $this->db->select('*')
                ->from('request_dtr_update')
                ->join('dtr','dtr.id = request_dtr_update.dtr_id','inner')
                ->join('users','users.id = dtr.user_id','inner')
                ->where('request_dtr_update.id', $rdtr_id);
            $query = $this->db->get();
            return $query->row_array();
        }

        public function count_all_pending_dtr($branch){

            $this->db->select('t1.*, t2.user_id')
                    ->from('request_dtr_update as t1')
                    ->join('dtr as t2', 't1.dtr_id = t2.id')
                    ->join('users as t3', 't2.user_id = t3.id')
                    ->where('t1.status', 'pending')
                    ->where('t3.branch', $branch);
            
            $query = $this->db->get();
            $row = $query->result_array();
            return count($row);
        }
    }
?>