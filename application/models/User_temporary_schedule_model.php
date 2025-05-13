<?php
    class User_temporary_schedule_model extends Crud {

        public function __construct(){
            parent::__construct('user_temporary_schedule', 'id');
        }

        public function get_temp_schedule_for_month($user_id, $year = null, $month = null){
            isset($year) ? $year = $year : $year = date('Y');
            isset($month) ? $month = $month : $month = date('Y');

            $this->db->select('id, date, time as in-out, workbase')
                    ->from('user_temporary_schedule')
                    ->where('user_id', $user_id)
                    ->where('YEAR(date)', $year)
                    ->where('MONTH(date)', $month)
                    ->order_by('date', 'desc');
            
            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_temp_schedule_today($user_id, $date){
            $this->db->select('date, time as in-out, workbase')
                    ->from('user_temporary_schedule')
                    ->where('user_id', $user_id)
                    ->where('date', $date);
            
            $query = $this->db->get();
            return $query->row_array();
        }

        public function get_earliers_record($emp_id){
            $this->db->select('*')
                    ->from('user_temporary_schedule')
                    ->where('user_id', $emp_id)
                    ->order_by('id', 'asc')
                    ->limit(1);
            
            $query = $this->db->get();
            return $query->row_array();
        }

    }
?>