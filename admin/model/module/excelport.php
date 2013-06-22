<?php 
class ModelModuleExcelport extends Model {
	private $now;
	
	public function __construct($register) {
		if (!defined('IMODULE_ROOT')) define('IMODULE_ROOT', substr(DIR_APPLICATION, 0, strrpos(DIR_APPLICATION, '/', -2)) . '/');
		if (!defined('IMODULE_ADMIN_ROOT')) define('IMODULE_ADMIN_ROOT', DIR_APPLICATION);
		if (!defined('IMODULE_SERVER_NAME')) define('IMODULE_SERVER_NAME', substr((defined('HTTP_CATALOG') ? HTTP_CATALOG : HTTP_SERVER), 7, strlen((defined('HTTP_CATALOG') ? HTTP_CATALOG : HTTP_SERVER)) - 8));
		
		$this->now = time();
		parent::__construct($register);
	}
	
	public function getSetting($group, $store_id = 0) {
		$data = array(); 
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
		
		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			} else {
				$data[$result['key']] = unserialize($result['value']);
			}
		}

		return $data;
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
	
	public function deleteSetting($group, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
	}
	
	public function cleanTemp($tempDir = '../temp') {
		$files = scandir($tempDir);
		foreach ($files as $file) {
			if (!in_array($file, array('.', '..', 'index.html'))) {
				if (is_file($tempDir.'/'.$file)) unlink ($tempDir.'/'.$file);
				if (is_dir($tempDir.'/'.$file)) {
					$this->cleanTemp($tempDir.'/'.$file);	
					rmdir($tempDir.'/'.$file);
				}
			}
		}
	}
	
	public function clearInvalidEntries($folders) {
		$result = array();
		foreach	($folders as $folder) {
			if ($folder != '') {
				$result[] = trim($folder);	
			}
		}
		return $result;
	}
	
	public function importXLS($type, $language, $file = '', $importLimit) {
		if (!file_exists($file)) throw new Exception($this->language->get('excelport_file_not_exists'));
		if (!is_numeric($importLimit) || $importLimit < 10 || $importLimit > 800) throw new Exception($this->language->get('excelport_import_limit_invalid'));
		
		$valid = false;
		switch (VERSION) {
			case '1.5.4' : { $valid = true; } break;
			case '1.5.4.1' : { $valid = true; } break;
			case '1.5.5' : { $valid = true; } break;
			case '1.5.5.1' : { $valid = true; } break;
		}
		if (!$valid) throw new Exception(str_replace('{VERSION}', '1.5.4.x and 1.5.5.x', $this->language->get('text_feature_unsupported')));
		
		$progress = $this->getProgress();
		$progress['importedCount'] = !empty($progress['importedCount']) ? $progress['importedCount'] : 0;
		$progress['done'] = false;
		
		require_once(IMODULE_ROOT.'vendors/phpexcel/PHPExcel.php');
		// Create new PHPExcel object
		
		require_once(IMODULE_ROOT.'vendors/phpexcel/CustomReadFilter.php');
		$chunkFilter = new CustomReadFilter(array('Products' => array('A', (26*$progress['importedCount'] + 2), 'P', (26*($progress['importedCount'] + $importLimit) + 1)))); 
		
		$madeImports = false;
		$objReader = new PHPExcel_Reader_Excel2007();
		$objReader->setReadFilter($chunkFilter);
		$objReader->setReadDataOnly(true);
		//$objReader->setLoadSheetsOnly(array("Products"));
		$objPHPExcel = $objReader->load($file);
		$progress['importingFile'] = substr($file, strripos($file, '/') + 1);
		$productsSheet = 0;
		$legendSheet = 1;
		$productSheetObj = $objPHPExcel->setActiveSheetIndex($productsSheet);
		$legendSheetObj = $objPHPExcel->setActiveSheetIndex($legendSheet);
		
		$progress['all'] = -1;//(int)(($productSheetObj->getHighestRow() - 2)/26);
		$this->setProgress($progress);
		
		if ($type == 'Products') {
			//$ids = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product");
			$this->load->model('catalog/product');
			$optionValues = array();
			
			$optionValuesStart = array(14,30);
			$i = 0;
			do {
				$tempOptionValue = $legendSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($optionValuesStart[0]) . ($optionValuesStart[1] + $i))->getValue();
				if (!empty($tempOptionValue)) {
					$optionValues[] = $tempOptionValue;
				}
				$i++;
			} while(!empty($tempOptionValue));
			
			$map = array(
				'product_id' 		=> array(0,0),
				'name'				=> array(1,0),
				'model'				=> array(2,0),
				'meta_description' 	=> array(1,2),
				'meta_keyword' 		=> array(1,3),
				'description' 		=> array(15,0),
				'tag'				=> array(1,4),
				'sku'				=> array(1,6),
				'upc'				=> array(1,7),
				'ean'				=> array(1,8),
				'jan'				=> array(1,9),
				'isbn'				=> array(1,10),
				'mpn'				=> array(1,11),
				'location'			=> array(1,12),
				'price'				=> array(13,0),
				'tax_class'			=> array(1,13),
				'quantity'			=> array(12,0),
				'minimum'			=> array(1,14),
				'subtract'			=> array(1,15),
				'stock_status'		=> array(1,16),
				'shipping'			=> array(1,17),
				'keyword'			=> array(1,18),
				'image'				=> array(5,0),
				'date_available'	=> array(1,19),
				'length'			=> array(6,0),
				'width'				=> array(7,0),
				'height'			=> array(8,0),
				'length_class'		=> array(1,20),
				'weight'			=> array(9,0),
				'weight_class'		=> array(1,21),
				'status'			=> array(4,0),
				'sort_order'		=> array(1,22),
				'manufacturer'		=> array(10,0),
				'categories'		=> array(3,0),
				'stores'			=> array(11,0),
				'downloads'			=> array(1,24),
				'filters'			=> array(1,25),
				'related'			=> array(14,0),
				'attribute'			=> array(4,1),
				'option'			=> array(4,3),
				'discount'			=> array(4,9),
				'special'			=> array(4,15),
				'product_image'		=> array(4,20),
				'points'			=> array(4,23),
				'reward_points'		=> array(5,22),
				'design'			=> array(4,24)
			);
			
			$source = array(0,2 + 26*($progress['importedCount']));
			
			do {
				set_time_limit(60);
				$product_name = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['name'][0]) . ($source[1] + $map['name'][1]))->getValue();
				if (!empty($product_name)) {
					$product_model = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['model'][0]) . ($source[1] + $map['model'][1]))->getValue();
					$product_id = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['product_id'][0]) . ($source[1] + $map['product_id'][1]))->getValue();
					$product_price = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['price'][0]) . ($source[1] + $map['price'][1]))->getValue();
					$product_price = (float)str_replace(array(' ', ','), array('', '.'), $product_price);
					
					$this->load->model('localisation/tax_class');
					$product_tax_classes = $this->model_localisation_tax_class->getTaxClasses();
					$found = false;
					foreach ($product_tax_classes as $product_tax_class) {
						if ($product_tax_class['title'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['tax_class'][0]) . ($source[1] + $map['tax_class'][1]))->getValue()) {
							$found = true;
							$product_tax_class_id = $product_tax_class['tax_class_id'];
							break;
						}
					}
					if (!$found) $product_tax_class_id = 0;
					
					$product_quantity = (int)str_replace(' ', '', $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['quantity'][0]) . ($source[1] + $map['quantity'][1]))->getValue());
					$product_minimum = (int)str_replace(' ', '', $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['minimum'][0]) . ($source[1] + $map['minimum'][1]))->getValue());
					$product_subtract = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['subtract'][0]) . ($source[1] + $map['subtract'][1]))->getValue() == 'Yes' ? 1 : 0;
					
					$this->load->model('localisation/stock_status');
					$product_stock_statusses = $this->model_localisation_stock_status->getStockStatuses();
					$found = false;
					foreach ($product_stock_statusses as $product_stock_status) {
						if ($product_stock_status['name'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['stock_status'][0]) . ($source[1] + $map['stock_status'][1]))->getValue()) {
							$found = true;
							$product_stock_status_id = $product_stock_status['stock_status_id'];
							break;
						}
					}
					if (!$found) $product_stock_status_id = 0;
					
					$product_shipping = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['shipping'][0]) . ($source[1] + $map['shipping'][1]))->getValue() == 'Yes' ? 1 : 0;
					$product_length = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['length'][0]) . ($source[1] + $map['length'][1]))->getValue();
					$product_length = (float)str_replace(array(' ', ','), array('', '.'), $product_length);
					$product_width = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['width'][0]) . ($source[1] + $map['width'][1]))->getValue();
					$product_width = (float)str_replace(array(' ', ','), array('', '.'), $product_width);
					$product_height = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['height'][0]) . ($source[1] + $map['height'][1]))->getValue();
					$product_height = (float)str_replace(array(' ', ','), array('', '.'), $product_height);
					
					$this->load->model('localisation/length_class');
					$product_length_classes = $this->model_localisation_length_class->getLengthClasses();
					$found = false;
					foreach ($product_length_classes as $product_length_class) {
						if ($product_length_class['title'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['length_class'][0]) . ($source[1] + $map['length_class'][1]))->getValue()) {
							$found = true;
							$product_length_class_id = $product_length_class['length_class_id'];
							break;
						}
					}
					if (!$found) $product_length_class_id = 0;
					
					$product_weight = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['weight'][0]) . ($source[1] + $map['weight'][1]))->getValue();
					$product_weight = (float)str_replace(array(' ', ','), array('', '.'), $product_weight);
					
					$this->load->model('localisation/weight_class');
					$product_weight_classes = $this->model_localisation_weight_class->getWeightClasses();
					$found = false;
					foreach ($product_weight_classes as $product_weight_class) {
						if ($product_weight_class['title'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['weight_class'][0]) . ($source[1] + $map['weight_class'][1]))->getValue()) {
							$found = true;
							$product_weight_class_id = $product_weight_class['weight_class_id'];
							break;
						}
					}
					if (!$found) $product_weight_class_id = 0;
					
					$product_status = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['status'][0]) . ($source[1] + $map['status'][1]))->getValue() == 'Enabled' ? 1 : 0;
					$product_sort_order = (int)str_replace(' ', '', $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['sort_order'][0]) . ($source[1] + $map['sort_order'][1]))->getValue());
					
					$this->load->model('catalog/manufacturer');
					$product_manufacturers = $this->model_catalog_manufacturer->getManufacturers();
					$found = false;
					foreach ($product_manufacturers as $product_manufacturer) {
						if ($product_manufacturer['name'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['manufacturer'][0]) . ($source[1] + $map['manufacturer'][1]))->getValue()) {
							$found = true;
							$product_manufacturer_id = $product_manufacturer['manufacturer_id'];
							break;
						}
					}
					if (!$found) $product_manufacturer_id = 0;
					
					$product_store = array();
					$stores = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['stores'][0]) . ($source[1] + $map['stores'][1]))->getValue())));
					foreach ($stores as $store) {
						$store = trim($store);
						if ($store !== '') $product_store[] = $store;
					}
					
					$product_category = array();
					$categories = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['categories'][0]) . ($source[1] + $map['categories'][1]))->getValue())));
					foreach ($categories as $category) {
						$category = trim($category);
						if (!empty($category)) $product_category[] = trim($category);
					}
					
					$product_filter = array();
					if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
						$filters = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['filters'][0]) . ($source[1] + $map['filters'][1]))->getValue())));
						foreach ($filters as $filter) {
							$filter = trim($filter);
							if (!empty($filter)) $product_filter[] = trim($filter);
						}
					} 
					
					$product_download = array();
					$downloads = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['downloads'][0]) . ($source[1] + $map['downloads'][1]))->getValue())));
					foreach ($downloads as $download) {
						$download = trim($download);
						if (!empty($download)) $product_download[] = trim($download);
					}
					
					$product_related = array();
					$related = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['related'][0]) . ($source[1] + $map['related'][1]))->getValue())));
					foreach ($related as $relate) {
						$relate = trim($relate);
						if (!empty($relate)) $product_related[] = trim($relate);
					}
					
					$i = 0;
					$attributeName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['attribute'][0] + $i) . ($source[1] + $map['attribute'][1]))->getValue();
					$this->load->model('catalog/attribute');
					$attributes = $this->model_catalog_attribute->getAttributes();
					$product_attribute = array();
					while(!empty($attributeName)) {
						foreach ($attributes as $attribute) {
							if ($attribute['name'] == $attributeName) {
								$product_attribute[] = array(
									'name' => $attributeName,
									'attribute_id' => $attribute['attribute_id'],
									'product_attribute_description' => array(
										$language => array(
											'text' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['attribute'][0] + $i) . ($source[1] + $map['attribute'][1] + 1))->getValue()
										)
									)
								);
							}
						}
						$i++;
						$attributeName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['attribute'][0] + $i) . ($source[1] + $map['attribute'][1]))->getValue();	
					}
					
					$i = -1;
					$j = -1;
					$k = -1;
					$this->load->model('catalog/option');
					$options = $this->model_catalog_option->getOptions();
					$product_option = array();
					$product_option_value = array();
					do {
						$i++;
						$optionCell = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['option'][0] + $i) . ($source[1] + $map['option'][1]));
						$optionValue = $optionCell->getValue();
						$optionCellCheck = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['option'][0] + $i) . ($source[1] + $map['option'][1] + 1));
						$optionValueCheck = $optionCellCheck->getValue();
						if (!empty($optionValue)) {
							
							/*foreach($productSheetObj->getMergeCells() as $cells) {
								if ($optionCellCheck->isInRange($cells)) {
									$isParent = true;	
								}
							}*/
							$isParent = in_array($optionValueCheck, $optionValues);
							if ($isParent) {
								foreach ($options as $option) {
									if ($optionValueCheck == $option['name']) {
										$j++;
										$product_option[$j] = array(
											'product_option_id' => '',
											'name' => $option['name'],
											'option_id' => $option['option_id'],
											'type' => $option['type'],
											'required' => $optionValue == 'Required: Yes' ? 1 : 0,
											'option_value' => ''
										);
									}
								}
							} else {
								if (!empty($product_option[$j]['option_id'])) {
									if (in_array($product_option[$j]['type'], array('radio', 'checkbox', 'select'))) {
										$option_values = $this->model_catalog_option->getOptionValues($product_option[$j]['option_id']);
										foreach ($option_values as $option_value) {
											if ($option_value['name'] == $optionValue) {
												$k++;
												$product_option_value_price = trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['option'][0] + $i) . ($source[1] + $map['option'][1] + 3))->getValue());
												$product_option_value_price_prefix = stripos($product_option_value_price, '-') === 0 ? '-' : '+';
												$product_option_value_price = (float)str_replace(array('+', '-', ' ', ','), array('', '', '', '.'), $product_option_value_price);
												$product_option_value_points = trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['option'][0] + $i) . ($source[1] + $map['option'][1] + 4))->getValue());
												$product_option_value_points_prefix = stripos($product_option_value_points, '-') === 0 ? '-' : '+';
												$product_option_value_points = (float)str_replace(array('+', '-', ' ', ','), array('', '', '', '.'), $product_option_value_points);
												$product_option_value_weight = trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['option'][0] + $i) . ($source[1] + $map['option'][1] + 5))->getValue());
												$product_option_value_weight_prefix = stripos($product_option_value_weight, '-') === 0 ? '-' : '+';
												$product_option_value_weight = (float)str_replace(array('+', '-', ' ', ','), array('', '', '', '.'), $product_option_value_weight);
												unset($product_option[$j]['option_value']);
												$product_option[$j]['product_option_value'][$k] = array (
													'option_value_id' => $option_value['option_value_id'],
													'product_option_value_id' => '',
													'quantity' => (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['option'][0] + $i) . ($source[1] + $map['option'][1] + 1))->getValue()),
													'subtract' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['option'][0] + $i) . ($source[1] + $map['option'][1] + 2))->getValue() == 'Yes' ? 1 : 0,
													'price_prefix' => $product_option_value_price_prefix,
													'price' => $product_option_value_price,
													'points_prefix' => $product_option_value_points_prefix,
													'points' => $product_option_value_points,
													'weight_prefix' => $product_option_value_weight_prefix,
													'weight' => $product_option_value_weight
												);
											}
										}
									} else {
										$product_option[$j]['option_value'] = $optionValue;
									}
								}
							}
						}
					} while (!empty($optionValue));
					
					$this->load->model('sale/customer_group');
					$customer_groups = $this->model_sale_customer_group->getCustomerGroups();
					
					// Discount
					$i = 0;
					$discountCustomerGroupName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['discount'][0] + $i) . ($source[1] + $map['discount'][1]))->getValue();
					$product_discount = array();
					while(!empty($discountCustomerGroupName)) {
						foreach ($customer_groups as $customer_group) {
							if ($customer_group['name'] == $discountCustomerGroupName) {
								$product_discount[] = array(
									'customer_group_id' => $customer_group['customer_group_id'],
									'quantity' => (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['discount'][0] + $i) . ($source[1] + $map['discount'][1] + 5))->getValue()),
									'priority' => (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['discount'][0] + $i) . ($source[1] + $map['discount'][1] + 1))->getValue()),
									'price' => (float)trim(str_replace(array(' ', ','), array('', '.'),$productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['discount'][0] + $i) . ($source[1] + $map['discount'][1] + 2))->getValue())),
									'date_start' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['discount'][0] + $i) . ($source[1] + $map['discount'][1] + 3))->getValue(),
									'date_end' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['discount'][0] + $i) . ($source[1] + $map['discount'][1] + 4))->getValue()
								);
							}
						}
						$i++;
						$discountCustomerGroupName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['discount'][0] + $i) . ($source[1] + $map['discount'][1]))->getValue();
					}
					
					// Special
					$i = 0;
					$specialCustomerGroupName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['special'][0] + $i) . ($source[1] + $map['special'][1]))->getValue();
					$product_special = array();
					while(!empty($specialCustomerGroupName)) {
						foreach ($customer_groups as $customer_group) {
							if ($customer_group['name'] == $specialCustomerGroupName) {
								$product_special[] = array(
									'customer_group_id' => $customer_group['customer_group_id'],
									'priority' => (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['special'][0] + $i) . ($source[1] + $map['special'][1] + 1))->getValue()),
									'price' => (float)trim(str_replace(array(' ', ','), array('', '.'),$productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['special'][0] + $i) . ($source[1] + $map['special'][1] + 2))->getValue())),
									'date_start' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['special'][0] + $i) . ($source[1] + $map['special'][1] + 3))->getValue(),
									'date_end' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['special'][0] + $i) . ($source[1] + $map['special'][1] + 4))->getValue()
								);
							}
						}
						$i++;
						$specialCustomerGroupName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['special'][0] + $i) . ($source[1] + $map['special'][1]))->getValue();
					}
					
					// Image
					$i = 0;
					$imagePath = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['product_image'][0] + $i) . ($source[1] + $map['product_image'][1]))->getValue();
					$product_image = array();
					while(!empty($imagePath)) {
						$product_image[] = array(
							'image' => trim($imagePath),
							'sort_order' => (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['product_image'][0] + $i) . ($source[1] + $map['product_image'][1] + 1))->getValue())
						);
						$i++;
						$imagePath = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['product_image'][0] + $i) . ($source[1] + $map['product_image'][1]))->getValue();
					}
					
					// Reward Points
					$i = 0;
					$rewardCustomerGroupName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['reward_points'][0] + $i) . ($source[1] + $map['reward_points'][1]))->getValue();
					$product_reward = array();
					while(!empty($rewardCustomerGroupName)) {
						foreach ($customer_groups as $customer_group) {
							if ($customer_group['name'] == $rewardCustomerGroupName) {
								$product_reward[$customer_group['customer_group_id']] = array(
									'points' => (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['reward_points'][0] + $i) . ($source[1] + $map['reward_points'][1] + 1))->getValue())
								);
							}
						}
						$i++;
						$rewardCustomerGroupName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['reward_points'][0] + $i) . ($source[1] + $map['reward_points'][1]))->getValue();
					}
					
					// Layouts (Design)
					$i = 0;
					$this->load->model('setting/store');
					$stores = array_merge(array(0 => array('store_id' => 0, 'name' => 'Default', 'url' => NULL, 'ssl' => NULL)),$this->model_setting_store->getStores());
					$this->load->model('design/layout');
					$layouts = $this->model_design_layout->getLayouts();
					$storeName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['design'][0] + $i) . ($source[1] + $map['design'][1]))->getValue();
					$product_layout = array();
					while(!empty($storeName)) {
						foreach ($stores as $store) {
							if ($store['name'] == $storeName) {
								foreach ($layouts as $layout) {
									$found = false;
									if ($layout['name'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['design'][0] + $i) . ($source[1] + $map['design'][1] + 1))->getValue()) {
											$product_layout[$store['store_id']] = array(
												'layout_id' => $layout['layout_id']
											);
											$found = true;
											break;
									}
									if (!$found) {
										$product_layout[$store['store_id']] = array(
											'layout_id' => ''
										);	
									}
								}
							}
						}
						$i++;
						$storeName = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['design'][0] + $i) . ($source[1] + $map['design'][1]))->getValue();
					}
					
					$product = array(
						'product_description' => array(
							$language => array(
								'name' => $product_name,
								'meta_description' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['meta_description'][0]) . ($source[1] + $map['meta_description'][1]))->getValue(),
								'meta_keyword' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['meta_keyword'][0]) . ($source[1] + $map['meta_keyword'][1]))->getValue(),
								'description' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['description'][0]) . ($source[1] + $map['description'][1]))->getValue(),
								'tag' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['tag'][0]) . ($source[1] + $map['tag'][1]))->getValue(),
							)
						),
						'model' => $product_model,
						'sku' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['sku'][0]) . ($source[1] + $map['sku'][1]))->getValue(),
						'upc' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['upc'][0]) . ($source[1] + $map['upc'][1]))->getValue(),
						'ean' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['ean'][0]) . ($source[1] + $map['ean'][1]))->getValue(),
						'jan' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['jan'][0]) . ($source[1] + $map['jan'][1]))->getValue(),
						'isbn' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['isbn'][0]) . ($source[1] + $map['isbn'][1]))->getValue(),
						'mpn' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['mpn'][0]) . ($source[1] + $map['mpn'][1]))->getValue(),
						'location' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['location'][0]) . ($source[1] + $map['location'][1]))->getValue(),
						'price' => $product_price,
						'tax_class_id' => $product_tax_class_id,
						'quantity' => $product_quantity,
						'minimum' => $product_minimum,
						'subtract' => $product_subtract,
						'stock_status_id' => $product_stock_status_id,
						'shipping' => $product_shipping,
						'keyword' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['keyword'][0]) . ($source[1] + $map['keyword'][1]))->getValue(),
						'image' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['image'][0]) . ($source[1] + $map['image'][1]))->getValue(),
						'date_available' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['date_available'][0]) . ($source[1] + $map['date_available'][1]))->getValue(),
						'length' => $product_length,
						'width' => $product_width,
						'height' => $product_height,
						'length_class_id' => $product_length_class_id,
						'weight' => $product_weight,
						'weight_class_id' => $product_weight_class_id,
						'status' => $product_status,
						'sort_order' => $product_sort_order,
						'manufacturer_id' => $product_manufacturer_id,
						'product_category' => $product_category,
						'product_filter' => $product_filter,
						'product_store' => $product_store,
						'product_download' => $product_download,
						'related' => '',
						'product_related' => $product_related,
						'product_attribute' => $product_attribute,
						'option' => '',
						'product_option' => $product_option,
						'product_discount' => $product_discount,
						'product_special' => $product_special,
						'product_image' => $product_image,
						'points' => (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['points'][0]) . ($source[1] + $map['points'][1]))->getValue()),
						'product_reward' => $product_reward,
						'product_layout' => $product_layout
					);
					
					if (!empty($product_id)) {
						$exists = false;
						$existsQuery = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE product_id = ".$product_id);
						
						$exists = $existsQuery->num_rows > 0;
								
						if ($exists) {
							$this->editProduct($product_id, $product);
						} else {
							$this->addProduct($product_id, $product);
						}
					} else {
						$this->model_catalog_product->addProduct($product);
					}
					
					$progress['current']++;
					$progress['importedCount']++;
					$madeImports = true;
					$this->setProgress($progress);
				}
				$source[1] += 26;
			} while (!empty($product_name));
			$progress['done'] = true;
			if (!$madeImports) {
				$progress['importedCount'] = 0;
				array_shift($this->session->data['uploaded_files']);
			}
			$this->setProgress($progress);
		}
	}
	
	public function exportXLS($type, $language, $store, $destinationFolder = '', $productNumber) {
		if (!is_string($destinationFolder)) throw new Exception($this->language->get('excelport_folder_not_string'));
		if (!is_numeric($productNumber) || $productNumber < 50 || $productNumber > 800) throw new Exception($this->language->get('excelport_product_number_invalid'));
		
		$valid = false;
		switch (VERSION) {
			case '1.5.4' : { $valid = true; } break;
			case '1.5.4.1' : { $valid = true; } break;
			case '1.5.5' : { $valid = true; } break;
			case '1.5.5.1' : { $valid = true; } break;
		}
		if (!$valid) throw new Exception(str_replace('{VERSION}', '1.5.4.x and 1.5.5.x', $this->language->get('text_feature_unsupported')));
		
		$progress = $this->getProgress();
		$progress['done'] = false;
		$file = IMODULE_ROOT . 'vendors/excelport/template.xlsx';
		
		$default_language = $this->config->get('config_language_id');
		$this->config->set('config_language_id', $language);
		require_once(IMODULE_ROOT.'vendors/phpexcel/PHPExcel.php');
		require_once(IMODULE_ROOT.'vendors/phpexcel/CustomReadFilter.php');
		
		if (!empty($progress['populateAll'])) {
			$all = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . $language . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id AND p2s.store_id = '" . $store . "') WHERE p2s.store_id = '" . $store . "' GROUP BY p.product_id");
			$progress['all'] = $all->num_rows;
			unset($progress['populateAll']);
			$this->setProgress($progress);
		}
		
		
		$styleMap = array(
			3 => array(
				'font' => array(
					'name' => 'Calibri',
					'size' => '9',
					'bold' => true
				),
				'alignment' => array(
					'horizontal' => 'center',
					'vertical' => 'center'
				)
			),
			4 => array(
				'borders' => array(
					'right' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THICK
					)
				)
			),
			5 => array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_NONE
				)
			),
			8 => array(
				'borders' => array(
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THICK
					)
				)
			)
		);
		
		$source = array(0,2,15,27);
		$productsSheet = 0;
		$legendSheet = 1;
		
		$taxClassesStart = array(0,30);
		$this->load->model('localisation/tax_class');
		$taxClasses = array_merge(array(0 => array('tax_class_id' => 0, 'title' => '--- None ---', 'description' => '--- None ---', 'date_added' => '0000-00-00 00:00:00', 'date_modified' => '0000-00-00 00:00:00')), $this->model_localisation_tax_class->getTaxClasses());
		
		$stockStatesStart = array(2,30);
		$this->load->model('localisation/stock_status');
		$stockStates = $this->model_localisation_stock_status->getStockStatuses();
		
		$lengthClassesStart = array(3,30);
		$this->load->model('localisation/length_class');
		$lengthClasses = $this->model_localisation_length_class->getLengthClasses();
		
		$weightClassesStart = array(4,30);
		$this->load->model('localisation/weight_class');
		$weightClasses = $this->model_localisation_weight_class->getWeightClasses();
		
		$manufacturersStart = array(6,30);
		$this->load->model('catalog/manufacturer');
		$manufacturers = array_merge(array(0 => array('manufacturer_id' => 0, 'name' => '--- None ---', 'image' => NULL, 'sort_order' => 0)), $this->model_catalog_manufacturer->getManufacturers());
		
		$categoriesStart = array(7,31);
		$this->load->model('catalog/category');
		
		if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
			$categories = $this->model_catalog_category->getCategories(array());
		} 
		
		if (VERSION == '1.5.4' || VERSION == '1.5.4.1') {
			$categories = $this->model_catalog_category->getCategories();
		} 
		
		if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
			$filtersStart = array(18,31);
			$this->load->model('catalog/filter');
			$filters = $this->model_catalog_filter->getFilters(array());
		} 
		
		$storesStart = array(9,31);
		$this->load->model('setting/store');
		$stores = array_merge(array(0 => array('store_id' => 0, 'name' => 'Default', 'url' => NULL, 'ssl' => NULL)),$this->model_setting_store->getStores());
		
		$downloadsStart = array(11,31);
		$this->load->model('catalog/download');
		$downloads = $this->model_catalog_download->getDownloads();
		
		$attributesStart = array(13,30);
		$this->load->model('catalog/attribute');
		$attributes = $this->model_catalog_attribute->getAttributes();
		
		$optionsStart = array(14,30);
		$this->load->model('catalog/option');
		$options = $this->model_catalog_option->getOptions();
		
		$requiredCoordinates = array(15,30,15,31);
		
		$customerGroupsStart = array(16,30);
		$this->load->model('sale/customer_group');
		$customerGroups = $this->model_sale_customer_group->getCustomerGroups();
		
		$layoutsStart = array(17,30);
		$this->load->model('design/layout');
		$layouts = $this->model_design_layout->getLayouts();
		
		$merges = array(0,1,1,1, 0,5,1,5, 0,23,1,23, 2,1,2,2, 2,3,2,8, 2,9,2,14, 2,15,2,19, 2,20,2,21, 2,22,2,23, 2,24,2,25);
		$generals = array(
			'product_id' 		=> array(0,0),
			'name'				=> array(1,0),
			'meta_description'	=> array(1,2),
			'meta_keyword'		=> array(1,3),
			'description'		=> array(15,0),
			'tag'				=> array(1,4)
		);
		$datas = array(
			'model' 		=> array(2,0),
			'sku'			=> array(1,6),
			'upc'			=> array(1,7),
			'ean'			=> array(1,8),
			'jan'			=> array(1,9),
			'isbn'			=> array(1,10),
			'mpn'			=> array(1,11),
			'location'		=> array(1,12),
			'price'			=> array(13,0),
			'tax_class'	 	=> array(1,13),
			'quantity'		=> array(12,0),
			'minimum'		=> array(1,14),
			'subtract'		=> array(1,15),
			'stock_status' 	=> array(1,16),
			'shipping'		=> array(1,17),
			'keyword'		=> array(1,18),
			'image'			=> array(5,0),
			'date_available'=> array(1,19),
			'length'		=> array(6,0),
			'width'			=> array(7,0),
			'height'		=> array(8,0),
			'length_class'	=> array(1,20),
			'weight'		=> array(9,0),
			'weight_class'	=> array(1,21),
			'status'		=> array(4,0),
			'sort_order'	=> array(1,22)
		);
		$links = array(
			'manufacturer'		=> array(10,0),
			'categories'		=> array(3,0),
			'filters'			=> array(1,25),
			'stores'			=> array(11,0),
			'downloads'			=> array(1,24),
			'related'			=> array(14,0)
		);
		$rewards = array(
			'points'			=> array(4,23)
		);
		$dynamicTemplates = array(
			'attributes' => array(4,1,4,2),
			'option_types' => array(4,3,4,8),
			'option_values' => array(5,3,5,8),
			'discounts' => array(4,9,4,14),
			'specials' => array(4,15,4,19),
			'images' => array(4,20,4,21),
			'reward_points' => array(5,22,5,23),
			'designs' => array(4,24,4,25)
		);
		
		$dataValidations = array(
			array(
				'type' => 'list',
				'field' => $datas['tax_class'],
				'data' => array($taxClassesStart[0], $taxClassesStart[1], $taxClassesStart[0], $taxClassesStart[1] + count($taxClasses) - 1),
				'range' => '',
				'count' => count($taxClasses)
			),
			array(
				'type' => 'list',
				'field' => $datas['subtract'],
				'data' => array(1,30,1,31),
				'range' => ''
			),
			array(
				'type' => 'list',
				'field' => $datas['stock_status'],
				'data' => array($stockStatesStart[0], $stockStatesStart[1], $stockStatesStart[0], $stockStatesStart[1] + count($stockStates) - 1),
				'range' => '',
				'count' => count($stockStates)
			),
			array(
				'type' => 'list',
				'field' => $datas['shipping'],
				'data' => array(1,30,1,31),
				'range' => ''
			),
			array(
				'type' => 'list',
				'field' => $datas['length_class'],
				'data' => array($lengthClassesStart[0], $lengthClassesStart[1], $lengthClassesStart[0], $lengthClassesStart[1] + count($lengthClasses) - 1),
				'range' => '',
				'count' => count($lengthClasses)
			),
			array(
				'type' => 'list',
				'field' => $datas['weight_class'],
				'data' => array($weightClassesStart[0], $weightClassesStart[1], $weightClassesStart[0], $weightClassesStart[1] + count($weightClasses) - 1),
				'range' => '',
				'count' => count($weightClasses)
			),
			array(
				'type' => 'list',
				'field' => $datas['status'],
				'data' => array(5,30,5,31),
				'range' => ''
			),
			array(
				'type' => 'list',
				'field' => $links['manufacturer'],
				'data' => array($manufacturersStart[0], $manufacturersStart[1], $manufacturersStart[0], $manufacturersStart[1] + count($manufacturers) - 1),
				'range' => '',
				'count' => count($manufacturers)
			)
		);
		
		$leftColumnStaticText = array(
			array(null),
			array('General', null, 'Attribute', 'Attribute Name'),
			array('Meta Tag Description', null, null, 'Attribute Text'),
			array('Meta Tag Keywords', null, 'Option', 'Option Value'),
			array('Product Tags', null, null, 'Quantity'),
			array('Data', null, null, 'Subtract Stock'),
			array('SKU', null, null, 'Price'),
			array('UPC', null, null, 'Points'),
			array('EAN', null, null, 'Weight'),
			array('JAN', null, 'Discount', 'Customer Group'),
			array('ISBN', null, null, 'Priority'),
			array('MPN', null, null, 'Price'),
			array('Location', null, null, 'Date Start'),
			array('Tax Class', null, null, 'Date End'),
			array('Minimum Quantity', null, null, 'Quantity'),
			array('Subtract Stock', null, 'Special', 'Customer Group'),
			array('Out Of Stock Status', null, null, 'Priority'),
			array('Requires Shipping', null, null, 'Price'),
			array('SEO Keywords', null, null, 'Date Start'),
			array('Date Available', null, null, 'Date End'),
			array('Length Class', null, 'Image', 'Image Path'),
			array('Weight Class', null, null, 'Sort Order'),
			array('Sort Order', null, 'Reward Points', 'Customer Group'),
			array('Links', null, null, 'Reward Points'),
			array('Downloads', null, 'Design', 'Store'),
			array('Filters', null, null, 'Layout Override')
		);
		
		$widths = array(18, 13, 12, 16, 20, 19, 14, 14, 14, 14, 15, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12);
		
		$target = array(0,2);
		$name = 'excelport_export_'.date("Y-m-d_H-i-s");
		$resultName = $name . '.xlsx';
		$result = $destinationFolder . '/' . $name . '.xlsx';

		$objPHPExcel = PHPExcel_IOFactory::load($file);
		
		// Set document properties
		$objPHPExcel->getProperties()
					->setCreator($this->user->getUserName())
					->setLastModifiedBy($this->user->getUserName())
					->setTitle($name)
					->setSubject($name)
					->setDescription("Backup for Office 2007 and later, generated using PHPExcel and ExcelPort.")
					->setKeywords("office 2007 2010 2013 xlsx openxml php phpexcel excelport")
					->setCategory("Backup");
		
		$legendSheetObj = $objPHPExcel->setActiveSheetIndex($legendSheet);
		
		for ($i = 0; $i < count($taxClasses); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($taxClassesStart[0]) . ($taxClassesStart[1] + $i), $taxClasses[$i]['title']);
		}
		for ($i = 0; $i < count($stockStates); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($stockStatesStart[0]) . ($stockStatesStart[1] + $i), $stockStates[$i]['name']);
		}
		for ($i = 0; $i < count($lengthClasses); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lengthClassesStart[0]) . ($lengthClassesStart[1] + $i), $lengthClasses[$i]['title']);
		}
		for ($i = 0; $i < count($weightClasses); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($weightClassesStart[0]) . ($weightClassesStart[1] + $i), $weightClasses[$i]['title']);
		}
		for ($i = 0; $i < count($manufacturers); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($manufacturersStart[0]) . ($manufacturersStart[1] + $i), $manufacturers[$i]['name']);
		}
		for ($i = 0; $i < count($categories); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($categoriesStart[0]) . ($categoriesStart[1] + $i), $categories[$i]['category_id']);
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($categoriesStart[0] + 1) . ($categoriesStart[1] + $i), htmlspecialchars_decode($categories[$i]['name']));
		}
		if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
			for ($i = 0; $i < count($filters); $i++) {
				$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($filtersStart[0]) . ($filtersStart[1] + $i), $filters[$i]['filter_id']);
				$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($filtersStart[0] + 1) . ($filtersStart[1] + $i), htmlspecialchars_decode($filters[$i]['name']));
			}
		} 
		for ($i = 0; $i < count($stores); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($storesStart[0]) . ($storesStart[1] + $i), $stores[$i]['store_id']);
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($storesStart[0] + 1) . ($storesStart[1] + $i), $stores[$i]['name']);
		}
		for ($i = 0; $i < count($downloads); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($downloadsStart[0]) . ($downloadsStart[1] + $i), $downloads[$i]['download_id']);
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($downloadsStart[0] + 1) . ($downloadsStart[1] + $i), $downloads[$i]['name']);
		}
		for ($i = 0; $i < count($attributes); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($attributesStart[0]) . ($attributesStart[1] + $i), $attributes[$i]['name']);
		}
		for ($i = 0; $i < count($options); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($optionsStart[0]) . ($optionsStart[1] + $i), htmlspecialchars_decode($options[$i]['name']));
		}
		for ($i = 0; $i < count($customerGroups); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($customerGroupsStart[0]) . ($customerGroupsStart[1] + $i), $customerGroups[$i]['name']);
		}
		for ($i = 0; $i < count($layouts); $i++) {
			$legendSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($layoutsStart[0]) . ($layoutsStart[1] + $i), $layouts[$i]['name']);
		}
			
		$this->load->model('catalog/product');
	
		$products = $this->db->query("SELECT *, 
			(SELECT IFNULL((SELECT keyword FROM " . DB_PREFIX . "url_alias ua WHERE ua.query=(SELECT CONCAT('product_id=', p.product_id))), '')) as keyword, 
			(SELECT IFNULL((SELECT GROUP_CONCAT(DISTINCT p2c.category_id ORDER BY p2c.category_id ASC SEPARATOR ',') FROM " . DB_PREFIX . "product_to_category p2c WHERE p2c.product_id = p.product_id GROUP BY p2c.product_id),'')) as categories,
			".((VERSION == '1.5.5' || VERSION == '1.5.5.1') ? "(SELECT IFNULL((SELECT GROUP_CONCAT(DISTINCT pf.filter_id ORDER BY pf.filter_id ASC SEPARATOR ',') FROM " . DB_PREFIX . "product_filter pf WHERE pf.product_id = p.product_id GROUP BY pf.product_id),'')) as filters," : "")."
			(SELECT IFNULL((SELECT GROUP_CONCAT(DISTINCT p2s.store_id ORDER BY p2s.store_id ASC SEPARATOR ',') FROM " . DB_PREFIX . "product_to_store p2s WHERE p2s.product_id = p.product_id GROUP BY p2s.product_id),'')) as stores,
			(SELECT IFNULL((SELECT GROUP_CONCAT(DISTINCT p2d.download_id ORDER BY p2d.download_id ASC SEPARATOR ',') FROM " . DB_PREFIX . "product_to_download p2d WHERE p2d.product_id = p.product_id GROUP BY p2d.product_id),'')) as downloads,
			(SELECT IFNULL((SELECT GROUP_CONCAT(DISTINCT pr.related_id ORDER BY pr.related_id ASC SEPARATOR ',') FROM " . DB_PREFIX . "product_related pr WHERE pr.product_id = p.product_id GROUP BY pr.product_id),'')) as related
			
			FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . $language . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id AND p2s.store_id = '" . $store . "') WHERE p2s.store_id = '" . $store . "' GROUP BY p.product_id ORDER BY p.product_id 
			LIMIT ". $progress['current'] . ", " . $productNumber);
		
		$productSheetObj = $objPHPExcel->setActiveSheetIndex($productsSheet);
		
		$dataValidationRanges = array(
			'attribute_name' => array('range' => ''),
			'option_required' => array('range' => ''),
			'option_type' => array('range' => ''),
			'option_subtract_strock' => array('range' => ''),
			'customer_group' => array('range' => ''),
			'design_layout' => array('range' => '')
		);
		
		$cellToStyleRange = '';
		
		$productSheetObj->getDefaultStyle()->applyFromArray($styleMap[3]);
		$productSheetObj->getStyle('D')->applyFromArray($styleMap[4], false);

		if ($products->num_rows > 0) {
			foreach ($products->rows as $myProductIndex => $row) {
				
				$productSheetObj->fromArray($leftColumnStaticText, null, 'A'.(($myProductIndex*26)+2));
				$productSheetObj->getStyle('D'.(($myProductIndex*26)+2))->applyFromArray($styleMap[5]);
				$productSheetObj->getStyle('A'.(($myProductIndex*26)+3).':'.'B'.(($myProductIndex*26)+3))->applyFromArray($styleMap[8]);
				$productSheetObj->getStyle('A'.(($myProductIndex*26)+7).':'.'B'.(($myProductIndex*26)+7))->applyFromArray($styleMap[8]);
				$productSheetObj->getStyle('A'.(($myProductIndex*26)+25).':'.'B'.(($myProductIndex*26)+25))->applyFromArray($styleMap[8]);
				
				// Prepare data
				foreach ($taxClasses as $taxClass) {
					if ($taxClass['tax_class_id'] == $row['tax_class_id']) { $row['tax_class'] = $taxClass['title']; break; }
				}
				if (empty($row['tax_class'])) $row['tax_class'] = $taxClasses[0]['title'];
				$row['subtract'] = empty($row['subtract']) ? 'No' : 'Yes';
				foreach ($stockStates as $stockStatus) {
					if ($stockStatus['stock_status_id'] == $row['stock_status_id']) { $row['stock_status'] = $stockStatus['name']; }
					if ($stockStatus['stock_status_id'] == $this->config->get('config_stock_status_id')) { $defaultStockStatus = $stockStatus['name']; }	
				}
				if (empty($row['stock_status'])) $row['stock_status'] = $defaultStockStatus;
				$row['shipping'] = empty($row['shipping']) ? 'No' : 'Yes';
				$row['length_class'] = $lengthClasses[0]['title'];
				foreach ($lengthClasses as $lengthClass) {
					if ($lengthClass['length_class_id'] == $row['length_class_id']) { $row['length_class'] = $lengthClass['title']; break; }
				}
				$row['weight_class'] = $weightClasses[0]['title'];
				foreach ($weightClasses as $weightClass) {
					if ($weightClass['weight_class_id'] == $row['weight_class_id']) { $row['weight_class'] = $weightClass['title']; break; }
				}
				$row['sort_order'] = empty($row['sort_order']) ? '0' : $row['sort_order'];
				$row['status'] = empty($row['status']) ? 'Disabled' : 'Enabled';
				foreach ($manufacturers as $manufacturer) {
					if ($manufacturer['manufacturer_id'] == $row['manufacturer_id']) { $row['manufacturer'] = $manufacturer['name']; break; }
				}
				if (empty($row['manufacturer'])) $row['manufacturer'] = $manufacturers[0]['name'];
				if (empty($row['filters'])) $row['filters'] = '';
				
				for ($i = 0; $i < count($merges); $i += 4) {
					$productSheetObj->mergeCells(PHPExcel_Cell::stringFromColumnIndex($target[0] + $merges[$i]) . ($target[1] + $merges[$i+1]) . ':' . PHPExcel_Cell::stringFromColumnIndex($target[0] + $merges[$i+2]) . ($target[1] + $merges[$i+3]));	
				}
				
				// Add data
				// General
				foreach ($generals as $name => $position) {
					$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $position[0]) . ($target[1] + $position[1]), empty($row[$name]) ? '' : $row[$name]);
				}
				// Data
				foreach ($datas as $name => $position) {
					$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $position[0]) . ($target[1] + $position[1]), empty($row[$name]) && $row[$name] !== '0' ? '' : $row[$name]);
				}
				// Links
				foreach ($links as $name => $position) {
					$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $position[0]) . ($target[1] + $position[1]), empty($row[$name]) && $row[$name] !== '0' ? '' : $row[$name]);
				}
				// Attributes
				$productAttributes = $this->model_catalog_product->getProductAttributes($row['product_id']);
				$i3 = $dynamicTemplates['attributes'][0];
				
				$attributesRange = array(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['attributes'][1]), '');
				
				if (empty($dataValidationRanges['attribute_name']['root'])) {
					$dataValidationRanges['attribute_name']['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['attributes'][1]);
				}
				
				foreach ($productAttributes as $productAttributeIndex => $productAttribute) {
					if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
						$myProductAttribute = $this->model_catalog_attribute->getAttribute($productAttribute['attribute_id']);
						$productAttributes[$productAttributeIndex]['name'] = $myProductAttribute['name'];
						$productAttribute['name'] = $myProductAttribute['name'];
					}
					
					for ($j = 0; $j <= $dynamicTemplates['attributes'][3] - $dynamicTemplates['attributes'][1]; $j++) {
						if ($j == 0) {
							$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['attributes'][1] + $j), $productAttribute['name']);
							$attributesRange[1] =  PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['attributes'][1] + $j);
						} else {
							$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['attributes'][1] + $j), $productAttribute['product_attribute_description'][$language]['text']);	
						}
					}
					$i3++;
				}
				
				if (!empty($attributesRange[1])) {
					if ($attributesRange[1] != $attributesRange[0]) {
						$dataValidationRanges['attribute_name']['range'] .= ' '.$attributesRange[0].':'.$attributesRange[1];
					} else {
						$dataValidationRanges['attribute_name']['range'] .= ' '.$attributesRange[0];
					}
				}
				
				// Options
				$productOptions = $this->model_catalog_product->getProductOptions($row['product_id']);
				$i3 = $dynamicTemplates['option_types'][0];
				$optionsRange = array(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1]), '');
				if (empty($dataValidationRanges['option_required']['root'])) {
					$dataValidationRanges['option_required']['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1]);
					$dataValidationRanges['option_type']['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1] + 1);
					$dataValidationRanges['option_subtract_strock']['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3+1) . ($target[1] + $dynamicTemplates['option_values'][1] + 2);
				}
				$optionsRequired = array();
				$optionsType = array();
				$optionsSubtract = array();
				
				foreach ($productOptions as $productOptionIndex => $productOption) {
					if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
						$myProductOption = $this->model_catalog_option->getOption($productOption['option_id']);
						$productOptions[$productOptionIndex]['name'] = $myProductOption['name'];
						$productOption['name'] = $myProductOption['name'];
					}
					
					// Create the main column
					for ($j = 0; $j <= 1; $j++) {
						if ($j == 0) {
							$optionsRequired[] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1] + $j);
							
							$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1] + $j), empty($productOption['required']) ? 'Required: No' : 'Required: Yes');
						} else {
							$optionsType[] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1] + $j);
							$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1] + $j), htmlspecialchars_decode($productOption['name']));
							$productSheetObj->mergeCells(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1] + $j) . ':' . PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][3]));
						}
					}
					$i3++;
					// Populate option values
					$optionDataFields = array();
					if (!empty($productOption['product_option_value']) && is_array($productOption['product_option_value'])) {
						foreach ($productOption['product_option_value'] as $product_option_value) {
							
							if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
								$productOptionValue = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
								$product_option_value['name'] = $productOptionValue['name'];
							}
							
							$optionDataFields[] = array(
								0 => $product_option_value['name'],
								1 => $product_option_value['quantity'],
								2 => !empty($product_option_value['subtract']) ? 'Yes' : 'No',
								3 => $product_option_value['price_prefix'] . $product_option_value['price'],
								4 => $product_option_value['points_prefix'] . $product_option_value['points'],
								5 => $product_option_value['weight_prefix'] . $product_option_value['weight']
							);
						}
					} else if (!empty($productOption['option_value'])) {
						$optionDataFields[] = array(
							0 => $productOption['option_value'],
							1 => '',
							2 => '',
							3 => '',
							4 => '',
							5 => ''
						);
					}
					
					$optionsRange[1] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_types'][1]);
					
					if (!empty($optionDataFields)) {
						foreach ($optionDataFields as $optionDataField) {
							for ($j = 0; $j < count($optionDataField); $j++) {
								if ($j == 2) {
									$optionsSubtract[] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_values'][1] + $j);
									
									$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_values'][1] + $j), $optionDataField[$j]);
								} else {
									$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['option_values'][1] + $j), $optionDataField[$j]);	
								}
							}
							$i3++;
						}
					}
				}
				
				if (!empty($optionsRequired)) {
					$dataValidationRanges['option_required']['range'] .= ' '.implode(' ',$optionsRequired);
				}
				if (!empty($optionsType)) {
					$dataValidationRanges['option_type']['range'] .= ' '.implode(' ',$optionsType);
				}
				if (!empty($optionsSubtract)) {
					$dataValidationRanges['option_subtract_strock']['range'] .= ' '.implode(' ',$optionsSubtract);
				}
				
				// Discount
				$productDiscounts = $this->model_catalog_product->getProductDiscounts($row['product_id']);
				$i3 = $dynamicTemplates['discounts'][0];
				
				$customerGroupRange = array(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['discounts'][1]), '');
				
				if (empty($dataValidationRanges['customer_group']['root'])) {
					$dataValidationRanges['customer_group']['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['discounts'][1]);
				}
				
				$discountJMap = array(
					0 => 'customer_group',
					1 => 'priority',
					2 => 'price',
					3 => 'date_start',
					4 => 'date_end',
					5 => 'quantity'
				);
				foreach ($productDiscounts as $productDiscount) {
					foreach ($customerGroups as $customerGroup) {
						if ($customerGroup['customer_group_id'] == $productDiscount['customer_group_id']) { $productDiscount['customer_group'] = $customerGroup['name']; break; }
					}
					for ($j = 0; $j <= $dynamicTemplates['discounts'][3] - $dynamicTemplates['discounts'][1]; $j++) {
						$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['discounts'][1] + $j), $productDiscount[$discountJMap[$j]]);
						
						if ($j == 0) {
							$customerGroupRange[1] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['discounts'][1] + $j);
						}
						
					}
					$i3++;
				}
				
				if (!empty($customerGroupRange[1])) {
					if ($customerGroupRange[1] != $customerGroupRange[0]) {
						$dataValidationRanges['customer_group']['range'] .= ' '.$customerGroupRange[0].':'.$customerGroupRange[1];
					} else {
						$dataValidationRanges['customer_group']['range'] .= ' '.$customerGroupRange[0];
					}
				}
				
				// Special
				$productSpecials = $this->model_catalog_product->getProductSpecials($row['product_id']);
				$i3 = $dynamicTemplates['specials'][0];
				
				$customerGroupRange = array(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['specials'][1]), '');
				
				if (empty($dataValidationRanges['customer_group']['root'])) {
					$dataValidationRanges['customer_group']['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['specials'][1]);
				}
				
				$specialJMap = array(
					0 => 'customer_group',
					1 => 'priority',
					2 => 'price',
					3 => 'date_start',
					4 => 'date_end'
				);
				foreach ($productSpecials as $productSpecial) {
					foreach ($customerGroups as $customerGroup) {
						if ($customerGroup['customer_group_id'] == $productSpecial['customer_group_id']) { $productSpecial['customer_group'] = $customerGroup['name']; break; }
					}
					for ($j = 0; $j <= $dynamicTemplates['specials'][3] - $dynamicTemplates['specials'][1]; $j++) {
						$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['specials'][1] + $j), $productSpecial[$specialJMap[$j]]);
						
						if ($j == 0) {
							$customerGroupRange[1] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['specials'][1] + $j);
						}
					}
					$i3++;	
				}
				
				if (!empty($customerGroupRange[1])) {
					if ($customerGroupRange[1] != $customerGroupRange[0]) {
						$dataValidationRanges['customer_group']['range'] .= ' '.$customerGroupRange[0].':'.$customerGroupRange[1];
					} else {
						$dataValidationRanges['customer_group']['range'] .= ' '.$customerGroupRange[0];
					}
				}
				
				// Image
				$productImages = $this->model_catalog_product->getProductImages($row['product_id']);
				$i3 = $dynamicTemplates['images'][0];
				
				$imagePathRange = array(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['images'][1]), '');
				
				$imageJMap = array(
					0 => 'image',
					1 => 'sort_order'
				);
				foreach ($productImages as $productImage) {
					$imagePathRange[1] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['images'][1]);
					for ($j = 0; $j <= $dynamicTemplates['images'][3] - $dynamicTemplates['images'][1]; $j++) {
						$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['images'][1] + $j), $productImage[$imageJMap[$j]]);
					}
					$i3++;
				}
				
				// Reward Points
				foreach ($rewards as $name => $position) {
					$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $position[0]) . ($target[1] + $position[1]), empty($row[$name]) && $row[$name] !== '0' ? '' : $row[$name]);
				}
				$productRewards = $this->model_catalog_product->getProductRewards($row['product_id']);
				$i3 = $dynamicTemplates['reward_points'][0];
				
				$rewardJMap = array(
					0 => 'customer_group',
					1 => 'points'
				);
				foreach ($productRewards as $customer_group_id => $productReward) {
					foreach ($customerGroups as $customerGroup) {
						if ($customerGroup['customer_group_id'] == $customer_group_id) { $productReward['customer_group'] = $customerGroup['name']; break; }
					}
					for ($j = 0; $j <= $dynamicTemplates['reward_points'][3] - $dynamicTemplates['reward_points'][1]; $j++) {
						$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['reward_points'][1] + $j), $productReward[$rewardJMap[$j]]);
						if ($j == 0) {
							if (($i3 - $dynamicTemplates['reward_points'][0]) == 1) {
								$customerGroupRange = array(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['reward_points'][1] + $j), '');
				
								if (empty($dataValidationRanges['customer_group']['root'])) {
									$dataValidationRanges['customer_group']['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['reward_points'][1] + $j);
								}
							}
							
							if (($i3 - $dynamicTemplates['reward_points'][0]) > 0) {
								$customerGroupRange[1] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['reward_points'][1] + $j);
							}
						}
					}
					$i3++;
				}
				
				if (!empty($customerGroupRange[1])) {
					if ($customerGroupRange[1] != $customerGroupRange[0]) {
						$dataValidationRanges['customer_group']['range'] .= ' '.$customerGroupRange[0].':'.$customerGroupRange[1];
					} else {
						$dataValidationRanges['customer_group']['range'] .= ' '.$customerGroupRange[0];
					}
				}
				
				// Design
				$productLayouts = $this->model_catalog_product->getProductLayouts($row['product_id']);
				$i3 = $dynamicTemplates['designs'][0];
				
				$designStoreRange = array(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['designs'][1]), '');
				$designLayoutRange = array();
				
				foreach ($stores as $store) {
					$designStoreRange[1] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['designs'][1]);
					
					for ($j = 0; $j <= $dynamicTemplates['designs'][3] - $dynamicTemplates['designs'][1]; $j++) {
						if ($j == 0) {
							$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['designs'][1] + $j), $store['name']);
						} else if ($j == 1) {
							
							if (empty($designLayoutRange)) {
								$designLayoutRange = array(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['designs'][1] + $j), '');
							}
				
							if (empty($dataValidationRanges['design_layout']['root'])) {
								$dataValidationRanges['design_layout']['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['designs'][1]);
							}
							
							if (!empty($productLayouts[$store['store_id']])) {
								$layout_id = $productLayouts[$store['store_id']];
								$productLayout = '';
								foreach ($layouts as $layout) {
									if ($layout['layout_id'] == $layout_id) { $productLayout = $layout['name']; break; }
								}
								$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['designs'][1] + $j), $productLayout);
							} else {
								$productSheetObj->setCellValue(PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['designs'][1] + $j), '');
							}
							$designLayoutRange[1] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $i3) . ($target[1] + $dynamicTemplates['designs'][1] + $j);
						}
					}
					$i3++;	
				}
				
				if (!empty($designLayoutRange[1])) {
					if ($designLayoutRange[1] != $designLayoutRange[0]) {
						$dataValidationRanges['design_layout']['range'] .= ' '.implode(':', $designLayoutRange);
					} else {
						$dataValidationRanges['design_layout']['range'] .= ' '.$designLayoutRange[0];
					}
				}
				
				// Data validations
				
				foreach ($dataValidations as $dataValidationIndex => $dataValidation) {
					if (isset($dataValidations[$dataValidationIndex]['count']) && $dataValidations[$dataValidationIndex]['count'] == 0) continue;
					$dataValidations[$dataValidationIndex]['range'] .= ' '.PHPExcel_Cell::stringFromColumnIndex($target[0] + $dataValidation['field'][0]) . ($target[1] + $dataValidation['field'][1]);
					if (empty($dataValidations[$dataValidationIndex]['root'])) $dataValidations[$dataValidationIndex]['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $dataValidation['field'][0]) . ($target[1] + $dataValidation['field'][1]);
				}
				
				//Collapse
				for ($i = $target[1] + 1; $i <= $target[1] + $source[3] - $source[1]; $i++) { 
					$productSheetObj->getRowDimension($i)->setOutlineLevel(1);
					$productSheetObj->getRowDimension($i)->setVisible(false);
				}
				$productSheetObj->getRowDimension($i)->setCollapsed(true);
				$target[1] = $target[1] + ($source[3] - $source[1] + 1);
				$progress['current']++;
				$progress['memory_get_usage'] = round(memory_get_usage(true)/(1024*1024));
				$progress['percent'] = 100 / ($products->num_rows / $progress['current']);
				$this->setProgress($progress);
			}
			
			for ($i = $target[0]; $i <= $target[0] + $source[2] - $source[0]; $i++) {
				$productSheetObj->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth($widths[$i]);
			}
			
			//Apply data validation for:
			// Generals
			foreach ($dataValidations as $dataValidation) {
				if (isset($dataValidation['count']) && $dataValidation['count'] == 0) continue;
				if ($dataValidation['type'] == 'list' && !empty($dataValidation['root'])) {
					$objValidation = $productSheetObj->getCell($dataValidation['root'])->getDataValidation();
					$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
					$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle('Input error');
					$objValidation->setError('Value is not in list.');
					$objValidation->setPromptTitle('Pick from list');
					$objValidation->setPrompt('Please pick a value from the drop-down list.');
					$objValidation->setFormula1($legendSheetObj->getTitle() . '!$' . PHPExcel_Cell::stringFromColumnIndex($dataValidation['data'][0]) . '$' . ($dataValidation['data'][1]) . ':$' . PHPExcel_Cell::stringFromColumnIndex($dataValidation['data'][2]) . '$' . ($dataValidation['data'][3]));
					$productSheetObj->setDataValidation(trim($dataValidation['range']), $objValidation);
				}
			}
			
			//Attributes
			if (count($attributes) > 0) {
				$objValidation = $productSheetObj->getCell($dataValidationRanges['attribute_name']['root'])->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				$objValidation->setPromptTitle('Pick from list');
				$objValidation->setPrompt('Please pick a value from the drop-down list.');
				$objValidation->setFormula1($legendSheetObj->getTitle() . '!$' . PHPExcel_Cell::stringFromColumnIndex($attributesStart[0]) . '$' . ($attributesStart[1]) . ':$' . PHPExcel_Cell::stringFromColumnIndex($attributesStart[0]) . '$' . ($attributesStart[1] + count($attributes) - 1));
				$productSheetObj->setDataValidation(trim($dataValidationRanges['attribute_name']['range']), $objValidation);
			}
			
			//Options
			//required
			if (count($options) > 0) {
				$objValidation = $productSheetObj->getCell($dataValidationRanges['option_required']['root'])->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				$objValidation->setPromptTitle('Pick from list');
				$objValidation->setPrompt('Please pick a value from the drop-down list.');
				$objValidation->setFormula1($legendSheetObj->getTitle() . '!$' . PHPExcel_Cell::stringFromColumnIndex($requiredCoordinates[0]) . '$' . ($requiredCoordinates[1]) . ':$' . PHPExcel_Cell::stringFromColumnIndex($requiredCoordinates[2]) . '$' . ($requiredCoordinates[3]));
				$productSheetObj->setDataValidation(trim($dataValidationRanges['option_required']['range']), $objValidation);
				
				//type
				$objValidation = $productSheetObj->getCell($dataValidationRanges['option_type']['root'])->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				$objValidation->setPromptTitle('Pick from list');
				$objValidation->setPrompt('Please pick a value from the drop-down list.');
				$objValidation->setFormula1($legendSheetObj->getTitle() . '!$' . PHPExcel_Cell::stringFromColumnIndex($optionsStart[0]) . '$' . ($optionsStart[1]) . ':$' . PHPExcel_Cell::stringFromColumnIndex($optionsStart[0]) . '$' . ($optionsStart[1] + count($options) - 1));
				$productSheetObj->setDataValidation(trim($dataValidationRanges['option_type']['range']), $objValidation);
				
				//sutract stock
				$objValidation = $productSheetObj->getCell($dataValidationRanges['option_subtract_strock']['root'])->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(true);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				$objValidation->setPromptTitle('Pick from list');
				$objValidation->setPrompt('Please pick a value from the drop-down list.');
				$objValidation->setFormula1($legendSheetObj->getTitle() . '!$' . PHPExcel_Cell::stringFromColumnIndex(1) . '$' . (30) . ':$' . PHPExcel_Cell::stringFromColumnIndex(1) . '$' . (31));
				$productSheetObj->setDataValidation(trim($dataValidationRanges['option_subtract_strock']['range']), $objValidation);
			}
			
			//Customer groups
			$objValidation = $productSheetObj->getCell($dataValidationRanges['customer_group']['root'])->getDataValidation();
			$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
			$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle('Input error');
			$objValidation->setError('Value is not in list.');
			$objValidation->setPromptTitle('Pick from list');
			$objValidation->setPrompt('Please pick a value from the drop-down list.');
			$objValidation->setFormula1($legendSheetObj->getTitle() . '!$' . PHPExcel_Cell::stringFromColumnIndex($customerGroupsStart[0]) . '$' . ($customerGroupsStart[1]) . ':$' . PHPExcel_Cell::stringFromColumnIndex($customerGroupsStart[0]) . '$' . ($customerGroupsStart[1] + count($customerGroups) - 1));
			$productSheetObj->setDataValidation(trim($dataValidationRanges['customer_group']['range']), $objValidation);
			
			//Design -> layout
			if (count($layouts)) {
				$objValidation = $productSheetObj->getCell($dataValidationRanges['design_layout']['root'])->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				$objValidation->setPromptTitle('Pick from list');
				$objValidation->setPrompt('Please pick a value from the drop-down list.');
				$objValidation->setFormula1($legendSheetObj->getTitle() . '!$' . PHPExcel_Cell::stringFromColumnIndex($layoutsStart[0]) . '$' . ($layoutsStart[1]) . ':$' . PHPExcel_Cell::stringFromColumnIndex($layoutsStart[0]) . '$' . ($layoutsStart[1] + count($layouts) - 1));
				$productSheetObj->setDataValidation(trim($dataValidationRanges['design_layout']['range']), $objValidation);
			}
			
			unset($objValidation);
		} else {
			$progress['done'] = true;
		}
		
		$this->config->set('config_language_id', $default_language);
		
		$this->session->data['generated_file'] = $result;
		$this->session->data['generated_files'][] = $resultName;
		$this->setProgress($progress);
		
		try {
			set_time_limit(60);
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->setPreCalculateFormulas(false);
			
			$objWriter->save($result);
			
			$progress['done'] = true;
		} catch (Exception $e) {
			$progress['message'] = $e->getMessage();
			$progress['error'] = true;
			$progress['done'] = false;
			$this->setProgress($progress);
		}
		$objPHPExcel->disconnectWorksheets();
		unset($legendSheetObj);
		unset($objWriter);
		unset($productSheetObj);
		unset($objPHPExcel);
		
		
		$progress['done'] = true;
		$this->setProgress($progress);
		
		return true;
	}
	
	public function getStandardFile($file, $arrayName, $indexName) {
		$allowedExts = array("zip", "xlsx");
		$name = $file['name'][$arrayName][$indexName];
		$explode = explode(".", $name);
		$extension = end($explode);
		$result = false;
		if ($file['size'][$arrayName][$indexName] <= $this->returnMaxUploadSize() && in_array($extension, $allowedExts)) { //file limit = post_max_size - 512KB
			if ($file['error'][$arrayName][$indexName] > 0) throw new Exception("Upload Error Code: " . $file['error'][$arrayName][$indexName]);
			$dest = IMODULE_ROOT.'temp/'.$name;
			if (!move_uploaded_file($file['tmp_name'][$arrayName][$indexName], $dest)) throw new Exception($this->language->get('excelport_unable_upload'));
			else $result = $dest;
		} else throw new Exception($this->language->get('excelport_invalid_file'));
		
		return $dest;
	}
	
	public function returnMaxUploadSize($readable = false) {
		$upload = $this->return_bytes(ini_get('upload_max_filesize'));
		$post = $this->return_bytes(ini_get('post_max_size'));
		
		if ($upload >= $post) return $readable ? $this->sizeToString($post - 524288) : $post - 524288;
		else return $readable ? $this->sizeToString($upload) : $upload;
	}
	private function return_bytes($val) { //from http://php.net/manual/en/function.ini-get.php
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
	
		return $val;
	}
	private function sizeToString($size) {
		$count = 0;
		for ($i = $size; $i >= 1024; $i /= 1024) $count++;
		switch ($count) {
			case 0 : $suffix = ' B'; break;
			case 1 : $suffix = ' KB'; break;
			case 2 : $suffix = ' MB'; break;
			case 3 : $suffix = ' GB'; break;
			case ($count >= 4) : $suffix = ' TB'; break;
		}
		return round($i, 2).$suffix;
	}
	
	public function addProduct($product_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET product_id = '" . (int)trim($product_id) . "', model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");
		
		$product_id = $this->db->getLastId();
		
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");
		}
		
		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "'");
		}
		
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
					
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {				
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}
	
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
				
					$product_option_id = $this->db->getLastId();
				
					if (isset($product_option['product_option_value'])) {
                        foreach ($product_option['product_option_value'] as $product_option_value) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
                        }
                    }
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}
		
		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}
		
		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($product_image['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}
		
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}
		
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}
		
		if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
			if (isset($data['product_filter'])) {
				foreach ($data['product_filter'] as $filter_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
				}
			}
		}
		
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
						
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
						
		$this->cache->delete('product');
	}
	
	public function editProduct($product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");
		}
		
		$this->load->model('localisation/language');
		$languages = array_values($this->model_localisation_language->getLanguages());
		$language_ids = array();
		foreach ($languages as $language) {
			$language_ids[] = $language['language_id'];	
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id NOT IN (" . implode(',', $language_ids) . ")");
		
		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "' ON DUPLICATE KEY UPDATE name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
	
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND language_id NOT IN (" . implode(',', $language_ids) . ")");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id NOT IN (" . implode(',', $language_ids) . ")");
					
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {				
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "' ON DUPLICATE KEY UPDATE text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
				
					$product_option_id = $this->db->getLastId();
				
					if (isset($product_option['product_option_value'])) {
                        foreach ($product_option['product_option_value'] as $product_option_value) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
                        }
                    }
				} else { 
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
				}					
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
 
		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($product_image['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}		
		}
		
		if (VERSION == '1.5.5' || VERSION == '1.5.5.1') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
        
			if (isset($data['product_filter'])) {
				foreach ($data['product_filter'] as $filter_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
				}       
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
						
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
		
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
						
		$this->cache->delete('product');
	}
	
	public function createDownload($file, $die = true) {
		$attachment_location = $file;
		if (file_exists($attachment_location)) {
			$attachment_info = pathinfo($attachment_location);
			header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
			header("Cache-Control: public"); // needed for i.e.
			header("Content-Type: application/zip");
			header("Content-Transfer-Encoding: Binary");
			header("Content-Length:".filesize($attachment_location));
			header("Content-Disposition: attachment; filename=".$attachment_info['basename']);
			readfile($attachment_location);
			if ($die) die();        
		} else {
			die("Error: File not found.");
		} 	
	}
	
	public function getProgress($error = NULL) {
		$result = array(
			'error' => false,
			'message' => '',
			'percent' => 0,
			'done' => false,
			'current' => 0,
			'all' => 0,
			'finishedImport' => false,
			'importingFile' => ''
		);
		
		if (!empty($error)) {
			$result['error'] = true;
			$result['message'] = $error;
			$result['done'] = true;
			$result['percent'] = 0;
			$this->setProgress($result);
		} else {
			if (file_exists(IMODULE_ROOT . 'temp/excelport_progress.pro')) {
				$fh = fopen(IMODULE_ROOT . 'temp/excelport_progress.pro', 'r');
				$data = fread($fh, filesize(IMODULE_ROOT . 'temp/excelport_progress.pro'));
				$result = json_decode($data, true);
			} else {
				$result['populateAll'] = true;
				$fh = fopen(IMODULE_ROOT . 'temp/excelport_progress.pro', 'w');
				fwrite($fh, json_encode($result));
			}
			fclose($fh);
		}
		
		return $result;
	}
	
	public function setProgress($progress) {
		if ($progress['all'] !== -1) {
			$progress['percent'] = $progress['all'] != 0 ? ceil($progress['current']*100/$progress['all']) : 0;
		} else {
			$progress['percent'] = 100;	
		}
		$fh = fopen(IMODULE_ROOT . 'temp/excelport_progress.pro', 'w');
		fwrite($fh, json_encode($progress));
		fclose($fh);
	}
	
	public function deleteProgress() {
		if (file_exists(IMODULE_ROOT . 'temp/excelport_progress.pro')) unlink(IMODULE_ROOT . 'temp/excelport_progress.pro');	
	}
	
	public function createZip($files = array(),$destination = '',$overwrite = false,$destinationFolder = '../temp/') {
		//FUNCTION FOUND FROM: http://davidwalsh.name/create-zip-php
		
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
			//make sure the file exists
				if(file_exists($destinationFolder.$file)) {
					$valid_files[] = $file;
				}
			}
		}
		
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($destinationFolder.$file,$file);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			
			//close the zip -- done!
			$zip->close();
			//check to make sure the file exists
			if (file_exists($destination)) return $destination;
			else return false;
		}
		else return false;
	}
	
	public function prepareUploadedFile($file) {
		$this->language->load('module/excelport');
		if (!file_exists($file)) throw new Exception('excelport_invalid_import_file');
		
		$info = pathinfo($file);
		$ext = $info['extension'];
		
		switch ($ext) {
			case 'xlsx' : {
				return array($file);
			} break;
			case 'zip' : {
				return $this->unzip($file, IMODULE_ROOT . 'temp/' . $info['filename'] . '/');
			} break;
		}
	}
	
	public function unzip($file, $decompressFolder) {
		$this->language->load('module/excelport');
		$zip = new ZipArchive();
		$success = array();
		if($zip->open($file, ZIPARCHIVE::CREATE) !== true) throw new Exception($this->language->get('excelport_unable_zip_file_open'));
		
		if (!file_exists($decompressFolder) || (file_exists($decompressFolder) && !is_dir($decompressFolder))) {
			if (!mkdir($decompressFolder, 0755)) throw new Exception($this->language->get('excelport_unable_create_unzip_folder'));
		}
		
		if (!$zip->extractTo($decompressFolder)) throw new Exception($this->language->get('excelport_unable_zip_file_extract'));
		
		
		//check the files
		$files = scandir($decompressFolder);
		
		foreach ($files as $tempFile) {
			if (in_array($tempFile, array('.', '..'))) continue;
			if (!file_exists($decompressFolder.$tempFile) || is_dir($decompressFolder.$tempFile)) continue;
			
			$tempInfo = pathinfo($tempFile);
			
			if ($tempInfo['extension'] == 'xlsx') $success[] = $decompressFolder . $tempFile;
		}
		return $success;	
	}
	
	public function deleteProducts() {
		$this->load->model('catalog/product');
		
		$ids = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product p");
		
		foreach ($ids->rows as $row) {
			$this->model_catalog_product->deleteProduct($row['product_id']);	
		}
	}
}
?>