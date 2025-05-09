<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {

          
           /*qwqer Module additional field saving*/
          

            //qwqer.omnivaparcelterminal
            if ($this->config->get('qwqer_status') && isset($this->session->data['shipping_method']['code']) && strpos($this->session->data['shipping_method']['code'],'qwqer.')!==false){
                $this->load->model('checkout/order');
				$this->load->model('extension/shipping/qwqer');
                $this->load->model('account/order');
                $order_info =  $this->session->data;
                $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
                if (isset($order_info) && $this->session->data['shipping_method']['code'] == 'qwqer.omnivaparcelterminal' ){
                    $order_info['shipping_method'] = 'Omnival Parcel Terminal';
                    $this->model_extension_shipping_qwqer->updateShippingMethod($this->session->data['order_id'],'Omnival Parcel Terminal');
					$this->session->data['shipping_method']['title'] = 'Omnival Parcel Terminal';
                }
				if (isset($order_info) && $this->session->data['shipping_method']['code'] == 'qwqer.expressdelivery' ){
					$this->session->data['shipping_method']['title'] = 'Express Delivery';
				}
				if (isset($order_info) && $this->session->data['shipping_method']['code'] == 'qwqer.scheduleddelivery' ){
					$this->session->data['shipping_method']['title'] = 'Scheduled Delivery';
				}
                $order_info =  $this->session->data;

                $data_order =  $this->model_extension_shipping_qwqer->addOrderData($order_info);
                
            }
          /*!qwqer Module additional field saving*/
          
			$this->cart->clear();

			// Add to activity log
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');

				if ($this->customer->isLogged()) {
					$activity_data = array(
						'customer_id' => $this->customer->getId(),
						'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
						'order_id'    => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_account', $activity_data);
				} else {
					$activity_data = array(
						'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
						'order_id' => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_guest', $activity_data);
				}
			}

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

		$data['heading_title'] = $this->language->get('heading_title');

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['button_continue'] = $this->language->get('button_continue');

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