<?php
class ControllerExtensionModuleManufacturersSlider extends Controller {
	public function index($setting) {

        $this->document->addStyle('catalog/view/theme/stylesheet/swiperjs/swiper-bundle.min.css');
        $this->document->addScript('catalog/view/theme/stylesheet/swiperjs/swiper-bundle.min.js');

        static $module = 0;

		$this->load->language('product/manufacturer');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_brands'] = $this->language->get('text_brands');
		$data['text_index'] = $this->language->get('text_index');

		$data['brands'] = array();

		$this->load->model('extension/module/manufacturers_slider');

		$results = $this->model_extension_module_manufacturers_slider->getAllManufacturersByOrder($setting['manufacturer']['access']);

		//echo "<pre>"; print_r($setting);
		$this->load->model('tool/image');

		foreach ($results as $result) {
            if (empty($result['image']) || $result['image']=='no_image.png'){
                continue;
            }

            $data['manufacturers'][] = array(
                'name'  => $result['name'],
                'image'  => $this->model_tool_image->resize($result['image'], $setting['height'], $setting['width'] ),
                'link'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
            );
        }
        if (isset($setting['module_id']))
        {
            $data['module'] = $setting['module_id'];
        }
        $data['width'] =  $setting['width'];
        $data['height'] = $setting['height'];
        $data['spaceBetween'] = $setting['spaceBetween'];
        $data['autoplay'] = $setting['autoplay'];
        $lccode =  (int)$this->config->get('config_language_id')  ;
        $data['header']=$setting['manufacturer_description'][$lccode];


		return $this->load->view('extension/module/manufacturers_slider', $data);
	}

}
