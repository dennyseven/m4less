<?php

require 'abstract.php';
require 'general.php';
require 'product.php';
require 'category.php';
require 'manufacturer.php';
require 'information.php';

final class Seo {

	public static function getObject($type, $id, $registry) {
		switch($type) {
			case 'general':
				$object = new SeoGeneral($registry);
				break;
			case 'product':
				$object = new SeoProduct($registry);
				break;
			case 'category':
				$object = new SeoCategory($registry);
				break;
			case 'manufacturer':
				$object = new SeoManufacturer($registry);
				break;
			case 'information':
				$object = new SeoInformation($registry);
				break;
		}


		$object->setDb($registry->get('db'));
		$object->setId($id);
		$object->load();
		return $object;
	}

	public static function getType($data) {

		if(isset($data['route'])){
			switch ($data['route']) {
				case 'product/product' :
					$return = array('type' => 'product', 'id' => $data['product_id']);
					break;
				case 'product/category' :
					if(!isset($data['path'])){
						$return = array('type' => 'general', 'id' => $data['route']);
						break;
					}
					$arr = explode('_', $data['path']);
					$category_id = end($arr);
					$return = array('type' => 'category', 'id' => $category_id);
					break;				
				case 'product/manufacturer/info' :
					$return = array('type' => 'manufacturer', 'id' => $data['manufacturer_id']);
					break;
				case 'information/information' :
					$return = array('type' => 'information', 'id' => $data['information_id']);
					break;
				default:
					$return = array('type' => 'general', 'id' => $data['route']);
			}
		} else {
			//no route present then take default common/home
			$return = array('type' => 'general', 'id' => 'common/home');
		}
		
		return $return;
	}

	public static function findGeneral($registry) {

		$db = $registry->get('db');

		$sql = "SELECT id FROM `" . DB_PREFIX . "seo_data` WHERE `type` = 'general'";

		$query = $db->query($sql);

		$object_array = array();

		foreach($query->rows as $row) {
			$object_array[] = self::getObject('general', $row['id'], $registry);
		}

		return $object_array;
	}

	public static function findGeneralTotal($registry) {

		$db = $registry->get('db');

		$sql = "SELECT id FROM `" . DB_PREFIX . "seo_data` WHERE `type` = 'general'";

		$query = $db->query($sql);

		return $query->num_rows;
	}

	public static function findProducts($registry, $data = array()) {

		$db = $registry->get('db');
		$config_language_id = $registry->get('config')->get('config_language_id');

        $sql = "SELECT p.product_id FROM `" . DB_PREFIX . "product` p INNER JOIN `".DB_PREFIX."product_description` pd ON (p.`product_id` = pd.`product_id`) WHERE pd.language_id = '" . (int)$config_language_id . "' ";
        
        if (!empty($data['filter_keyword'])) {
		    $sql .= " AND LCASE(pd.name) LIKE '%" . $db->escape(utf8_strtolower($data['filter_keyword'])) . "%'";
		}		
		
		if(isset($data['limit']) || isset($data['start'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }               

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }   
            
                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $db->query($sql);

		$object_array = array();

		foreach($query->rows as $row) {
			$object_array[] = self::getObject('product', $row['product_id'], $registry);
		}

		return $object_array;

	}

	public static function findProductsTotal($registry, $data = array()) {

		$db = $registry->get('db');
		$config_language_id = $registry->get('config')->get('config_language_id');

        $sql = "SELECT p.product_id FROM `" . DB_PREFIX . "product` p INNER JOIN `".DB_PREFIX."product_description` pd ON (p.`product_id` = pd.`product_id`) WHERE pd.language_id = '" . (int)$config_language_id . "' ";
        
        if (!empty($data['filter_keyword'])) {
		    $sql .= " AND LCASE(pd.name) LIKE '%" . $db->escape(utf8_strtolower($data['filter_keyword'])) . "%'";
		}			

		$query = $db->query($sql);

		return $query->num_rows;

	}

	public static function findCategories($registry) {

		$db = $registry->get('db');

		$sql = "SELECT category_id FROM `" . DB_PREFIX . "category`";

		$query = $db->query($sql);

		$object_array = array();

		foreach($query->rows as $row) {
			$object_array[] = self::getObject('category', $row['category_id'], $registry);
		}

		return $object_array;

	}

	public static function findCategoriesTotal($registry) {

		$db = $registry->get('db');

		$sql = "SELECT category_id FROM `" . DB_PREFIX . "category`";

		$query = $db->query($sql);

		return $query->num_rows;

	}


	public static function findManufacturers($registry) {
		$db = $registry->get('db');
		
		$sql = "SELECT manufacturer_id FROM `" . DB_PREFIX . "manufacturer`";
		
		$query = $db->query($sql);
		
		$object_array = array();
		
		foreach($query->rows as $row) {
			$object_array[] = self::getObject('manufacturer', $row['manufacturer_id'], $registry);
		}
		
		return $object_array;
	}

	public static function findManufacturersTotal($registry) {

		$db = $registry->get('db');
		
		$sql = "SELECT manufacturer_id FROM `" . DB_PREFIX . "manufacturer`";
		
		$query = $db->query($sql);

		return $query->num_rows;

	}

	public static function findInformations($registry) {
		$db = $registry->get('db');
		
		$sql = "SELECT information_id FROM `" . DB_PREFIX . "information`";
		
		$query = $db->query($sql);
		
		$object_array = array();
		
		foreach($query->rows as $row) {
			$object_array[] = self::getObject('information', $row['information_id'], $registry);
		}
		
		return $object_array;
	}

	public static function findInformationsTotal($registry) {

		$db = $registry->get('db');
		
		$sql = "SELECT information_id FROM `" . DB_PREFIX . "information_description`";
		
		$query = $db->query($sql);

		return $query->num_rows;

	}
	
}
