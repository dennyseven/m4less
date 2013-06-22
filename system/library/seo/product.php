<?php
class SeoProduct extends SeoAbstract {

	private $_image;

	private $_name;

	private $_model;
	
	public function getModel() {
		return $this->_model;
	}


	public function getImage() {
		return $this->_image;
	}

	public function getName() {
		return $this->_name;
	}

	public function load() {

		$sql = "SELECT sd.title, pd.meta_keyword, pd.meta_description, pd.language_id FROM `" . DB_PREFIX . "product_description` pd
		LEFT JOIN `" . DB_PREFIX . "seo_data` sd ON 
		((sd.id = pd.product_id AND sd.type='product') AND (sd.language_id = pd.language_id)) 
		WHERE pd.product_id = '" . (int) $this->_id . "'";

		$query = $this->_db->query($sql);

		foreach($query->rows as $row) {
				
			$this->_title[$row['language_id']] = $row['title'];
			$this->_meta_keywords[$row['language_id']] = $row['meta_keyword'];
			$this->_meta_description[$row['language_id']] = $row['meta_description'];
				
		}

		$config = $this->_registry->get('config');
		
		$query = $this->_db->query("SELECT DISTINCT *,
		(SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$this->_id . "') AS keyword
		FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd
		ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$this->_id . "'
		AND pd.language_id = '" . (int)$config->get('config_language_id') . "'");

		$this->_name = isset($query->row['name']) ? $query->row['name'] : '';
		$this->_model = isset($query->row['model']) ? $query->row['model'] : '';
		$this->_image = isset($query->row['image']) ? $query->row['image'] : '';
		$this->_seo_keyword = isset($query->row['keyword']) ? $query->row['keyword'] : '';

	}
	
	public function save() {
		
		$db = $this->_registry->get('db');
		
		$query = $db->query("SELECT url_alias_id FROM `" . DB_PREFIX . "seo_data` 
							WHERE id = '" . $db->escape($this->_id) . "' AND type = 'product' ");
		
		if($query->num_rows) {
			$db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE url_alias_id = '" . (int) $query->row['url_alias_id'] . "'");
			$db->query("DELETE FROM `" . DB_PREFIX . "seo_data` WHERE id = '" . $db->escape($this->_id) . "' AND type= 'product' ");
		}
		
		$url_alias_id = 0;
		
		if($this->_seo_keyword){

			$sql = "SELECT COUNT(*) as count FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $db->escape($this->_url_query) . "'";
			$already_existing = $db->query($sql)->row['count'];
			if($already_existing) {
				$db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $db->escape($this->_url_query) . "'");
			}
			
			$db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET `query` = '" . $db->escape($this->_url_query) . "',
						`keyword` = '" . $db->escape($this->_seo_keyword) . "'");
			
			$url_alias_id = $db->getLastId();
		}
		
		foreach($this->_title as $language_id => $row ){
			$db->query("INSERT INTO `" . DB_PREFIX . "seo_data` 
						SET `title` = '" . $db->escape($this->_title[$language_id]) . "',
						`type` = 'product',
						`id` = '" . $db->escape($this->_id) . "',
						`language_id` = '" . (int) $language_id . "',
						`url_alias_id` = '" . (int) $url_alias_id . "'
						");
			
			$db->query("UPDATE `" . DB_PREFIX . "product_description` SET
						`meta_keyword` = '" . $db->escape($this->_meta_keywords[$language_id]) . "',
						`meta_description` = '" . $db->escape($this->_meta_description[$language_id]) . "'
						WHERE product_id = '" . $db->escape($this->_id) . "' AND language_id = '". (int) $language_id ."'
						");
		} 
		
		
		//delete product cache
		$cache = $this->_registry->get('cache');
		$cache->delete('product');
		
	}

}
