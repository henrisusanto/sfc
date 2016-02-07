<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class belanja extends my_controller {

	public function distributor ($tpl='table', $id=null) {
	  $data = array('entity' => 'distributor');
    $post = $this->input->post();
    $this->load->model('distributor');

    $data['thead'] = $this->distributor->getTHead();
    $data['fields'] = $this->distributor->getInputFields();
    $data['tbody'] = $this->distributor->find();
    $data['tablePage'] = $this->distributor->getTablePage($id);

    if ($tpl == 'delete') {
      $this->distributor->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $distributor = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $distributor[$field] = $post[$field];
      }
      if (!is_null($id)) $distributor['id'] = $id;
      $this->distributor->save($distributor);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->distributor->findOne($id);
    }

		$this->loadview($tpl, $data);
	}

  public function baranggudang ($tpl='table', $id=null) {
    $data = array('entity' => 'baranggudang');
    $post = $this->input->post();
    $this->load->model('baranggudang');

    $data['thead'] = $this->baranggudang->getTHead();
    $data['fields'] = $this->baranggudang->getInputFields();
    $data['tbody'] = $this->baranggudang->find();
    $data['tablePage'] = $this->baranggudang->getTablePage($id);

    if ($tpl == 'delete') {
      $this->baranggudang->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $baranggudang = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $baranggudang[$field] = $post[$field];
      }
      if (!is_null($id)) $baranggudang['id'] = $id;
      $this->baranggudang->save($baranggudang);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->baranggudang->findOne($id);
    }

    $this->loadview($tpl, $data);
  }
}