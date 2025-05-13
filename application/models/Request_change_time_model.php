<?php
	class Request_change_time_model extends Crud {

        public function __construct(){
            parent::__construct('request_change_time', 'id');
        }

        public function get_all_with_user_data($month, $year, $id = null, $status = null){
            if(isset($id)){
                $this->db->where('t1.user_id', $id);
            }

            $this->db->select('t1.*, t2.name as employee, t3.name as updated_by')
                ->from('request_change_time as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id') 
                ->where('t1.status', 'pending')
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');

            $query1 = $this->db->get();
            $pending = $query1->result_array();

            if(isset($id)){
                $this->db->where('t1.user_id', $id);
            }
            $this->db->select('t1.*, t2.name as employee, t3.name as updated_by')
                ->from('request_change_time as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'approved')
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');

            $query2 = $this->db->get();
            $approved = $query2->result_array();

            if(isset($id)){
                $this->db->where('t1.user_id', $id);
            }
            $this->db->select('t1.*, t2.name as employee, t3.name as updated_by')
                ->from('request_change_time as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'denied')
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');

            $query3 = $this->db->get();
            $denied = $query3->result_array();

            if(isset($id)){
                $this->db->where('t1.user_id', $id);
            }
            $this->db->select('t1.*, t2.name as employee, t3.name as updated_by')
                ->from('request_change_time as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'cancelled')
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');

            $query4 = $this->db->get();
            $cancelled = $query4->result_array();

            if(isset($status)){
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
            }else{
                $all_requests = array_merge($pending, $approved, $denied, $cancelled);
            }

            return $all_requests;
        }

        public function get_latest_request(){
            $this->db->select('*')
                ->from('request_change_time')
                ->order_by('date_created', 'desc')
                ->limit(1);
    
            $query = $this->db->get();
            return $query->row_array();
        }
	}
?>