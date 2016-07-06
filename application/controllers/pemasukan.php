<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pemasukan extends my_controller {

  public function gudang ($tpl='table', $id=null) {
    $this->crud ('pemasukangudang', $tpl, $id);
  }

  public function setoran ($tpl='table', $id=null) {
    $model = 'setoran';
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

  public function peminjaman ($tpl='table', $id=null) {
    $this->crud ('peminjaman', $tpl, $id);
  }
}
