<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class personal extends my_controller {

  public function karyawan ($tpl='table', $id=null) {
    $data = array('entity' => 'karyawan');
    $post = $this->input->post();
    $this->load->model('karyawan');

    $data['thead'] = $this->karyawan->getTHead();
    $data['fields'] = $this->karyawan->getInputFields();
    $data['tbody'] = $this->karyawan->find();
    $data['tablePage'] = $this->karyawan->getTablePage($id);

    if ($tpl == 'delete') {
      $this->karyawan->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $karyawan = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $karyawan[$field] = $post[$field];
      }
      if (!is_null($id)) $karyawan['id'] = $id;
      $this->karyawan->save($karyawan);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->karyawan->findOne($id);
    }

    $this->loadview($tpl, $data);
  }
 
}