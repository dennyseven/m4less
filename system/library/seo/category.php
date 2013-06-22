<?php
class SeoCategory extends SeoAbstract {
	
	private $_name;

	public function getName() {
		return $this->_name;
	}

	public function load() {

		$sql = "SELECT sd.title, cd.meta_keyword, cd.meta_description, cd.language_id FROM `" . DB_PREFIX . "category_description` cd
		LEFT JOIN `" . DB_PREFIX . "seo_data` sd ON 
		((sd.id = cd.category_id AND sd.type='category') AND (sd.language_id = cd.language_id)) 
		WHERE cd.category_id = '" . (int) $this->_id . "'";

		$query = $this->_db->query($sql);

		foreach($query->rows as $row) {
				
			$this->_title[$row['language_id']] = $row['title'];
			$this->_meta_keywords[$row['language_id']] = $row['meta_keyword'];
			$this->_meta_description[$row['language_id']] = $row['meta_description'];
				
		}

		$config = $this->_registry->get('config');
		
		$query = $this->_db->query("SELECT DISTINCT *, (SELECT keyword 
									FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$this->_id . "') AS keyword 
									FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$this->_id . "'");

		$this->_name = $this->getPath($this->_id);
		$this->_seo_keyword = isset($query->row['keyword']) ? $query->row['keyword'] : '';

	}
	
	public function getPath($category_id) {
		
		$config = $this->_registry->get('config');
		$language = $this->_registry->get('language');
		$db = $this->_registry->get('db');
		
		$query = $db->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c 
									LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) 
									WHERE c.category_id = '" . (int)$category_id . "' 
									AND cd.language_id = '" . (int)$config->get('config_language_id') . "' 
									ORDER BY c.sort_order, cd.name ASC");
		
		$category_info = $query->row;
		
		if($category_info) {
			if ($category_info['parent_id']) {
				return $this->getPath($category_info['parent_id'], $config->get('config_language_id')) . $language->get('text_separator') . $category_info['name'];
			} else {
				return $category_info['name'];
			}
		}
	}
	
	public function save() {
		
		$db = $this->_registry->get('db');
		
		$query = $db->query("SELECT url_alias_id FROM `" . DB_PREFIX . "seo_data` 
							WHERE id = '" . $db->escape($this->_id) . "' AND type= 'category' ");
		
		if($query->num_rows) {
			$db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE url_alias_id = '" . (int) $query->row['url_alias_id'] . "'");
			$db->query("DELETE FROM `" . DB_PREFIX . "seo_data` WHERE  id = '" . $db->escape($this->_id) . "' AND type= 'category' ");
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
						`type` = 'category',
						`id` = '" . $db->escape($this->_id) . "',
						`language_id` = '" . (int) $language_id . "',
						`url_alias_id` = '" . (int) $url_alias_id . "'
						");
			
			$db->query("UPDATE `" . DB_PREFIX . "category_description` SET
						`meta_keyword` = '" . $db->escape($this->_meta_keywords[$language_id]) . "',
						`meta_description` = '" . $db->escape($this->_meta_description[$language_id]) . "'
						WHERE category_id = '" . $db->escape($this->_id) . "' AND language_id = '". (int) $language_id ."' 
						");
		} 
		
		
		//delete category cache
		$cache = $this->_registry->get('cache');
		$cache->delete('category');
		
	}
	
}
