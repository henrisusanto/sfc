<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class keuangan extends my_controller {

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

}