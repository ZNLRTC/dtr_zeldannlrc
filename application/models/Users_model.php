<?php
    class Users_model extends Crud {

        public function __construct(){
            parent::__construct('users', 'id');
        }

        public function get_all_desc_by_id($archive){
            $this->db->select('*')
                ->from('users')
                ->where('archive='.$archive)
                ->order_by("date_created", "desc");
            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_all_desc_qc_users($archive){
            $where = ["archive" => $archive, "branch" => "Quezon City", "id !=" => "1"];
            $this->db->select('*')
                ->from('users')
                ->where($where)
                ->order_by("date_created", "desc");
            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_all_employee_names(){
            $this->db->select('t1.id, t1.name')
                    ->from('users as t1')
                    ->where_not_in('t1.user_type', [1, 5])
                    ->where('archive', 0)
                    ->order_by('t1.name');
            
            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_all_employees_for_observer(){
            $this->db->select('*')
                    ->from('users')
                    ->where_in('user_type', [2, 3, 6, 7, 11, 12, 13, 15])
                    ->where('archive', 0)
                    ->where('restrict_account', 0)
                    ->order_by('name');
            
            $query = $this->db->get();
            return $query->result_array();
        }

        public function get_all_employees(){
            $this->db->select('*')
                    ->from('users')
                    ->where_not_in('user_type', [1, 5, 16])
                    ->where('archive', 0)
                    ->where('restrict_account', 0)
                    ->order_by('name');
            
            $query = $this->db->get();
            return $query->result_array();
        }
    }
?>