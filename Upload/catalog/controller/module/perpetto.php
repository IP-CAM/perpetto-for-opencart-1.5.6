<?php 
class ControllerModulePerpetto extends Controller  {
	private $moduleName = 'perpetto';
    private $moduleNameSmall = 'perpetto';
    private $moduleData_module = 'perpetto_module';
    private $moduleModel = 'model_module_perpetto';
	
    protected function index($config) {       

        $this->load->model('module/'.$this->moduleNameSmall);
        $this->document->addStyle('catalog/view/theme/'.$this->config->get('config_template').'/stylesheet/'.$this->moduleNameSmall.'.css');
    
        $languageVariables= array('heading_title', 'add_to_cart');

        foreach ($languageVariables as $variable) {
            $this->data[$variable] = $this->language->get($variable);
        }

        $this->data['ptto_current_position'] = $config['position'];


        $this->load->model('design/layout');

		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = 'common/home';
		}

		$layout_id = 0;

		if ($route == 'product/category' && isset($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$path = explode('_', (string)$this->request->get['path']);

			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if ($route == 'product/product' && isset($this->request->get['product_id'])) {
			$this->load->model('catalog/product');

			$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
		}

		if ($route == 'information/information' && isset($this->request->get['information_id'])) {
			$this->load->model('catalog/information');

			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
		}

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$this->data['moduleData'] = $this->config->get($this->moduleNameSmall);
        
		if(!empty($this->data['moduleData']['account_id']) && !empty($this->data['moduleData']['secret']) && !empty($this->data['moduleData']['connected']) && $this->data['moduleData']['connected'] == "yes") {
			
            if(!empty($this->data['moduleData']['positions'])) {
                if($layout_id == 1 || strpos($route,'common/home') !== false) {
                    foreach($this->data['moduleData']['positions']['home_page_position'] as $key => $value) {
                        $this->data['page_slots'][$key]['token'] = $key;
                        $this->data['page_slots'][$key]['position'] = $this->data['moduleData']['positions']['home_page_position'][$key]['position'];
                    }
                } else if($layout_id == 2 || strpos($route,'product/product') !== false) {
                    foreach($this->data['moduleData']['positions']['product_page_position'] as $key => $value) {
                        $this->data['page_slots'][$key]['token'] = $key;
                        $this->data['page_slots'][$key]['position'] = $this->data['moduleData']['positions']['product_page_position'][$key]['position'];
                    }
                } else if($layout_id == 3 || strpos($route,'product/category') !== false) {
                    foreach($this->data['moduleData']['positions']['category_page_position'] as $key => $value) {
                        $this->data['page_slots'][$key]['token'] = $key;
                        $this->data['page_slots'][$key]['position'] = $this->data['moduleData']['positions']['category_page_position'][$key]['position'];
                    }
                } else if(strpos($route,'checkout/cart') !== false) {
                    foreach($this->data['moduleData']['positions']['cart_page_position'] as $key => $value) {
                        $this->data['page_slots'][$key]['token'] = $key;
                        $this->data['page_slots'][$key]['position'] = $this->data['moduleData']['positions']['cart_page_position'][$key]['position'];
                    }
                }
            }
            
            if(!empty($this->data['page_slots'])) {
                foreach($this->data['page_slots'] as $slot) {
                    if(!empty($slot['position'])) {
                        if($slot['position'] == $this->data['ptto_current_position']) {
                            $this->data['slots'][] = $slot;
                        }
                    }
                }

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/banner.tpl')) {
                    $this->template = $this->config->get('config_template').'/template/module/'.$this->moduleNameSmall.'.tpl';
                } else {
                    $this->template = 'default/template/module/'.$this->moduleNameSmall.'.tpl';
                }
                    
                $this->render();

            }

		}
			
    }

    public function resetSlotPositions() {
        if($this->request->server['REQUEST_METHOD'] == "PUT") {
            $this->load->model('module/'.$this->moduleNameSmall);
            
            $put_request = array();

            $put_request = fopen("php://input","r");  

            $request_query = "";
            $query = "";

            while ($data = fread($put_request, 1024)) {
                $request_query .= $data;
            }    

            parse_str($request_query,$query);

            $request_acc_id = !empty($query['account_id']) ? $query['account_id'] : "";
            $request_secret = !empty($query['secret']) ? $query['secret'] : "";
            
            $json_result = array();
            
            if(!empty($request_acc_id) && !empty($request_secret)) {                
                $this->load->model('module/'.$this->moduleNameSmall);
                $settings = $this->config->get($this->moduleNameSmall);
                $account_id = !empty($settings['account_id']) ? $settings['account_id'] : "";
                $secret = !empty($settings['secret']) ? $settings['secret'] : "";

                if($account_id == $request_acc_id && $secret == $request_secret) {

                    $ch = curl_init("https://$account_id.api.perpetto.com/v0/info/slots?secret=$secret");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = "";
                    if( ($result = curl_exec($ch) ) === false) {
                        $json_result['status'] = 'error';
                        $json_result['errors'][] = 'Cannot get slots information!';
                    } else {
                        $slots = json_decode($result);
                        if(!empty($slots->error)) {
                            $json_result['status'] = 'error';
                            $json_result['errors'][] = 'Cannot get slots information!';
                        } else {
                            $this->request->post[$this->moduleNameSmall]['connected'] = 'yes';

                            $this->request->post[$this->moduleName]['account_id'] = $account_id;
                            $this->request->post[$this->moduleName]['secret'] =  $secret;

                            foreach($slots->data->slots as $slot) {
                                $grouped_slots[$slot->page][] = $slot;
                            }

                            if(!empty($grouped_slots)) {
                                foreach($grouped_slots as $key=>$value) {
                                    foreach($grouped_slots[$key] as $slot) {
                                        if($key === 'home_page') { 
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['position'] = 'content_bottom';
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['layout_id'] = '1';
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['token'] = $slot->token;
                                        } else if($key === 'product_page') {
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['position'] = 'content_bottom';
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['layout_id'] = '2';
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['token'] = $slot->token;
                                        } else if($key === 'category_page') {
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['position'] = 'content_bottom';
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['layout_id'] = '3';
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['token'] = $slot->token;
                                        } else if($key === 'cart_page') {
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['position'] = 'content_bottom';
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['layout_id'] = '7';
                                            $this->request->post[$this->moduleNameSmall]['positions'][$key."_position"][$slot->token]['token'] = $slot->token;
                                        }
                                    }
                                    
                                }      
                            }

                            if(!empty($this->request->post[$this->moduleName]['positions'])) {
                                foreach($this->request->post[$this->moduleName]['positions'] as $slots) {
                                    foreach($slots as $slot) {
                                        $slot_positions[$slot['layout_id']][] = $slot['position'];
                                    }
                                }

                                foreach($slot_positions as $key => $value) {
                                    $slot_positions[$key] = array_unique($slot_positions[$key]);
                                }

                                $i = 0;
                                foreach( $slot_positions as $key => $value) {
                                    foreach($slot_positions[$key] as $position) {
                                        $this->request->post[$this->moduleData_module][$i]['position'] = $position;
                                        $this->request->post[$this->moduleData_module][$i]['status'] = '1';
                                        $this->request->post[$this->moduleData_module][$i]['layout_id'] = $key;
                                        $this->request->post[$this->moduleData_module][$i]['sort_order'] = '0';
                                        $i++;
                                    }
                                }
                            }

                            $this->model_module_perpetto->editSetting($this->moduleNameSmall, $this->request->post, $this->config->get('config_store_id'));

                            $json_result['status'] = 'success';
                        }

                        
                    }
                } else {
                    $json_result['status'] = 'error';
                    $json_result['errors'][] = 'Wrong credentials!';
                }
                
            } else {
                $json_result['status'] = 'error';
                $json_result['errors'][] = 'Missing parameters!';
            }
           
            echo json_encode($json_result);exit;
            
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "<h1>404 Not Found</h1>";
            echo "The page that you have requested could not be found.";
            exit();
        }
        
        
    }
    
    public function getCatalogURL($store_id){
        if(isset($store_id) && $store_id){
            $storeURL = $this->db->query('SELECT url FROM `'.DB_PREFIX.'store` WHERE store_id=' . $store_id)->row['url'];
        }elseif (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        } 
        return $storeURL;
    }
}
?>
