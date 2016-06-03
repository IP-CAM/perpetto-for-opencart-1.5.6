<?php 
class ModelModulePerpetto extends Model {

	public function getSetting($code, $store_id = 0) {
	    $this->load->model('setting/setting');
		return $this->model_setting_setting->getSetting($code,$store_id);
	}
  
  	public function editSetting($code, $data, $store_id = 0) {
	    $this->load->model('setting/setting');
		$this->model_setting_setting->editSetting($code,$data,$store_id);
	}

	public function getRandomProductId() {
		 $random_product_id = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product LIMIT 1");
		 if($random_product_id->num_rows > 0) {
		 	return $random_product_id->row['product_id'];
		 } else {
		 	return 0;
		 }
	}

	public function checkIfSlotExists($token, $store_id=0) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "perpetto_slots` WHERE token = '".$token."' and store_id = '".$store_id."'");
		if($query->num_rows<1) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "perpetto_slots` (token,position,store_id)
								VALUES ('".$token."','content_bottom' ,'".$store_id."');");
			return true;
		} else {
			return $query->row;
		}
	}

	public function setModuleToLayout($slot, $moduleData_module) {
		$layout_id = 0;

		switch($slot->page) {
			case 'home_page': $layout_id = 1; break; 
			case 'product_page': $layout_id = 2; break; 
			case 'category_page': $layout_id = 3; break; 
			case 'cart_page': $layout_id = 7; break; 
		}

		if(!empty($slot->position)) {
			//$query = $this->db->query("SELECT * FROM " . DB_PREFIX ."setting WHERE layout_id = '".$layout_id."' AND position = '".$slot->position."' and code = '" . $this->db->escape('perpetto')."'");
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX ."setting WHERE 'key' LIKE '%".$moduleData_module."%' LIMIT 1");

			if($query->num_rows > 0) {

			} else {
				$module_layouts = $this->db->query("SELECT * FROM " . DB_PREFIX ."setting WHERE `key` LIKE '%_module'");
				if($module_layouts->num_rows > 0) {
					$sort_orders = array();
					foreach($module_layouts->rows as $module) {

						$value = unserialize($module['value']);
						foreach($value as $v) {
							if($v['layout_id'] == $layout_id && $module['key'] != $moduleData_module) {
								$sort_orders[] = $v['sort_order'];
							}
						}
						
					}

					if(!empty($sort_orders)) {
						arsort($sort_orders);
						$sort_order = (int)reset($sort_orders) + 1;
					} else {
						$sort_order = 0;
					}
					
					
				}
			}

			
			if($query->num_rows < 1) {

				$order = $this->db->query("SELECT sort_order FROM " . DB_PREFIX ."layout_module WHERE layout_id = '".$layout_id."' AND position = '".$slot->position."' and code != '" . $this->db->escape('perpetto')."' ORDER BY sort_order DESC");
				if(!empty($order->row['sort_order'])) {
					$sort_order = (int)$order->row['sort_order'] + 1;
				} else {
					$sort_order = 0;
				}

				$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module 
				SET layout_id = '" . (int)$layout_id . "', code = '" . $this->db->escape('perpetto') . "', position = '" . 
				$this->db->escape($slot->position) . "', sort_order = '".(int)$sort_order."'");

			}
		} 

		$this->load->model('design/layout');
	}

	public function deleteAllModuleLayouts($moduleName = "perpetto") {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting 
			WHERE 'key' LIKE '%" . $this->db->escape($moduleName)."_module%'");
	}

	public function perpettoCall($data) {
		if(empty($data['method']) || empty($data['account_id']) || empty($data['secret'])) return false; // return if the request is not structured correctly

		switch($data['method']) {
			case 'info': 
				$url = "https://".$data['account_id'].".api.perpetto.com/v0/info?secret=".$data['secret'];
				break;
			case 'slots':
				$url = "https://".$data['account_id'].".api.perpetto.com/v0/info/slots?secret=".$data['secret'];
				break;
		}
			
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if( ($result = curl_exec($ch) ) !== false) {
            $result = json_decode($result);
        } else {
        	$result = false;
        }

		return $result;
	}
	
  	public function install($moduleName) {
		$query = $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "perpetto_slots`
			(`token` VARCHAR(25) NOT NULL UNIQUE, 
			 `position` VARCHAR(25) NULL DEFAULT NULL,
			 `store_id` INT(2) NULL DEFAULT 0);");	
  	} 
  
  	public function uninstall($moduleName) {
  		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "perpetto_slots`");
  		$this->deleteAllModuleLayouts($moduleName);		
  	}
	
  }
?>