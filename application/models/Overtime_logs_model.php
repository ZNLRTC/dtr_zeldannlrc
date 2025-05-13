<?php
	class Overtime_logs_model extends Crud {

        public function __construct(){
            parent::__construct('overtime_logs', 'id');
        }

        public function get_all_ot_sorted($month, $year, $emp_id, $status){

            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
    
            $this->db->select('t1.*, t1.id as req_id, t2.name, t3.name as updated_ni')
                ->from('overtime_logs as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'pending')
                ->where('t1.time_out !=', null)
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');
            $query_pending = $this->db->get();
            $pending = $query_pending->result_array();
    
            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t1.id as req_id, t2.name, t3.name as updated_ni')
                ->from('overtime_logs as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'approved')
                ->where('t1.time_out !=', null)
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');
            $query_approved = $this->db->get();
            $approved = $query_approved->result_array();
    
            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t1.id as req_id, t2.name, t3.name as updated_ni')
                ->from('overtime_logs as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'denied')
                ->where('t1.time_out !=', null)
                ->where('MONTH(t1.date_updated)', $month)
                ->where('YEAR(t1.date_updated)', $year)
                ->order_by('t1.date_created', 'desc');
            $query_denied = $this->db->get();
            $denied = $query_denied->result_array();
    
            if($emp_id != null){
                $this->db->where('t1.user_id', $emp_id);
            }
            $this->db->select('t1.*, t1.id as req_id, t2.name, t3.name as updated_ni')
                ->from('overtime_logs as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->join('users as t3', 't1.updated_by = t3.id')
                ->where('t1.status', 'cancelled')
                ->where('t1.time_out !=', null)
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
                ->from('overtime_logs')
                ->order_by('date_created', 'desc')
                ->limit(1);
    
            $query = $this->db->get();
            return $query->row_array();
        }
	}
    
?>