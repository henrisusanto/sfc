<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class belanja extends my_controller {

  public function harian ($tpl='table', $id=null) {
    $data = array('entity' => 'belanjaharian');
    $post = $this->input->post();
    $this->load->model('belanjaharian');

    $data['thead'] = $this->belanjaharian->getTHead();
    $data['fields'] = $this->belanjaharian->getInputFields();
    $data['subfields'] = $this->belanjaharian->getSubFields();
    $data['tbody'] = $this->belanjaharian->find();
    $data['tablePage'] = $this->belanjaharian->getTablePage($id);

    if ($tpl == 'delete') {
      $this->belanjaharian->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post && is_null($id)) {
      $this->belanjaharian->insert($post);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post && !is_null($id)) {
      $this->belanjaharian->update($post);
      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->belanjaharian->findOne($id);
      $this->load->model('belanjadetail');
      $data['subform'] = $this->belanjadetail->find(array('belanja' => $id));
    }

    if ($tpl == 'form') $data['viewers'][] = 'belanjadetail';
    $this->loadview($tpl, $data);
  }
 
}