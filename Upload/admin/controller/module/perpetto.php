<?php
class ControllerModulePerpetto extends Controller {
	private $moduleName = 'perpetto';
	private $moduleNameSmall = 'perpetto';
	private $moduleData_module = 'perpetto_module';
	private $moduleModel = 'model_module_perpetto';
	
    public function index() { 
		$this->data['moduleName'] = $this->moduleName;
		$this->data['moduleNameSmall'] = $this->moduleNameSmall;
		$this->data['moduleData_module'] = $this->moduleData_module;
		$this->data['moduleModel'] = $this->moduleModel;
	 
        $this->load->language('module/'.$this->moduleNameSmall);
        $this->load->model('module/'.$this->moduleNameSmall);
        $this->load->model('setting/store');
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');
        $this->load->model('design/layout');

        $catalogURL = $this->getCatalogURL();

        $this->document->addScript('view/javascript/'.$this->moduleNameSmall.'/bootstrap/js/bootstrap.min.js');
        $this->document->addStyle('view/javascript/'.$this->moduleNameSmall.'/bootstrap/css/bootstrap.min.css');
        $this->document->addStyle('view/stylesheet/'.$this->moduleNameSmall.'/font-awesome/css/font-awesome.min.css');
        $this->document->addStyle('view/stylesheet/'.$this->moduleNameSmall.'/'.$this->moduleNameSmall.'.css');
        $this->document->setTitle($this->language->get('heading_title'));

        if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0; 
        }
	
        $store = $this->getCurrentStore($this->request->get['store_id']);
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) { 	
            if (!empty($_POST['OaXRyb1BhY2sgLSBDb21'])) {
                $this->request->post[$this->moduleNameSmall]['LicensedOn'] = $_POST['OaXRyb1BhY2sgLSBDb21'];
            }

            if (!empty($_POST['cHRpbWl6YXRpb24ef4fe'])) {
                $this->request->post[$this->moduleNameSmall]['License'] = json_decode(base64_decode($_POST['cHRpbWl6YXRpb24ef4fe']), true);
            }

            $account_id = $this->request->post[$this->moduleNameSmall]['account_id'];
            $secret = $this->request->post[$this->moduleNameSmall]['secret'];

            $account_request_data = array(
                'method'        => 'info',
                'account_id'    => $account_id,
                'secret'        => $secret
            );

            $result = "";

            $account_info = $this->model_module_perpetto->perpettoCall($account_request_data);

            if(!isset($account_info->error) && !empty($account_info->data)) { 
                $this->request->post[$this->moduleNameSmall]['connected'] = 'yes';
                $result = $account_info;
            } else {
                if(!empty($account_info->error)) {
                    $this->session->data['error_warning'] = $account_info->error;
                } else {
                    $this->session->data['error_warning'] = $this->language->get('text_invalid_account');
                }
            }

            $layouts = $this->model_design_layout->getLayouts();

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

        	$this->model_setting_setting->editSetting($this->moduleNameSmall, $this->request->post, $this->request->post['store_id']);            
			
            $this->redirect($this->url->link('module/'.$this->moduleNameSmall, 'store_id='.$this->request->post['store_id'] . '&token=' . $this->session->data['token'], 'SSL'));
        }
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		if (isset($this->session->data['error_warning'])) {
			$this->data['error_warning'] = $this->session->data['error_warning'];
            unset($this->session->data['error_warning']);
		} else {
			$this->data['error_warning'] = '';
		}

        $this->data['breadcrumbs']   = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/'.$this->moduleNameSmall, 'token=' . $this->session->data['token'], 'SSL'),
        );

        $languageVariables = array(
		    // Main
			'heading_title',
			'error_permission',
			'text_success',
			'text_enabled',
			'text_disabled',
			'button_cancel',
			'save_changes',
			'text_default',
			'text_module',
			// Control panel
            'entry_code',
			'entry_code_help',
            'text_content_top', 
            'text_content_bottom',
            'text_column_left', 
            'text_column_right',
            'entry_layout',         
            'entry_position',       
            'entry_status',         
            'entry_sort_order',     
            'entry_layout_options',  
            'entry_position_options',
			'entry_action_options',
            'button_add_module',
            'button_remove',
			// Custom CSS
			'custom_css',
            'custom_css_help',
            'custom_css_placeholder',
			// Module depending
			'wrap_widget',
			'wrap_widget_help',
			'text_products',
			'text_products_help',
			'text_image_dimensions',
			'text_image_dimensions_help',
			'text_pixels',
			'text_panel_name',
			'text_panel_name_help',
			'text_products_small',
			'show_add_to_cart',
			'show_add_to_cart_help',
            'text_invalid_account',
            'text_connected_account'
        );
       
        foreach ($languageVariables as $languageVariable) {
            $this->data[$languageVariable] = $this->language->get($languageVariable);
        }
 
        $this->data['stores'] = array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' (' . $this->data['text_default'].')', 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
        $this->data['languages']              = $this->model_localisation_language->getLanguages();
        $this->data['store']                  = $store;
        $this->data['token']                  = $this->session->data['token'];
        $this->data['action']                 = $this->url->link('module/'.$this->moduleNameSmall, 'token=' . $this->session->data['token'], 'SSL');
        $this->data['login_action']           = $this->url->link('module/'.$this->moduleNameSmall.'/perpettoFirstLogin', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel']                 = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['change_slot_position']   = htmlspecialchars_decode($this->url->link('module/perpetto/editSlotPosition','token=' . $this->session->data['token'], 'SSL'));
        $this->data['data']		         	= $this->model_setting_setting->getSetting($this->moduleNameSmall, $store['store_id']);

        if(!empty($this->data['data']['perpetto']['positions'])) {
            $this->data['positions'] = $this->data['data']['perpetto']['positions'];
        } else {
            $this->data['positions'] = array();
        }
        

        $this->data['catalog_url']			= $catalogURL;
		  
		$this->data['moduleData']				= isset($this->data['data'][$this->moduleNameSmall]) ? $this->data['data'][$this->moduleNameSmall] : array ();
		
        if(empty($this->data['data'])) {
            $this->template = 'module/perpetto/first_login.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );
            $this->response->setOutput($this->render());
        } else {

            if(!empty($this->data['data'][$this->moduleNameSmall]['account_id']) && !empty($this->data['data'][$this->moduleNameSmall]['secret'])) {
                $account_id = $this->data['data'][$this->moduleNameSmall]['account_id'];
                $secret = $this->data['data'][$this->moduleNameSmall]['secret'];

                $account_request_data = array(
                    'method'        => 'info',
                    'account_id'    => $account_id,
                    'secret'        => $secret
                );

                $account_info = $this->model_module_perpetto->perpettoCall($account_request_data);

                if(!isset($account_info->error) && !empty($account_info->data)) {

                    $this->data['account_info'] = $account_info;

                    $this->data['card_added'] = $account_info->data->store->has_card;
                    
                    if($account_info->data->store->trial_days_left > 0) {
                        $this->data['trial_days_left'] = $account_info->data->store->trial_days_left;
                    } else {
                        if(!$this->data['card_added']) {
                            $this->data['error_warning'] = $this->language->get('text_expired_trial');
                        }
                        $this->data['trial_days_left'] = 0;
                    }

                    $request_data = array(
                        'method'        => 'slots',
                        'account_id'    => $account_id,
                        'secret'        => $secret
                    );

                    $result = $this->model_module_perpetto->perpettoCall($request_data);

                    if(!isset($result->error) && !empty($result->data)) {
                        $slots = $result;
                        foreach($slots->data->slots as $slot) {
                           $grouped_slots[$slot->page][] = $slot;
                        }
                        $this->data['slots'] = $grouped_slots;
                    } else {
                        if(!empty($result->error)) {
                            $this->data['error_warning'] = $result->error;
                        } else {
                            $this->data['error_warning'] = $this->language->get('text_invalid_account');
                        }
                    }

                } else {
                    if(!empty($result->error)) {
                        $this->data['error_warning'] = $result->error;
                    } else {
                        $this->data['error_warning'] = $this->language->get('text_invalid_account');
                    }
                }

            } else {
                $this->data['error_warning'] = $this->language->get('text_invalid_account');
            } 

            $enabled_ssl = $this->config->get('config_secure');

            $this->template = 'module/'.$this->moduleNameSmall.'.tpl';
            
            $this->children = array(
                'common/header',
                'common/footer'
            );
            
            if(!empty($enabled_ssl)) {
                $this->data['live_preview_link'] = HTTPS_CATALOG."index.php?route=product/product&product_id=".$this->model_module_perpetto->getRandomProductId()."&ptto_env=PREVIEW";
            } else {
                $this->data['live_preview_link'] = HTTP_CATALOG."index.php?route=product/product&product_id=".$this->model_module_perpetto->getRandomProductId()."&ptto_env=PREVIEW";
            }

            $this->response->setOutput($this->render());
        }
    }

    public function perpettoFirstLogin() {
        $this->load->model('setting/setting');
        $this->load->model('module/'.$this->moduleNameSmall);
        $this->load->language('module/'.$this->moduleNameSmall);
        $account_id = $this->request->post[$this->moduleName]['account_id'];
        $secret = $this->request->post[$this->moduleName]['secret'];

        if(empty($account_id) || empty($secret)) {
            $this->session->data['error_warning'] = "All fields must be filled!";
        } else {     

            $request_data = array(
                'method'        => 'info',
                'account_id'    => $account_id,
                'secret'        => $secret
            );

            $result = $this->model_module_perpetto->perpettoCall($request_data);

            if(!isset($result->error) && !empty($result->data)) { 
                $this->request->post[$this->moduleNameSmall]['connected'] = 'yes';

                $slots = $result;
                if(!empty($slots->error)) {
                    $this->data['error_warning'] = $this->language->get('text_invalid_account');
                } else {
                    foreach($slots->data->slots as $slot) {
                        $grouped_slots[$slot->page][] = $slot;
                    }
                    $this->data['slots'] = $grouped_slots;
                }

                $this->load->model('design/layout');
                $layouts = $this->model_design_layout->getLayouts();

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
                            //var_dump($slot);exit;
                            $slot_positions[$slot['layout_id']][] = $slot['position'];
                           // $this->editSlotPosition($slot['token'],$slot['position'],$this->request->post['store_id']);
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

                $this->model_setting_setting->editSetting($this->moduleNameSmall, $this->request->post, $this->request->post['store_id']);
                $this->session->data['success'] = $this->language->get('text_connected_account'); 

            } else {
                if(!empty($result->error)) {
                    $this->session->data['error_warning'] = $result->error;
                } else {
                    $this->session->data['error_warning'] = $this->language->get('text_invalid_account');
                }
            }

            
        }

        $this->redirect($this->url->link('module/'.$this->moduleNameSmall, 'store_id='.$this->request->post['store_id'] . '&token=' . $this->session->data['token'], 'SSL'));
                

    }

    public function editSlotPosition($token, $position, $store_id) {
        $this->load->model('module/'.$this->moduleNameSmall);

        $token_info_from_db = $this->model_module_perpetto->checkIfSlotExists($token,$store_id);
        if(!empty($token_info_from_db)) {
            if($position != 'not_set') {
                $query = $this->db->query("UPDATE `" . DB_PREFIX . "perpetto_slots` SET position = '".$this->db->escape($position)."' WHERE token = '".$this->db->escape($token)."'");
            }
            else {
                $query = $this->db->query("UPDATE `" . DB_PREFIX . "perpetto_slots` SET position = 0 WHERE token = '".$this->db->escape($token)."'");
            }
        }

        $this->response->setOutput(json_encode('success')); 
    }
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'module/'.$this->moduleNameSmall)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

    private function getCatalogURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_CATALOG;
        } else {
            $storeURL = HTTP_CATALOG;
        } 
        return $storeURL;
    }

    private function getServerURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        } 
        return $storeURL;
    }

    private function getCurrentStore($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL(); 
        }
        return $store;
    }
    
    public function install() {
	    $this->load->model('module/'.$this->moduleNameSmall);
        $this->load->language('module/'.$this->moduleNameSmall);
	    $this->{$this->moduleModel}->install($this->moduleNameSmall);
        $this->session->data['success'] = $this->language->get('text_completed_installation');
        $this->redirect($this->url->link('module/'.$this->moduleNameSmall, 'store_id='.$this->config->get('config_store_id') . '&token=' . $this->session->data['token'], 'SSL'));

    }
    
    public function uninstall() {
    	$this->load->model('setting/setting');
		
		$this->load->model('setting/store');
		$this->model_setting_setting->deleteSetting($this->moduleData_module,0);
		$stores=$this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$this->model_setting_setting->deleteSetting($this->moduleData_module, $store['store_id']);
		}
		
        $this->load->model('module/'.$this->moduleNameSmall);
        $this->{$this->moduleModel}->uninstall($this->moduleNameSmall);
    }
}

?>
