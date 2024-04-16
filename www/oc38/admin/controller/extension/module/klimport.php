<?php

class ControllerExtensionModuleKlimport extends Controller {

  private $error = array();
  private $ext = 'klimport';
  private $path = 'extension/module/klimport';
  private $module_path = 'marketplace/extension';
  private $lang = 1;
  private $a = 0;
  private $message = '';
  private $insert_tables = array('attributes' => array('attribute_id','xml_name'),'replace_name' => array('name_from','name_to'),'additional_params' => array('param_name','param_text'),'category_match' => array('category_id','xml_name','markup','custom'),'markup' => array('name','products','markup'));



  public function index() {
     $this->response->setOutput($this->load->view($this->path, $data));
  }


  public function install(){

  }

  public function uninstall(){
    
  }
}
