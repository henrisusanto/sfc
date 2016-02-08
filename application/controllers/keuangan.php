<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class keuangan extends my_controller {

  public function cashflow ($tpl='table', $id=null) {
    $data = array('entity' => 'cashflow');
    $post = $this->input->post();
    $this->load->model('cashflow');

    $data['thead'] = $this->cashflow->getTHead();
    $data['fields'] = $this->cashflow->getInputFields();
    $data['tbody'] = $this->cashflow->find();
    $data['tablePage'] = $this->cashflow->getTablePage($id);

    if ($tpl == 'delete') {
      $this->cashflow->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $cashflow = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $cashflow[$field] = $post[$field];
      }
      if (!is_null($id)) $cashflow['id'] = $id;
      $this->cashflow->save($cashflow);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->cashflow->findOne($id);
    }

    $this->loadview($tpl, $data);
  }

	public function debitur ($tpl='table', $id=null) {
	  $data = array('entity' => 'debitur');
    $post = $this->input->post();
    $this->load->model('debitur');

    $data['thead'] = $this->debitur->getTHead();
    $data['fields'] = $this->debitur->getInputFields();
    $data['tbody'] = $this->debitur->find();
    $data['tablePage'] = $this->debitur->getTablePage($id);

    if ($tpl == 'delete') {
      $this->debitur->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $debitur = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $debitur[$field] = $post[$field];
      }
      if (!is_null($id)) $debitur['id'] = $id;
      $this->debitur->save($debitur);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->debitur->findOne($id);
    }

		$this->loadview($tpl, $data);
	}

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

    $this->loadview($tpl, $data);
  }
}