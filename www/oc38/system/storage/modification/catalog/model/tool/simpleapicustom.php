<?php
/*
@author Dmitriy Kubarev
@link   http://www.simpleopencart.com
*/

class ModelToolSimpleApiCustom extends Model {

					/* START Shipping data */
					public function getShippingCities($zone_id) {
						$this->load->model('extension/module/shippingdata');

						$shipping_method = $this->model_extension_module_shippingdata->getShippingMethod();

						$values = array(
							array(
								'id'   => '',
								'text' => $this->language->get('text_select')
							)
						);

						if ($shipping_method == 'justin') {
							$results = $this->model_extension_module_shippingdata->getJustinCities($zone_id);

							if (!$results) {
								$values[] = array(
									'id'   => 0,
									'text' => $this->language->get('text_none')
								);
							} else {
								foreach ($results as $result) {
									$values[] = array(
										'id'   => trim($result['description']),
										'text' => trim($result['description'])
									);
								}
							}
						} elseif ($shipping_method['method'] == 'ukrposhta') {
							$results = $this->model_extension_module_shippingdata->getUkrPoshtaCities($zone_id);

							if (!$results) {
								$values[] = array(
									'id'   => 0,
									'text' => $this->language->get('text_none')
								);
							} else {
								foreach ($results as $result) {
									$values[] = array(
										'id'   => trim($result['description'] . ', ' . $result['district_description']),
										'text' => trim($result['description'] . ', ' . $result['district_description'])
									);
								}
							}
						} else {
							$results = $this->model_extension_module_shippingdata->getNovaPoshtaCities($zone_id);

							if (!$results) {
								$values[] = array(
									'id'   => 0,
									'text' => $this->language->get('text_none')
								);
							} else {
								foreach ($results as $result) {
									$values[] = array(
										'id'   => trim($result['description']),
										'text' => trim($result['description'])
									);
								}
							}
						}

						return $values;
					}

					public function getShippingDepartments($city_name) {
						$this->load->model('extension/module/shippingdata');

						$shipping_method = $this->model_extension_module_shippingdata->getShippingMethod();

						$values = array(
							array(
								'id'   => '',
								'text' => $this->language->get('text_select')
							)
						);

						if ($shipping_method == 'justin') {
							$results = $this->model_extension_module_shippingdata->getJustinDepartments($city_name);

							if (!$results) {
								$values[] = array(
									'id'   => 0,
									'text' => $this->language->get('text_none')
								);
							} else {
								foreach ($results as $result) {
									$values[] = array(
										'id'   => trim($result['description']),
										'text' => trim($result['description'])
									);
								}
							}
						} elseif ($shipping_method['method'] == 'ukrposhta') {
							$results = $this->model_extension_module_shippingdata->getUkrPoshtaDepartments($city_name);

							if (!$results) {
								$values[] = array(
									'id'   => 0,
									'text' => $this->language->get('text_none')
								);
							} else {
								foreach ($results as $result) {
									$values[] = array(
										'id'   => trim($result['description']),
										'text' => trim($result['description'])
									);
								}
							}
						} else {
							$results = $this->model_extension_module_shippingdata->getNovaPoshtaDepartments($city_name);

							if (!$results) {
								$values[] = array(
									'id'   => 0,
									'text' => $this->language->get('text_none')
								);
							} else {
								foreach ($results as $result) {
									$values[] = array(
										'id'   => trim($result['description']),
										'text' => trim($result['description'])
									);
								}
							}
						}

						return $values;
					}

					public function getShippingPoshtomats($city_name) {
						$this->load->model('extension/module/shippingdata');

						$values = array(
							array(
								'id'   => '',
								'text' => $this->language->get('text_select')
							)
						);

						$results = $this->model_extension_module_shippingdata->getNovaPoshtaPoshtomats($city_name);

						if (!$results) {
							$values[] = array(
								'id'   => 0,
								'text' => $this->language->get('text_none')
							);
						} else {
							foreach ($results as $result) {
								$values[] = array(
									'id'   => trim($result['description']),
									'text' => trim($result['description'])
								);
							}
						}

					return $values;
					}
					/* END Shipping data */
					
    public function example($filterFieldValue) {
        $values = array();

        $values[] = array(
            'id'   => 'my_id',
            'text' => 'my_text'
        );

        return $values;
    }

    public function checkCaptcha($value, $filter) {
        if (isset($this->session->data['captcha']) && $this->session->data['captcha'] != $value) {
            return false;
        }

        return true;
    }

    public function getYesNo($filter = '') {
        return array(
            array(
                'id'   => '1',
                'text' => $this->language->get('text_yes')
            ),
            array(
                'id'   => '0',
                'text' => $this->language->get('text_no')
            )
        );
    }
}