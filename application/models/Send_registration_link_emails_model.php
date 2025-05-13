<?php
    class Send_registration_link_emails_model extends Crud {

        public function __construct(){
            parent::__construct('send_registration_link_emails', 'id');
        }

        public function get_all_with_limit($limit){
            $this->db->select('t1.id, t1.email')
                ->from('send_registration_link_emails as t1')
                ->where('status', 'pending')
                ->limit($limit);
            $query = $this->db->get();
            return $query->result_array();
        }

    }
?>