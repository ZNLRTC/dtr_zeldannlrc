<?php
    class User_schedule_model extends Crud {

        public function __construct(){
            parent::__construct('user_schedule', 'id');
        }

    }
?>