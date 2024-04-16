<?php

class ControllerExtensionModuleFeatureBlocks extends Controller {

  private $error = array();
  private $ext = 'klimport';
  private $path = 'extension/module/feature_blocks';
  private $module_path = 'marketplace/extension';
  private $lang = 1;
  private $a = 0;
  private $message = '';
  private $insert_tables = array('attributes' => array('attribute_id','xml_name'),'replace_name' => array('name_from','name_to'),'additional_params' => array('param_name','param_text'),'category_match' => array('category_id','xml_name','markup','custom'),'markup' => array('name','products','markup'));



  public function index() {
      $this->load->language('extension/module/feature_blocks');

      $this->document->setTitle($this->language->get('heading_title'));

      $this->load->model('setting/setting');

      $data = [];

      if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

          $this->model_setting_setting->editSetting('module_feature_blocks', $this->request->post);
          $this->cache->delete('product');

          $this->session->data['success'] = $this->language->get('text_success');

          $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
      }
     $data['action'] = $this->url->link('extension/module/feature_blocks', 'user_token=' . $this->session->data['user_token'], true);

     $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

     $data['breadcrumbs'] = array();
     $data['breadcrumbs'][] = array(
         'text' => $this->language->get('text_home'),
         'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
     );
     $data['breadcrumbs'][] = array(
         'text' => $this->language->get('text_extension'),
         'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
     );
     $data['breadcrumbs'][] = array(
         'text' => $this->language->get('heading_title'),
         'href' => $this->url->link('extension/module/account', 'user_token=' . $this->session->data['user_token'], true)
     );

      if (isset($this->request->post['module_feature_blocks_status'])) {
          $data['module_feature_blocks_status'] = $this->request->post['module_feature_blocks_status'];
      } else {
          $data['module_feature_blocks_status'] = $this->config->get('module_feature_blocks_status');
      }

     $data['header'] = $this->load->controller('common/header');
     $data['column_left'] = $this->load->controller('common/column_left');
     $data['footer'] = $this->load->controller('common/footer');

     $this->response->setOutput($this->load->view($this->path, $data));
  }


  public function install(){

  }

  public function uninstall(){
    
  }

  public function validate(){
    return true;
  }
}
