<?php
	class Users_type_model extends Crud {

        public function __construct(){
            parent::__construct('user_type', 'id');
        }
	}
?>