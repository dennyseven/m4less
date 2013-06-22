<?php
class ModelCatalogSeo extends Model {
	public function getSeoPattern(){
		$sql = "SELECT * FROM " . DB_PREFIX . "seo_pattern ";
		$query = $this->db->query($sql);
		if($query->num_rows){
			return $query->row;
		}else{
			return 0;
		}
	}

	/*  PRODUCTS START  */
	public function generateProductUrlKeyword($template, $source_langcode, $pattern = array()) {
		$products = $this->getProducts();
		$keywords_in_db = $this->keywordsInDb($products,'product_id');

		$slugs = array();

		foreach ($products as $product) {
			$tags = array('[product_name]' => $product['name'],
                          '[model_name]' => $product['model'],
                          '[manufacturer_name]' => $product['manufacturer_name'],
                          '[product_price]' => $this->currency->format($product['price'],'','', false)
			);
			$slug = $uniqueSlug = $this->makeSlugs(strtr($template, $tags), 0, true, $source_langcode);
			$index = 1;
			while (in_array($uniqueSlug, $slugs) || in_array($uniqueSlug, $keywords_in_db)) {
				$uniqueSlug = $slug . '-' . $index++;
			}
			$slugs[] = $uniqueSlug;
			$this->setUrlAlias('product_id', $product['product_id'], $uniqueSlug);
		}
		$this->setPattern($pattern);
		$this->cache->delete('product');
	}

	private function getProducts() {
		$query = $this->db->query("SELECT p.product_id, pd.name, p.model, p.price, p.image, m.name as manufacturer_name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON(p.product_id=p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pd.name ASC");
		return $query->rows;
	}

	private function keywordsInDb($entity, $entity_id){
		$all_route_keywords = $this->getSeoRouteKeywords();

		$new_array = array();
		if($all_route_keywords){
			foreach ($all_route_keywords as $value) {
				$new_array[$value['query']] = $value['keyword'];
			}
			if($new_array && $entity){
				foreach ($entity as $value) {
					$new_entity_id = $entity_id;
					if($entity_id=='mall_category_id'){
						$new_entity_id = 'category_id';
					}
					$key = $entity_id . '=' . $value[$new_entity_id];
					if(array_key_exists($key, $new_array)){
						unset($new_array[$key]);
					}
				}
			}
		}
		return $new_array;
	}
	
	public function getSeoRouteKeywords(){
		$result = $this->db->query("SELECT ua.query,ua.keyword FROM " . DB_PREFIX . "url_alias ua ");
		if($result->num_rows){
			return $result->rows;
		}else{
			return 0;
		}
	}
	
	private function makeSlugs($string, $maxlen = 0, $noSpace = true, $source_langcode = null) {
		global $session;
		$newStringTab = array();
		$string = strtolower(trim(html_entity_decode($string, ENT_QUOTES, "UTF-8"))); //strtolower($this->_transliteration_process(trim(html_entity_decode($string, ENT_QUOTES, "UTF-8")), '-', $source_langcode));

		if (function_exists('str_split')) {
			$stringTab = str_split($string);
		} else {
			$stringTab = $this->my_str_split($string);
		}

		/*
		
		Original modified as below!

		$numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-");
		foreach ($stringTab as $letter) {
			if (in_array($letter, range("a", "z")) || in_array($letter, $numbers)) {
				$newStringTab[] = $letter;
			} elseif ($letter == " ") {
				if ($noSpace) {
					$newStringTab[] = "-";
				} else {
					$newStringTab[] = " ";
				}
			}
		}
		*/

		$special_characters = array('+',
									'*',
									'/',
									'~',
									'`',
									'!',
									'@',
									'$',
									'%',
									'^',
									'&',
									'(',
									')',
									'_',
									'{',
									'}',
									'[',
									']',
									'|',
									'\\',
									':',
									';',
									'"',
									'\'',
									'<',
									'>',
									'?',
									',',									
									'=');


		foreach ($stringTab as $letter) {
			if ($letter == " ") {
				if ($noSpace) {
					$newStringTab[] = "-";
				} else {
					$newStringTab[] = " ";
				}
			} else if(!in_array($letter, $special_characters)){
				$newStringTab[] = $letter;
			}		
		}

		if (count($newStringTab)) {
			$newString = implode($newStringTab);

			if ($maxlen > 0) {
				$newString = substr($newString, 0, $maxlen);
			}
			$newString = $this->removeDuplicates('--', '-', $newString);
		} else {
			$newString = '';
		}

		return $newString;
	}
	
	/**
	 * Transliterates UTF-8 encoded text to US-ASCII.
	 *
	 * Based on Mediawiki's UtfNormal::quickIsNFCVerify().
	 *
	 * @param $string
	 *   UTF-8 encoded text input.
	 * @param $unknown
	 *   Replacement string for characters that do not have a suitable ASCII
	 *   equivalent.
	 * @param $source_langcode
	 *   Optional ISO 639 language code that denotes the language of the input and
	 *   is used to apply language-specific variations. If the source language is
	 *   not known at the time of transliteration, it is recommended to set this
	 *   argument to the site default language to produce consistent results.
	 *   Otherwise the current display language will be used.
	 * @return
	 *   Transliterated text.
	 */
	function _transliteration_process($string, $unknown = '?', $source_langcode = NULL) {
		// ASCII is always valid NFC! If we're only ever given plain ASCII, we can
		// avoid the overhead of initializing the decomposition tables by skipping
		// out early.
		if (!preg_match('/[\x80-\xff]/', $string)) {
			return $string;
		}
		static $tailBytes;

		if (!isset($tailBytes)) {
			// Each UTF-8 head byte is followed by a certain number of tail bytes.
			$tailBytes = array();
			for ($n = 0; $n < 256; $n++) {
				if ($n < 0xc0) {
					$remaining = 0;
				}
				elseif ($n < 0xe0) {
					$remaining = 1;
				}
				elseif ($n < 0xf0) {
					$remaining = 2;
				}
				elseif ($n < 0xf8) {
					$remaining = 3;
				}
				elseif ($n < 0xfc) {
					$remaining = 4;
				}
				elseif ($n < 0xfe) {
					$remaining = 5;
				}
				else {
					$remaining = 0;
				}
				$tailBytes[chr($n)] = $remaining;
			}
		}
		// Chop the text into pure-ASCII and non-ASCII areas; large ASCII parts can
		// be handled much more quickly. Don't chop up Unicode areas for punctuation,
		// though, that wastes energy.
		preg_match_all('/[\x00-\x7f]+|[\x80-\xff][\x00-\x40\x5b-\x5f\x7b-\xff]*/', $string, $matches);

		$result = '';
		foreach ($matches[0] as $str) {
			if ($str[0] < "\x80") {
				// ASCII chunk: guaranteed to be valid UTF-8 and in normal form C, so
				// skip over it.
				$result .= $str;
				continue;
			}

			// We'll have to examine the chunk byte by byte to ensure that it consists
			// of valid UTF-8 sequences, and to see if any of them might not be
			// normalized.
			//
			// Since PHP is not the fastest language on earth, some of this code is a
			// little ugly with inner loop optimizations.

			$head = '';
			$chunk = strlen($str);
			// Counting down is faster. I'm *so* sorry.
			$len = $chunk + 1;

			for ($i = -1; --$len;) {
				$c = $str[++$i];
				if ($remaining = $tailBytes[$c]) {
					// UTF-8 head byte!
					$sequence = $head = $c;
					do {
						// Look for the defined number of tail bytes...
						if (--$len && ($c = $str[++$i]) >= "\x80" && $c < "\xc0") {
							// Legal tail bytes are nice.
							$sequence .= $c;
						}
						else {
							if ($len == 0) {
								// Premature end of string! Drop a replacement character into
								// output to represent the invalid UTF-8 sequence.
								$result .= $unknown;
								break 2;
							}
							else {
								// Illegal tail byte; abandon the sequence.
								$result .= $unknown;
								// Back up and reprocess this byte; it may itself be a legal
								// ASCII or UTF-8 sequence head.
								--$i;
								++$len;
								continue 2;
							}
						}
					} while (--$remaining);

					$n = ord($head);
					if ($n <= 0xdf) {
						$ord = ($n - 192) * 64 + (ord($sequence[1]) - 128);
					}
					elseif ($n <= 0xef) {
						$ord = ($n - 224) * 4096 + (ord($sequence[1]) - 128) * 64 + (ord($sequence[2]) - 128);
					}
					elseif ($n <= 0xf7) {
						$ord = ($n - 240) * 262144 + (ord($sequence[1]) - 128) * 4096 + (ord($sequence[2]) - 128) * 64 + (ord($sequence[3]) - 128);
					}
					elseif ($n <= 0xfb) {
						$ord = ($n - 248) * 16777216 + (ord($sequence[1]) - 128) * 262144 + (ord($sequence[2]) - 128) * 4096 + (ord($sequence[3]) - 128) * 64 + (ord($sequence[4]) - 128);
					}
					elseif ($n <= 0xfd) {
						$ord = ($n - 252) * 1073741824 + (ord($sequence[1]) - 128) * 16777216 + (ord($sequence[2]) - 128) * 262144 + (ord($sequence[3]) - 128) * 4096 + (ord($sequence[4]) - 128) * 64 + (ord($sequence[5]) - 128);
					}
					$result .= $this->_transliteration_replace($ord, $unknown, $source_langcode);
					$head = '';
				}
				elseif ($c < "\x80") {
					// ASCII byte.
					$result .= $c;
					$head = '';
				}
				elseif ($c < "\xc0") {
					// Illegal tail bytes.
					if ($head == '') {
						$result .= $unknown;
					}
				}
				else {
					// Miscellaneous freaks.
					$result .= $unknown;
					$head = '';
				}
			}
		}
		return $result;
	}

	function _transliteration_replace($ord, $unknown = '?', $langcode = NULL) {
        static $map = array();
        
        $bank = $ord >> 8;

        if (!isset($map[$bank][$langcode])) {
            $file = dirname(__FILE__) . '/seo_data/' . sprintf('x%02x', $bank) . '.php';
            if (file_exists($file)) {
                include $file;
                if ($langcode != 'en' && isset($variant[$langcode])) {
                    // Merge in language specific mappings.
                    $map[$bank][$langcode] = $variant[$langcode] + $base;
                }
                else {
                    $map[$bank][$langcode] = $base;
                }
            }
            else {
                $map[$bank][$langcode] = array();
            }
        }
        $a = isset($map[$bank][$langcode][$ord]) ? $map[$bank][$langcode][$ord] : '9';
        $ord = $ord & 255;

        return isset($map[$bank][$langcode][$ord]) ? $map[$bank][$langcode][$ord] : $unknown;
    }

	private function removeDuplicates($sSearch, $sReplace, $sSubject) {
		$i = 0;
		do {
			$sSubject = str_replace($sSearch, $sReplace, $sSubject);
			$pos = strpos($sSubject, $sSearch);
			$i++;
			if ($i > 100) {
				die('removeDuplicates() loop error');
			}
		} while ($pos !== false);
		return $sSubject;
	}
	
	private function setUrlAlias($column, $id, $keyword){
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = '" . $column . "=" . (int)$id. "'");

		if (isset($keyword) && $keyword) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '" . $column . "=" . (int)$id . "', keyword = '" . $this->db->escape($keyword) . "'");
		}
	}

	private function setPattern($pattern){

		$count = $this->db->query("SELECT COUNT(*) as count FROM " . DB_PREFIX . "seo_pattern ")->row['count'];

		if($count){
			$sql = "UPDATE " . DB_PREFIX . "seo_pattern
                    SET ";  
		}else{
			$sql = "INSERT INTO " . DB_PREFIX . "seo_pattern
                    SET ";
		}

		// $key used here has to be the actual column name in seo_pattern table
		foreach($pattern as $key => $value){
			$sql .= "" . $key . " = '" . $value . "'";
		}

		$this->db->query($sql);
	}

	public function generateProductTitle($template, $source_langcode, $pattern = array()) {
		$products = $this->getProductsForMetaKeywords();
		$this->clearProduct();
		foreach ($products as $product) {            
		    $stripped = substr(trim(strip_tags(html_entity_decode($product['description']))), 0, 60);
		    $tags = array(
		        '[product_name]' 		=> $product['name'], 
		        '[product_description]' => $stripped,
		        '[product_price]'		=> $this->currency->format($product['price'],'','', true),
		        '[model_name]'			=> $product['model'],
		        '[manufacturer_name]'	=> $product['manufacturer_name']
		    );
		    $finalTitle = array();
		    $titles = explode(',', strtr($template, $tags));
		    foreach ($titles as $title) {
		        if(!trim($title)) continue;
		        $finalTitle[] = $title;
		    }
		    $finalTitle = array_filter(array_unique($finalTitle));
		    $finalTitle = implode('-',$finalTitle);
		    $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_data` SET `title` = '" . $this->db->escape($finalTitle) . "', `type` = 'product', `id` = '" . (int)$product['product_id'] . "', `language_id` = '". (int)$product['language_id'] ."' ");
		}
		$this->setPattern($pattern);
	    $this->cache->delete('product');
	}
	
	public function generateProductMetaKeywords($template, $yahooID = null, $source_langcode, $pattern = array()) {
		$products = $this->getProductsForMetaKeywords();
		$slugs = array();
		foreach ($products as $product) {
			$finalCategories = array();
			$categories = $this->getProductCategories($product['product_id'], $product['language_id']);
			foreach ($categories as $category) {
				$finalCategories[] = $category['name'];
			}
			$tags = array('[product_name]' => $product['name'],
                          '[model_name]' => $product['model'],
                          '[manufacturer_name]' => $product['manufacturer_name'],
                          '[categories_names]' => implode(',', $finalCategories),
                          '[product_price]' => $this->currency->format($product['price'],'','', false)

			);
			$finalKeywords = array();
			$keywords = explode(',', strtr($template, $tags));
			if ($yahooID != null) {
				$keywords = array_merge($keywords, $this->getYahooKeywords($yahooID, $product['description']));
			}
			foreach ($keywords as $keyword) {
				$finalKeywords[] = $this->makeSlugs(trim($keyword), 0, false, $source_langcode);
			}
			$finalKeywords = array_filter(array_unique($finalKeywords));
			$finalKeywords = implode(', ', $finalKeywords);
			$this->db->query("UPDATE " . DB_PREFIX . "product_description SET meta_keyword = '" . $this->db->escape($finalKeywords) . "' where product_id = " . (int)$product['product_id'] . " and language_id = " . (int)$product['language_id']);
		}
		$this->setPattern($pattern);
		$this->cache->delete('product');
	}

	private function getProductsForMetaKeywords() {
		$query = $this->db->query("SELECT p.product_id, pd.name, p.model, p.price, m.name as manufacturer_name, pd.description, pd.language_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON(p.product_id=p2s.product_id) ORDER BY pd.name ASC");
		return $query->rows;
	}
	
	private function getProductCategories($productId, $languageId) {
		$query = $this->db->query("SELECT c.category_id, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) INNER JOIN " . DB_PREFIX . "product_to_category pc ON (pc.category_id = c.category_id)  WHERE cd.language_id = " . (int)$languageId . " AND pc.product_id = " . (int)$productId . " ORDER BY c.sort_order, cd.name ASC");
		return $query->rows;
	}

	public function generateProductMetaDescription($template, $source_langcode, $pattern = array()) {
		$products = $this->getProductsForMetaKeywords();
		$slugs = array();
		foreach ($products as $product) {
			$stripped = substr(trim(strip_tags(html_entity_decode($product['description']))), 0, 120);
			$tags = array('[product_name]' => $product['name'],
                          '[product_description]' => $stripped,
                          '[product_price]' => $this->currency->format($product['price'],'','', false)

			);
			$finalDescription = array();
			$descriptions = explode(',', strtr($template, $tags));
			foreach ($descriptions as $description) {
				$finalDescription[] = $description;
			}
			$finalDescription = array_filter(array_unique($finalDescription));
			$finalDescription = implode(',',$finalDescription);
			$this->db->query("UPDATE " . DB_PREFIX . "product_description SET meta_description = '" . $this->db->escape($finalDescription) . "' where product_id = " . (int)$product['product_id'] . " and language_id = " . (int)$product['language_id']);
		}
		$this->setPattern($pattern);
		$this->cache->delete('product');
	}

	public function generateProductTags($template, $source_langcode, $pattern = array()) {
		$products = $this->getProductsForMetaKeywords();
		$slugs = array();
		foreach ($products as $product) {
			$finalCategories = array();
			$categories = $this->getProductCategories($product['product_id'], $product['language_id']);
			foreach ($categories as $category) {
				$finalCategories[] = $category['name'];
			}
			$tags = array('[product_name]' => $product['name'],
                          '[model_name]' => $product['model'],
                          '[manufacturer_name]' => $product['manufacturer_name'],
                          '[categories_names]' => implode(',', $finalCategories)

			);
			$finalKeywords = array();
			$keywords = explode(',', strtr($template, $tags));
			foreach ($keywords as $keyword) {
				$finalKeywords[] =  $this->makeSlugs(trim($keyword), 0, false, $source_langcode);
			}
			$finalKeywords = array_filter(array_unique($finalKeywords));
                        $this->db->query("UPDATE " . DB_PREFIX . "product_description SET tag = '" . $this->db->escape(implode(' ,', $finalKeywords)) . "' WHERE product_id = '" . (int) $product['product_id'] . "' AND language_id = '" . (int) $product['language_id'] . "'");

		}
		$this->setPattern($pattern);
		$this->cache->delete('product');
	}
	
	public function generateProductImage($template, $source_langcode, $pattern = array()) {
		$products = $this->getProducts();
		$slugs = array();

		foreach ($products as $product) {
			$tags = array('[product_name]' => $product['name']
			);
			$slug = $uniqueSlug = $this->makeSlugs(strtr($template, $tags), 0, true, $source_langcode);
			$index = 1;
			while (in_array($uniqueSlug, $slugs)) {
				$uniqueSlug = $slug . '-' . $index++;
			}
			$slugs[] = $uniqueSlug;
						
			
			if($product['image']) {
				$file = $product['image'];
				$info = pathinfo($file);
				$new_image = $info['dirname'] . '/' . $uniqueSlug . '.' . $info['extension'];
				if(file_exists(DIR_IMAGE.$file)) {
					rename(DIR_IMAGE.$file, DIR_IMAGE.$new_image);
					$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $new_image . "' WHERE image LIKE '" . $product['image'] . "'");
					$this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $new_image . "' WHERE image LIKE '" . $product['image'] . "'");
					$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $new_image . "' WHERE image LIKE '" . $product['image'] . "'");
					$this->db->query("UPDATE " . DB_PREFIX . "product_image SET image = '" . $new_image . "' WHERE image LIKE '" . $product['image'] . "'");
				}
			}
			
		}

		// Deleting existing cache content from current store
		$dir  = DIR_IMAGE.'cache/data/';
		$this->clearStoreCache($dir);
		$this->setPattern($pattern);
		$this->cache->delete('product');
		$this->cache->delete('category');
		$this->cache->delete('manufacturer');
	}
	/*  PRODUCTS END  */
	
	private function clearStoreCache($dir){
		$files = glob($dir . '*.*');

		if ($files) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					unlink($file);
					clearstatcache();
				}
			}
		}
	}
	
	/*  CATEGORIES START  */
	public function generateCategoryUrlKeyword($template, $source_langcode, $pattern = array()) {
		$categories = $this->getCategories();
		$keywords_in_db = $this->keywordsInDb($categories,'category_id');

		$slugs = array();
		foreach ($categories as $category) {
			$tags = array('[category_name]' => $category['name']);
			$slug = $uniqueSlug = $this->makeSlugs(strtr($template, $tags), 0, true, $source_langcode);
			$index = 1;
			while (in_array($uniqueSlug, $slugs) || in_array($uniqueSlug, $keywords_in_db)) {
				$uniqueSlug = $slug . '-' . $index++;
			}
			$slugs[] = $uniqueSlug;
			$this->setUrlAlias('category_id', $category['category_id'], $uniqueSlug);
		}
		$this->setPattern($pattern);
		$this->cache->delete('category');
	}
	
    public function generateCategoryTitle($template, $source_langcode, $pattern = array()) {
		$categories = $this->getCategories();
        $this->clearCategory();
		foreach ($categories as $category) {		    
		    $stripped = substr(trim(strip_tags(html_entity_decode($category['description']))), 0, 60);
			$tags = array(
				'[category_name]' => $category['name'], 
				'[category_description]' => $stripped
			);
			$finalTitle = array();
			$titles = explode(',', strtr($template, $tags));
			foreach ($titles as $title) {
				if(!trim($title)) continue;
                $finalTitle[] = $title;
            }
            $finalTitle = array_filter(array_unique($finalTitle));
            $finalTitle = implode('-',$finalTitle);
			$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_data` SET `title` = '" . $this->db->escape($finalTitle) . "', `type` = 'category', `id` = '" . (int)$category['category_id'] . "', `language_id` = '". (int)$category['language_id'] ."' ");
		}
		$this->setPattern($pattern);
		$this->cache->delete('category');
	}

	public function generateCategoryMetaKeywords($template, $source_langcode, $pattern = array()) {
		$categories = $this->getCategoriesForMetaKeywords();
		$slugs = array();
		foreach ($categories as $category) {
			$stripped = substr(trim(strip_tags(html_entity_decode($category['description']))), 0, 120);
			$tags = array('[category_name]' => $category['name'],
                          '[category_description]' => $stripped

			);
			$finalDescription = array();
			$descriptions = explode(',', strtr($template, $tags));
			foreach ($descriptions as $description) {
				$finalDescription[] = $description;
			}
			$finalDescription = array_filter(array_unique($finalDescription));
			$finalDescription = implode(',',$finalDescription);

			$this->db->query("UPDATE " . DB_PREFIX . "category_description SET meta_keyword = '" . $this->db->escape($finalDescription) . "' where category_id = " . (int)$category['category_id'] . " and language_id = " . (int)$category['language_id']);
		}
		$this->setPattern($pattern);
		$this->cache->delete('category');
	}
	
	public function generateCategoryMetaDescription($template, $source_langcode, $pattern = array()) {
		$categories = $this->getCategoriesForMetaKeywords();
		$slugs = array();
		foreach ($categories as $category) {
			$stripped = substr(trim(strip_tags(html_entity_decode($category['description']))), 0, 120);
			$tags = array('[category_name]' => $category['name'],
                          '[category_description]' => $stripped

			);
			$finalDescription = array();
			$descriptions = explode(',', strtr($template, $tags));
			foreach ($descriptions as $description) {
				$finalDescription[] = $description;
			}
			$finalDescription = array_filter(array_unique($finalDescription));
			$finalDescription = implode(',',$finalDescription);

			$this->db->query("UPDATE " . DB_PREFIX . "category_description SET meta_description = '" . $this->db->escape($finalDescription) . "' where category_id = " . (int)$category['category_id'] . " and language_id = " . (int)$category['language_id']);
		}
		$this->setPattern($pattern);
		$this->cache->delete('category');
	}

	private function getCategories() {
		$query = $this->db->query("SELECT c.category_id, c.image, cd.name, cd.description, cd.language_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id=c2s.category_id) ORDER BY c.sort_order, cd.name ASC");
		return $query->rows;
	}

	private function getCategoriesForMetaKeywords() {
		$query = $this->db->query("SELECT c.category_id, cd.name, cd.description, cd.language_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id=c2s.category_id) ORDER BY c.sort_order, cd.name ASC");
		return $query->rows;
	}
	
	/*  CATEGORIES END  */
	
	/*  MANUFACTURERS START  */
	public function generateManufacturerUrlKeyword($template, $source_langcode, $pattern = array()) {
		$manufacturers = $this->getManufacturers();
		$keywords_in_db = $this->keywordsInDb($manufacturers,'manufacturer_id');

		$slugs = array();
		foreach ($manufacturers as $manufacturer) {
			$tags = array('[manufacturer_name]' => $manufacturer['name']);
			$slug = $uniqueSlug = $this->makeSlugs(strtr($template, $tags), 0, true, $source_langcode);
			$index = 1;
			while (in_array($uniqueSlug, $slugs) || in_array($uniqueSlug, $keywords_in_db)) {
				$uniqueSlug = $slug . '-' . $index++;
			}
			$slugs[] = $uniqueSlug;
			$this->setUrlAlias('manufacturer_id', $manufacturer['manufacturer_id'], $uniqueSlug);
		}
		$this->setPattern($pattern);
		$this->cache->delete('manufacturer');
	}
	/*  MANUFACTURERS END  */

	/*  INFORMATION PAGES START  */
	public function generateInformationPageUrlKeyword($template, $source_langcode, $pattern = array()) {
		$information_pages = $this->getInformationPages();
		$keywords_in_db = $this->keywordsInDb($information_pages,'information_id');

		$slugs = array();
		foreach ($information_pages as $information_page) {
			$tags = array('[information_page_title]' => $information_page['title']);
			$slug = $uniqueSlug = $this->makeSlugs(strtr($template, $tags), 0, true, $source_langcode);
			$index = 1;
			while (in_array($uniqueSlug, $slugs)) {
				$uniqueSlug = $slug . '-' . $index++;
			}
			$slugs[] = $uniqueSlug;
			$this->setUrlAlias('information_id', $information_page['information_id'], $uniqueSlug);
		}
		$this->setPattern($pattern);
		$this->cache->delete('information');
	}

	public function generateInformationPageTitle($template, $source_langcode, $pattern = array()) {
		$information_pages = $this->getInformationPages();
		$this->clearInformation();
		foreach ($information_pages as $information_page) {		    
		    $stripped = substr(trim(strip_tags(html_entity_decode($information_page['description']))), 0, 60);
			$tags = array(
				'[information_page_title]' => $information_page['title'], 
				'[information_page_description]' => $stripped
			);
			$finalTitle = array();
            $titles = explode(',', strtr($template, $tags));
            foreach ($titles as $title) {
                if(!trim($title)) continue;
                $finalTitle[] = $title;
            }
            $finalTitle = array_filter(array_unique($finalTitle));
            $finalTitle = implode('-',$finalTitle);
			$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_data` SET `title` = '" . $this->db->escape($finalTitle) . "', `type` = 'information', `id` = '" . (int)$information_page['information_id'] . "', `language_id` = '". (int)$information_page['language_id'] ."' ");
		}
		$this->setPattern($pattern);
		$this->cache->delete('information');
	}

	/*  INFORMATION PAGES END  */

	private function getManufacturers() {
		$query = $this->db->query("SELECT m.manufacturer_id, m.name FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id=m2s.manufacturer_id) ORDER BY m.name ASC");
		return $query->rows;
	}

	private function getInformationPages(){
		$query = $this->db->query("SELECT id.information_id,id.title, id.description, id.language_id FROM " . DB_PREFIX . "information_description id LEFT JOIN " . DB_PREFIX . "information i ON (id.information_id = i.information_id)  LEFT JOIN " . DB_PREFIX . "information_to_store its ON (id.information_id = its.information_id) ORDER BY id.title ASC");
		return $query->rows;
	}

	public function clearGeneral() {
		$query = $this->db->query("SELECT url_alias_id FROM `" . DB_PREFIX . "seo_data` WHERE type = 'general'");
		
		if($query->num_rows){
			$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_data` WHERE type = 'general'");
			$url_alias_ids = $query->rows;
			foreach($url_alias_ids as $value) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE url_alias_id = '" . (int)$value['url_alias_id'] . "'");
			}
		}
	}
	
	public function clearCategory() {
    	$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_data` WHERE type = 'category'");
	}

	public function clearInformation(){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_data` WHERE type = 'information'");
	}
	
	public function clearProduct() {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_data` WHERE type = 'product'");
	}

	public function createTablesInDatabse() {
        if (mysql_num_rows( mysql_query("SHOW TABLES LIKE '". DB_PREFIX ."seo_data'")) != '1') {
            $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "seo_data` (
                      `title` varchar(255) COLLATE utf8_bin NOT NULL,
                      `meta_keywords` varchar(255) COLLATE utf8_bin NOT NULL,
                      `meta_description` varchar(255) COLLATE utf8_bin NOT NULL,
                      `type` varchar(32) COLLATE utf8_bin NOT NULL,
                      `id` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'It will be id for products, category, manufacturer, information and route for general urls',
                      `language_id` int(11) NOT NULL,
                      `url_alias_id` int(11) NOT NULL,
                      UNIQUE KEY `type` (`type`,`id`,`language_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
            $this->db->query($sql);
        }

        if (mysql_num_rows( mysql_query("SHOW TABLES LIKE '". DB_PREFIX ."seo_pattern'")) != '1') {
            $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "seo_pattern` (
                      `pattern_id` int(11) NOT NULL AUTO_INCREMENT,
                      `product_url_keyword` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_title` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_meta_keywords` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_meta_description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_tags` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_image_name` text NOT NULL,
                      `category_url_keyword` text NOT NULL,
                      `category_title` text NOT NULL,
                      `category_keyword` text NOT NULL,
                      `category_meta_description` text NOT NULL,
                      `manufacturer_url_keyword` text NOT NULL,
                      `information_page_url_keyword` text NOT NULL,
                      `information_pages_title` text NOT NULL,
                      `yahoo_id` int(11) NOT NULL,
                      PRIMARY KEY (`pattern_id`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
            $this->db->query($sql);
        }
    }
}
