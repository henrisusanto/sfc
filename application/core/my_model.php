<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class my_model extends CI_Model {

  function __construct () {
    parent::__construct();
    $this->load->database();
  }

  function getTHead () {
    return $this->thead;
  }

  function getInputFields () {
    return $this->inputFields;
  }

  function getTablePage ($id) {
    $goto = current_url();
    if (strpos($goto, 'form')) $goto = str_replace('form', '', $goto);
    if (strpos($goto, 'delete')) $goto = str_replace('delete', '', $goto);
    if (!is_null($id)) $goto = str_replace("/$id", '', $goto);
    return $goto;  
  }

  function find ($where = array()) {
    return $this->db->get($this->table)->result();
  }

  function findOne ($id) {
    return $this->db->get_where($this->table, array('id'=>$id))->row_array();
  }

  function save ($data) {
    if (!isset($data['id'])) {
      $data['id'] = time();
      $this->db->insert($this->table, $data);
    } else {
      return $this->db->where('id', $data['id'])->update($this->table, $data);
    }
    return $data['id'];
  }

  function delete ($id) {
    return $this->db->where('id', $id)->delete($this->table);
  }
}
