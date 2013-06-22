<?php

abstract class SeoAbstract {
	protected $_title;
	protected $_meta_keywords;
	protected $_meta_description;
	protected $_seo_keyword;
	protected $_url_query;
	protected $_db;
	protected $_id;
	protected $_url_alias_id;
	protected $_registry;
	
	public function __construct($registry) {
		$this->_registry = $registry;
	}
	
	public function getTitle() {
		return $this->_title;
	}

	public function getMetaKeywords() {
		return $this->_meta_keywords;
	}

	public function getMetaDescription() {
		return $this->_meta_description;
	}

	public function getSeoKeyword() {
		return $this->_seo_keyword;
	}
	
	public function getQuery() {
		return $this->_url_query;
	}
	
	public function getId() {
		return $this->_id;
	}
	
	public function getUrlAliasId() {
		return $this->_url_alias_id;
	}
	
	public function setTitle($title) {
		$this->_title = $title;
	}

	public function setMetaKeywords($keywords) {
		$this->_meta_keywords = $keywords;
	}

	public function setMetaDescription($description) {
		$this->_meta_description = $description;
	}

	public function setSeoKeyword($keyword) {
		$this->_seo_keyword = $keyword;
	}

	public function setQuery($query) {
		$this->_url_query = $query;
	}

	public function setDb($db) {
		$this->_db = $db;
	}

	public function setId($id) {
		$this->_id = $id;
	}
	
	public function setUrlAliasId($url_alias_id) {
		$this->_url_alias_id = $url_alias_id;	
	}
	
}