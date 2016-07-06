<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class produksi extends my_controller {

  public function pemotongan ($tpl='table', $id=null) {
    $model = 'pemotongan';
    $data = array('entity' => $model);
    $post = $this->input->post();
    $this->load->model($model);

    if (!$this->pemotongan->is_ok()) {
      $data['message'] = array("PROSES TIDAK DAPAT DILANJUTKAN KARENA 
      AYAM HIDUP DAN ATAU ATI AYAM TIDAK DITEMUKAN DALAM DATA MASTER AYAM MENTAH", 'error');
    }

    if ($tpl == 'table') {
      $data['thead'] = $this->$model->getTHead();
      $data['tbody'] = $this->$model->find();
    } else if ($tpl == 'form') {
      $data['fields'] = $this->$model->getInputFields();
      $data['expandables'] = $this->$model->getExpandables($id);
    }
    $data['tablePage'] = $this->$model->getTablePage($id);

    if ($tpl == 'delete') {
      $this->$model->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $entity = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $entity[$field] = $post[$field];
      }
      if (!empty($data['expandables'])) $entity = $post;
      if (!is_null($id)) $entity['id'] = $id;
      $valid = $this->$model->validate($entity);
      if ($valid === true) {
        $this->$model->save($entity);
        redirect($data['tablePage']);
      } else {
        $data['message'] = $valid;
        $data = $this->$model->prePopulate($entity, $data);
      }
    }

    if (!is_null($id)) {
      $data['form'] = $this->$model->findOne($id);
    }

    $this->loadview($tpl, $data);
  }

  public function bawaan ($tpl='table', $id=null) {
    $this->crud ('bawaan', $tpl, $id);
  }

  public function proses ($tpl='table', $id=null) {
    $this->crud ('prosesproduksi', $tpl, $id);
  }

  public function transaksiinternal ($tpl='table', $id=null) {
    $this->crud ('transaksiinternal', $tpl, $id);
  }

  public function pesanan ($tpl='table', $id=null) {
    $this->load->library('session');
    $model = 'pesanan';
    $data = array('entity' => $model);
    $post = $this->input->post();
    $this->load->model($model);

    if ($tpl == 'table') {
      $data['thead'] = $this->$model->getTHead();
      $data['tbody'] = $this->$model->find();
    } else if ($tpl == 'form') {
      $data['fields'] = $this->$model->getInputFields();
      $data['expandables'] = $this->$model->getExpandables($id);
    }
    $data['tablePage'] = $this->$model->getTablePage($id);

    if ($tpl == 'delete') {
      $message = $this->$model->delete($id);
      if (strlen($message) > 0) $this->session->set_flashdata('message', $message);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $entity = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $entity[$field] = $post[$field];
      }
      if (!empty($data['expandables'])) $entity = $post;
      $valid = $this->$model->validate($entity);
      if ($valid === true) {
        $this->$model->save($entity);
        redirect($data['tablePage']);
      } else {
        $data['message'] = $valid;
        $data = $this->$model->prePopulate($entity, $data);
      }
    }

    if (!is_null($id))  $data['form'] = $this->$model->findOne($id);
    if ($this->session->flashdata('message'))
      $data['message'] = array($this->session->flashdata('message'), 'error');

    $this->loadview($tpl, $data);
  }
  
}