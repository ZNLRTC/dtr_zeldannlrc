<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crud extends CI_Model {

    protected $name;
    protected $key = 'ID';
    protected $orderby = '';

    public function __construct($name = "", $key = "") {
        parent::__construct();
        $this->load->database();
        $this->name = $name;
        if ($key != "") {
            $this->key = $key;
        }
    }

    public function get_all() {
        if (isset($this->orderby) && $this->orderby != '') {
            $this->db->order_by($this->orderby);
        }
        $query = $this->db->get($this->name);
        return $query->result_array();
    }

    public function get_all_desc() {
        if (isset($this->orderby) && $this->orderby != '') {
            $this->db->order_by($this->orderby, 'desc');
        }
        $query = $this->db->get($this->name);
        return $query->result_array();
    }

    public function get_row($id) {
        $query = $this->db->get_where($this->name, array($this->key => $id));
        return $query->row_array();
    }

    public function get_all_by($column, $value) {
        if (isset($this->orderby) && $this->orderby != '') {
            $this->db->order_by($this->orderby);
        }
        $where = array($column => $value);
        return $this->getAllByWhere($where);
    }

    public function get_all_by_two_columns($column1, $value1, $column2, $value2) {
        $where = array($column1 => $value1, $column2 => $value2);
        return $this->getAllByWhere($where);
    }

    public function get_all_by_where($where) {
        if (isset($this->orderby) && $this->orderby != '') {
            $this->db->order_by($this->orderby);
        }
        $this->db->where($where);
        $query = $this->db->get($this->name);
        return $query->result_array();
    }

    public function get_all_by_where_with_sort($where, $key_to_sort = 'id', $sort_type = 'asc') {
        if (empty($key_to_sort)) {
            $key_to_sort = 'id';
        }

        if (empty($sort_type)) {
            $sort_type = 'asc';
        }
        $this->db->order_by($key_to_sort, $sort_type);
        $this->db->where($where);
        $query = $this->db->get($this->name);
        return $query->result_array();
    }

    public function get_all_by_single_join($join_table, $join_by, $where) {
        if (isset($this->orderby) && $this->orderby != '') {
            $this->db->order_by($this->orderby);
        }
        $join_clause = $join_table . "." . $join_by . "=" . $this->name . "." . $join_by; //sample: category.cat_id = po_cat.cat_id

        $this->db->select('*');
        $this->db->from($this->name);
        $this->db->join($join_table, $join_clause);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_one_by($column, $value) {
        if (isset($this->orderby) && $this->orderby != '') {
            $this->db->order_by($this->orderby);
        }
        $this->db->where($column, $value);
        $query = $this->db->get($this->name);
        return $query->row_array();
    }

    public function get_one_by_where($where) {
        $this->db->where($where);
        $query = $this->db->get($this->name);
        return $query->row_array();
    }

    public function get_one_column_by($select_column, $where_column, $value) {
        $where = array($where_column => $value);
        return $this->getOneColumnMultipleBy($select_column, $where);
    }

    public function get_one_column_multiple_by($select_column, $where) {
        if (isset($this->orderby) && $this->orderby != '') {
            $this->db->order_by($this->orderby);
        }
        $this->db->select($select_column);
        $this->db->where($where);
        $query = $this->db->get($this->name);
        return $query->row_array();
    }

    public function count() {
        return $this->db->count_all($this->name);
    }

    public function count_by($column, $value) {
        $where = array($column => $value);
        return $this->countByWhere($where);
    }

    public function count_by_where($where) {
        $this->db->where($where);
        $this->db->from($this->name);
        return $this->db->count_all_results();
    }


    //Second parameter should be supplied with TRUE if the table has a DateCreated field
    public function add($row, $dateCreated = FALSE) {
        if ($dateCreated == TRUE) {
            $this->db->set('DateCreated', 'NOW()', FALSE);
        }
        $this->db->insert($this->name, $row);
        return $this->db->insert_id();
    }

    public function add_one($column, $value) {
        $row = array($column => $value);
        $this->add($row);
    }

    public function batch_add($rows,$dateCreated = FALSE){
        if ($dateCreated == TRUE) {
            $this->db->set('date_created', 'NOW()', FALSE);
        }
        return $this->db->insert_batch($this->name, $rows);
    }

    public function update($id, $row) {
        $this->db->where($this->key, $id);
        $this->db->update($this->name, $row);
        return $this->db->affected_rows() > 0 ? TRUE: FALSE;
    }

    public function update_where($where, $row) {
        $this->db->where($where);
        $this->db->update($this->name, $row);
        return $this->db->affected_rows() > 0 ? TRUE: FALSE;
    }

    public function delete($id) {
        return $this->db->delete($this->name, array('ID' => $id));
    }

    public function delete_where($where) {
        return $this->db->delete($this->name, $where);
    }

}

?>
