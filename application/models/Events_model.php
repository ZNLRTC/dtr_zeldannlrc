<?php
	class Events_model extends Crud {

        public function __construct(){
            parent::__construct('events', 'id');
        }

        public function get_events() {
            return $this->db->get('events')->result();
        }
    
        public function add_event($data) {
            $this->db->insert('events', $data);
        }

        public function delete_event($event_id) {
            $this->db->where('id', $event_id);
            $this->db->delete('events');
        }
	}
?>