<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class my_model extends CI_Model {

  function __construct () {
    parent::__construct();
    $this->load->database();
    date_default_timezone_set('Asia/Jakarta');
  }

  function getTHead () {
    return $this->thead;
  }

  function getInputFields () {
    return $this->inputFields;
  }

  function getSubFields () {
    return $this->subFields;
  }

  function getTablePage ($id) {
    $goto = current_url();
    if (strpos($goto, 'form')) $goto = str_replace('form', '', $goto);
    if (strpos($goto, 'delete')) $goto = str_replace('delete', '', $goto);
    if (!is_null($id)) $goto = str_replace("/$id", '', $goto);
    return $goto;  
  }

  function find ($where = array()) {
    $this->db->where($where);
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

  function findAnother ($table, $where = array()) {
    $this->db->where($where);
    return $this->db->get($table)->result();
  }

  function cashFlowGetSaldo ($cash, $flow) {
    $cashflow = $this->db->get('cashflow')->result();
    $last = end($cashflow);
    $saldo = isset($last->saldo) ? $last->saldo : 0;
    if ($flow == 'MASUK') return $saldo + $cash;
    else if ($flow == 'KELUAR') return $saldo - $cash;
  }
}
