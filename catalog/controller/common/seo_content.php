<?php
class ControllerCommonSeoContent extends Controller {
	
	public function index() {
		
		$info = Seo::getType($this->request->get);
		
		$object = Seo::getObject($info['type'], $info['id'], $this->registry);
		
		$titles = $object->getTitle();

		//change the title only if customize title is present else keep the default
		if(isset($titles[$this->config->get('config_language_id')]) && $titles[$this->config->get('config_language_id')]) {
			$this->document->setTitle($titles[$this->config->get('config_language_id')]);
		}
		
		$meta_keywords = $object->getMetaKeywords();

		if(isset($meta_keywords[$this->config->get('config_language_id')])) {
			$this->document->setKeywords($meta_keywords[$this->config->get('config_language_id')]);
		}
		
		$meta_description = $object->getMetaDescription();

		if(isset($meta_description[$this->config->get('config_language_id')])) {
		    $this->document->setDescription($meta_description[$this->config->get('config_language_id')]);
        }
		
	}
}
