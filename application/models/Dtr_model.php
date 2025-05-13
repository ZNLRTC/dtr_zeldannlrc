<?php

    class Dtr_model extends Crud {



        public function __construct(){

            parent::__construct('dtr', 'id');

        }


        public function get_current_dtr($user_id){

            $this->db->select('t1.*')

                ->from('dtr as t1')
                ->where('user_id', $user_id)
                ->where('time_out', NULL);

            $query = $this->db->get();
            $row = $query->result_array();

            if(!empty($row)){
                return $row[0];
            }else{
                return false;
            }
        }

        public function get_all_dtr_desc(){

            $this->db->select('*')

                ->from('users')

                ->join('dtr','users.id = dtr.user_id')

                ->order_by("dtr.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function get_all_my_dtr_desc($user_id, $month, $year){

            $this->db->select('*')

                ->from('dtr')

                ->where('user_id', $user_id)

                ->where('MONTH(date)', $month)

                ->where('YEAR(date)', $year)

                ->order_by("date desc, date_created desc");
  
            $query = $this->db->get();

            return $query->result_array();

        }

        public function get_first_ever_record($user_id = null){

            if(isset($user_id)){
                $this->db->where('user_id', $user_id);
            }

            $this->db->select('date')
                    ->from('dtr')
                    ->order_by('date', 'asc')
                    ->limit(1);
                    
            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_user_eod_record($user_id, $date){

            $this->db->select('t1.end_of_day')
                    ->from('dtr as t1')
                    ->where('user_id', $user_id)
                    ->where('date', $date);
                    
            $query = $this->db->get();
            return $query->row();
        }



        public function get_same_date($user_id,$date){

            $where = ['user_id' => $user_id, "date" => $date, "time_out !=" => NULL];

            $this->db->select('*')

                ->from('dtr')

                ->where($where)

                ->order_by("date", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function get_hours($user_id,$date){

            $where = ['user_id' => $user_id, "date" => $date];

            $this->db->select('*')

                ->from('dtr')

                ->where($where);

            $query = $this->db->get();

            return $query->result_array();

        }



        public function get_all_active_dtr_desc(){
            
            $this->db->select('t1.id, t1.*, t2.*')

                ->from('users as t1')
                ->join('dtr as t2','t1.id = t2.user_id')
                ->where('t2.time_out =', NULL)
                ->order_by("t2.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function outstanding_dtr_desc(){

            $where = ["dtr.time_out !=" => NULL, "dtr.paid" => "no"];

            $this->db->select('*')

                ->from('users')

                ->join('dtr','users.id = dtr.user_id')

                ->where($where)

                ->group_by("dtr.user_id, dtr.date")

                ->order_by("dtr.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }

        

        public function outstanding_dtr_group_desc(){

            $where = ["dtr.time_out !=" => NULL, "dtr.paid" => "no"];

            $this->db->select('*')

                ->from('users')

                ->join('dtr','users.id = dtr.user_id')

                ->where($where)

                ->group_by("dtr.user_id")

                ->order_by("dtr.date_created", "asc");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function check_if_paid($user_id,$date){

            $where = ['user_id' => $user_id, "date" => $date, "paid" => "yes"];

            $this->db->select('*')

                ->from('dtr')

                ->where($where);

            $query = $this->db->get();

            return $query->result_array();

        }



        public function get_all_by_group(){

            $where = ['time_out !=' => NULL, "time_out !=" => ""];

            $this->db->select('*')

                ->from('dtr')

                ->where($where)

                ->group_by("date, user_id");

            $query = $this->db->get();

            return $query->result_array();

        }



        public function get_all_by_group_desc($month, $year){

            $where = ['time_out !=' => NULL, "time_out !=" => ""];

            $this->db->select('*')
                ->from('dtr')
                ->where($where)
                ->where('MONTH(date)', $month)
                ->where('YEAR(date)', $year)
                ->group_by("date, user_id")
                ->order_by("date desc");

            $query = $this->db->get();
            return $query->result_array();

        }



        public function get_all_qc_by_group($month, $year){

            $where = ['t1.time_out !=' => NULL, "t1.time_out !=" => "", "t2.branch" => "Quezon City"];

            $this->db->select('t1.*')
                ->from('dtr as t1')
                ->join('users as t2', 't1.user_id = t2.id')
                ->where($where)
                ->where('MONTH(t1.date)', $month)
                ->where('YEAR(t1.date)', $year)
                ->group_by("t1.date, t1.user_id")
                ->order_by("t1.date desc");

            $query = $this->db->get();
            return $query->result_array();

        }



        public function get_latest_dtr($user_id){

            $this->db->select('*')

                ->from('dtr')

                ->where('user_id ='.$user_id)

                ->order_by("dtr.date_created", "desc")

                ->limit(1);

            $query = $this->db->get();

            return $query->result_array();

        }



        public function get_all_active_qc_dtr(){

            $where = ['dtr.time_out' => NULL, "users.branch" => "Quezon City"];

            $this->db->select('*')

                ->from('users')

                ->join('dtr','users.id = dtr.user_id')

                ->where($where)

                ->order_by("dtr.date_created", "desc");

            $query = $this->db->get();

            return $query->result_array();

        }


        public function get_one_by_where_date($user_id, $date) {
            $this->db->select('*')
                ->from('dtr')
                //->where("(date = '".$date."' OR date = '".$date_2."') AND user_id = ".$user_id);
                ->where("date = '".$date."' AND user_id = ".$user_id);
            $query = $this->db->get();
            return $query->row_array();

        }

    }

?>