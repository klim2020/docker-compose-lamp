<?php
class ControllerExtensionModuleFeatureBlocks extends Controller {

    public function index(&$data){

        if ($this->config->get('module_feature_blocks_status') ==false){
            return '';
        }

        $this->document->addStyle('/catalog/view/theme/default/stylesheet/feature_blocks.css');
        $this->load->model('catalog/category');
        $this->load->model('extension/module/feature_blocks');
        $this->load->language('extension/module/feature_blocks');
        $module = $this->model_extension_module_feature_blocks;

        $cat = $this->model_catalog_category->getCategory($data['category_id']);
        $category_name = $cat['name'];

        foreach($module->getItemsByPrice($data['category_id'],array('sort'=>'asc')) as $item){
            $data['cheapest'][] = array(
                'discount'=> $this->currency->format($item['discount'],$this->session->data['currency']),
                'price'   => $this->currency->format($item['price'],  $this->session->data['currency']),
                'name' => htmlspecialchars_decode($item['name'],ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401),
                'link' => htmlspecialchars_decode($this->url->link('product/product', 'product_id=' . $item['product_id'])),
            );
        }

        foreach($module->getItemsByPrice($data['category_id'],array('sort'=>'desc')) as $item){
            $data['expensive'][] = array(
                'discount'=> $this->currency->format($item['discount'],$this->session->data['currency']),
                'price'   => $this->currency->format($item['price'],  $this->session->data['currency']),
                'name' => htmlspecialchars_decode($item['name'],ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401),
                'link' => htmlspecialchars_decode($this->url->link('product/product', 'product_id=' . $item['product_id'])),
            );
        }

        foreach($module->getItemsByDiscount($data['category_id']) as $item){
            $data['discounts'][] = array(
                'discount'=> $this->currency->format($item['discount'],$this->session->data['currency']),
                'price'   => $this->currency->format($item['price'],  $this->session->data['currency']),
                'name' => htmlspecialchars_decode($item['name'],ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401),
                'link' => htmlspecialchars_decode($this->url->link('product/product', 'product_id=' . $item['product_id'])),
            );
        }

        foreach($module->getItemsThisYear($data['category_id']) as $item){
            $data['year'][] = array(
                'discount'=> $this->currency->format($item['discount'],$this->session->data['currency']),
                'price'   => $this->currency->format($item['price'],  $this->session->data['currency']),
                'name' => htmlspecialchars_decode($item['name'],ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401),
                'link' => htmlspecialchars_decode($this->url->link('product/product', 'product_id=' . $item['product_id'])),
            );
        }

        foreach($module->getItemsNew($data['category_id']) as $item){

            $data['new'][] = array(
                'discount'=> $this->currency->format($item['discount'],$this->session->data['currency']),
                'price'   => $this->currency->format($item['price'],  $this->session->data['currency']),
                'name' => htmlspecialchars_decode($item['name'],ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401),
                'link' => htmlspecialchars_decode($this->url->link('product/product', 'product_id=' . $item['product_id'])),
            );
        }

        foreach($module->getRandomItems($data['category_id']) as $item){

            $data['rand'][] = array(
                'discount'=> $this->currency->format($item['discount'],$this->session->data['currency']),
                'price'   => $this->currency->format($item['price'],  $this->session->data['currency']),
                'name' => htmlspecialchars_decode($item['name'],ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401),
                'link' => htmlspecialchars_decode($this->url->link('product/product', 'product_id=' . $item['product_id'])),
            );
        }


        $data['cat_name'] = $cat['name'];

        return $this->load->view('extension/module/feature_blocks', $data);

    }



}
