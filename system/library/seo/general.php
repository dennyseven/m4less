<?php
class SeoGeneral extends SeoAbstract {

	public function load() {
		$sql = "SELECT * FROM `" . DB_PREFIX . "seo_data`
				WHERE `type` = 'general'
				AND `id` = '" . $this->_db->escape($this->_id) . "'";

		$query = $this->_db->query($sql);

		if($query->num_rows) {
			foreach($query->rows as $row) {
				$this->_title[$row['language_id']] = $row['title'];
				$this->_meta_keywords[$row['language_id']] = $row['meta_keywords'];
				$this->_meta_description[$row['language_id']] = $row['meta_description'];
			}
	
			$query2 = $this->_db->query("SELECT * FROM `" . DB_PREFIX . "url_alias` WHERE query = 'route=" . $this->_db->escape($this->_id) . "'");
	
			$this->_seo_keyword = isset($query2->row['keyword']) ? $query2->row['keyword'] : '';
			$this->_url_query = isset($query2->row['query']) ? $query2->row['query'] : '';
			$this->_url_alias_id = isset($query2->row['url_alias_id']) ? $query2->row['url_alias_id'] : '';
			
		}

	}
	
	public function save() {
		
		$db = $this->_registry->get('db');
		
		$query = $db->query("SELECT url_alias_id FROM `" . DB_PREFIX . "seo_data` WHERE id = '" . $db->escape($this->_id) . "'");
		
		if($query->num_rows) {
			$db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE url_alias_id = '" . (int) $query->row['url_alias_id'] . "'");
			$db->query("DELETE FROM `" . DB_PREFIX . "seo_data` WHERE url_alias_id = '" . (int) $query->row['url_alias_id'] . "'");
		}

		$sql = "SELECT COUNT(*) as count FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $db->escape($this->_url_query) . "'";
		$already_existing = $db->query($sql)->row['count'];
		if($already_existing) {
			$db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $db->escape($this->_url_query) . "'");
		}
		
		$db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET `query` = '" . $db->escape($this->_url_query) . "',
					`keyword` = '" . $db->escape($this->_seo_keyword) . "'");
		
		$url_alias_id = $db->getLastId();
		
		foreach($this->_title as $language_id => $row ){
			$db->query("INSERT INTO `" . DB_PREFIX . "seo_data` 
						SET `title` = '" . $db->escape($this->_title[$language_id]) . "',
						`meta_keywords` = '" . $db->escape($this->_meta_keywords[$language_id]) . "',
						`meta_description` = '" . $db->escape($this->_meta_description[$language_id]) . "',
						`type` = 'general',
						`id` = '" . $db->escape($this->_id) . "',
						`language_id` = '" . (int) $language_id . "',
						`url_alias_id` = '" . (int) $url_alias_id . "'
						");
		} 
	}

}