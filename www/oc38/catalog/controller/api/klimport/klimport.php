<?php
class ControllerApiKlimportKlimport extends Controller {

    public  function db_query(){
        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } elseif(isset($this->request->post['query'])) {
            $res = $this->db->query($this->request->post['query']);
            $json['succes'] = 'ok';
            $json['result'] = $res->rows;
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }



}
