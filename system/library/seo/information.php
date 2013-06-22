<?php
class SeoInformation extends SeoAbstract { 

	private $_name;

	public function getName() {
		return $this->_name;
	}

	public function load() {

		$sql = "SELECT sd.title, id.language_id, sd.meta_keywords, sd.meta_description FROM `" . DB_PREFIX . "information_description` id LEFT JOIN `" . DB_PREFIX . "seo_data` sd ON (sd.id = id.information_id AND sd.type='information' AND sd.language_id = id.language_id) WHERE id.information_id = '" .(int) $this->_id . "'";
		
		$query = $this->_db->query($sql);

		foreach($query->rows as $row) {
				
			$this->_title[$row['language_id']] = $row['title'];
			$this->_meta_keywords[$row['language_id']] = $row['meta_keywords'];
			$this->_meta_description[$row['language_id']] = $row['meta_description'];
				
		}

		$config = $this->_registry->get('config');

		$query = $this->_db->query("SELECT DISTINCT *,
		(SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$this->_id . "') AS keyword FROM " . DB_PREFIX . "information_description id WHERE id.information_id = '" . (int)$this->_id ."'");
		$this->_name = isset($query->row['title']) ? $query->row['title'] : '';
		$this->_seo_keyword = isset($query->row['keyword']) ? $query->row['keyword'] : '';
		
	}

	public function save() {
				
		$db = $this->_registry->get('db');
		
		$query = $db->query("SELECT url_alias_id FROM `" . DB_PREFIX . "seo_data` 
							WHERE id = '" . $db->escape($this->_id) . "' AND type= 'information' ");
		
		if($query->num_rows) {
			$db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE url_alias_id = '" . (int) $query->row['url_alias_id'] . "'");
			$db->query("DELETE FROM `" . DB_PREFIX . "seo_data` WHERE id = '" . $db->escape($this->_id) . "' AND type= 'information' ");
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
						`meta_keywords` = '" . $db->escape($this->_meta_keywords[$language_id]) . "',
						`meta_description` = '" . $db->escape($this->_meta_description[$language_id]) . "',
						`type` = 'information',
						`id` = '" . $db->escape($this->_id) . "',
						`language_id` = '" . (int) $language_id . "',
						`url_alias_id` = '" . (int) $url_alias_id . "'
						");
		} 
		
		//delete information cache
		$cache = $this->_registry->get('cache');		
		$cache->delete('information');
		
	}

}

?>
