<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gerai extends my_controller {

  public function setoran ($tpl='table', $id=null) {
    $data = array('entity' => 'setoran');
    $post = $this->input->post();
    $this->load->model('setoran');

    $data['thead'] = $this->setoran->getTHead();
    $data['fields'] = $this->setoran->getInputFields();
    $data['tbody'] = $this->setoran->find();
    $data['tablePage'] = $this->setoran->getTablePage($id);

    if ($tpl == 'delete') {
      $this->setoran->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $setoran = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $setoran[$field] = $post[$field];
      }
      if (!is_null($id)) $setoran['id'] = $id;
      $this->setoran->save($setoran);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->setoran->findOne($id);
    }
    if ($tpl == 'form') {
      $data['viewers'][] = 'penjualanoutlet';
      $data['viewers'][] = 'operasionaloutlet';
      $data['viewers'][] = 'sisadaganganoutlet';
    }
    $this->loadview($tpl, $data);
  } 

  public function bawaan ($tpl='table', $id=null) {
    $data = array('entity' => 'bawaan');
    $post = $this->input->post();
    $this->load->model('bawaan');

    $data['thead'] = $this->bawaan->getTHead();
    $data['fields'] = $this->bawaan->getInputFields();
    $data['subfields1'] = $this->bawaan->getSubFields(1);
    $data['subfields2'] = $this->bawaan->getSubFields(2);
    $data['tbody'] = $this->bawaan->find();
    $data['tablePage'] = $this->bawaan->getTablePage($id);

    if ($tpl == 'delete') {
      $this->bawaan->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post && is_null($id)) {
      $this->bawaan->insert($post);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post && !is_null($id)) {
      $this->bawaan->update($post);
      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->bawaan->findOne($id);
      $this->load->model('bawaandetail');    
      $data['subform1'] = $this->bawaandetail->find(array('bawaan' => $id, 'type' => 'BARANG'));
      $data['subform2'] = $this->bawaandetail->find(array('bawaan' => $id, 'type' => 'PRODUK'));
    }

    if ($tpl == 'form') {
      $data['viewers'][] = 'bawaanbarang';
      //$data['viewers'][] = 'bawaanproduk';
    }
    $this->loadview($tpl, $data);
  }

}