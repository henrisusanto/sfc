<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class belanja extends my_controller {

	public function distributor ($tpl='table', $id=null) {
	  $data = array();
    $this->load->model('distributor');
    $data['thead'] = array(
      'ID DISTRIBUTOR', 
      'NAMA DISTRIBUTOR'
    );
    $data['tbody'] = $this->distributor->find();
		$this->loadview($tpl, $data);
	}
}