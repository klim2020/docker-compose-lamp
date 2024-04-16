<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {

          
           /*qwqer Module additional field saving*/
          
            $this->load->model('extension/shipping/qwqer');
            $this->load->model('account/order');
            $order_info =  $this->session->data;
            //qwqer.omnivaparcelterminal
            if ($this->config->get('shipping_qwqer_status') && isset($this->session->data['shipping_method']['code']) && strpos($this->session->data['shipping_method']['code'],'qwqer.')!==false){
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
                if (isset($order_info) && $this->session->data['shipping_method']['code'] == 'qwqer.omnivaparcelterminal' ){
                    $order_info['shipping_method'] = 'Omnival Parcel Terminal';
                    $this->model_extension_shipping_qwqer->updateShippingMethod($this->session->data['order_id'],'Omnival Parcel Terminal');
                }


                $data_order =  $this->model_extension_shipping_qwqer->addOrderData($order_info);
                $this->session->data['shipping_method']['title'] = 'Omnival Parcel Terminal';
            }
          /*!qwqer Module additional field saving*/
          
			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}