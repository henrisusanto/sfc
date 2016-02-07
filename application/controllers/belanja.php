<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class belanja extends my_controller {

	public function distributor ($tpl='table', $id=null) {
	  $data = array();
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
}