<?php
class ModelModulePerpetto extends Model {
  
  	public function getSetting($code, $store_id) {
	    $this->load->model('setting/setting');
		return $this->model_setting_setting->getSetting($code,$store_id);
  	}

  	public function editSetting($group, $data, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");

		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
			}
		}
	}

  	public function getSlotInfo($token, $store_id) {
  		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "perpetto_slots` WHERE token = '".$token."' and store_id = '".$store_id."'");
  		return $query->row;
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

	public function setModuleToLayout($slot_page,$position) {
		$layout_id = 0;

		switch($slot_page) {
			case 'home_page': $layout_id = 1; break; 
			case 'product_page': $layout_id = 2; break; 
			case 'category_page': $layout_id = 3; break; 
			case 'cart_page': $layout_id = 7; break; 
		}

		if(!empty($position)){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX ."layout_module WHERE layout_id = '".$layout_id."' AND position = '".$position."' and code = '" . $this->db->escape('perpetto')."'");
			if($query->num_rows < 1) {

				$order = $this->db->query("SELECT sort_order FROM " . DB_PREFIX ."layout_module WHERE layout_id = '".$layout_id."' AND position = '".$position."' and code != '" . $this->db->escape('perpetto')."' ORDER BY sort_order DESC");
				if(!empty($order->row['sort_order'])) {
					$sort_order = (int)$order->row['sort_order'] + 1;
				} else {
					$sort_order = 0;
				}

				$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module 
				SET layout_id = '" . (int)$layout_id . "', code = '" . $this->db->escape('perpetto') . "', position = '" . 
				$this->db->escape($position) . "', sort_order = '".(int)$sort_order."'");

			}
		} 

		$this->load->model('design/layout');
	}

	public function getLayouts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "layout";

		$sort_data = array('name');

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function deleteAllModuleLayouts($moduleName = "perpetto") {
		$layouts = array();
		$layouts = $this->getLayouts();
			
		foreach ($layouts as $layout) {
			$this->db->query("DELETE FROM " . DB_PREFIX . 
				"layout_module 
				WHERE layout_id = '" . (int)$layout['layout_id'] . "' and  
				code = '" . $this->db->escape($moduleName)."'");
		}
	}

}
?>