<?php
class ControllerCatalogSeo extends Controller {

    protected $error = array();

    public function customize() {
        $this->load->model('catalog/seo');
        $this->model_catalog_seo->createTablesInDatabse();
        //$this->load->helper('seo_validator');

        $helper = 'seo_validator';

        $file = DIR_SYSTEM . 'helper/' . $helper . '.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load helper ' . $helper . '!');
            exit();
        }

        $this->validate = new SeoValidator($this->registry);

        $this->load->language('catalog/seo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/jquery/jquery.tipTip.minified.js');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate($this->request->post))) {

            $post_data = $this->request->post;

            switch ($post_data['tab']) {
                case 'tab_general' :
                    $this->model_catalog_seo->clearGeneral();

                    if(isset($post_data['custom_url_store'])) {
                        foreach($post_data['custom_url_store'] as $row) {
                            $object = new SeoGeneral($this->registry);
                            $object->setSeoKeyword($row['id']['keyword']);
                            $object->setQuery('route='.$row['id']['query']);

                            $title = $meta_description = $meta_keywords = array();

                            foreach($row['custom_url_store_description'] as $language_id => $r) {
                                $title[$language_id] = $r['name'];
                                $meta_keywords[$language_id] = $r['meta_keywords'];
                                $meta_description[$language_id] = $r['meta_description'];
                            }

                            $object->setTitle($title);
                            $object->setId($row['id']['query']);
                            $object->setMetaKeywords($meta_keywords);
                            $object->setMetaDescription($meta_description);
                            $object->save();
                        }
                    }
                    break;
                case 'tab_products' :

                    foreach($post_data['product']['product_id'] as $product_id => $keyword) {
                        $object = new SeoProduct($this->registry);
                        $object->setSeoKeyword($keyword);
                        $object->setQuery('product_id='.$product_id);

                        $title = $meta_description = $meta_keywords = array();

                        foreach($post_data['product']['product_description'][$product_id] as $language_id => $r) {
                            $title[$language_id] = $r['title'];
                            $meta_keywords[$language_id] = $r['meta_keywords'];
                            $meta_description[$language_id] = $r['meta_description'];
                        }

                        $object->setTitle($title);
                        $object->setId($product_id);
                        $object->setMetaKeywords($meta_keywords);
                        $object->setMetaDescription($meta_description);
                        $object->save();
                    }
                    break;
                case 'tab_categories' :

                    foreach($post_data['category']['category_id'] as $category_id => $keyword) {
                        $object = new SeoCategory($this->registry);
                        $object->setSeoKeyword($keyword);
                        $object->setQuery('category_id='.$category_id);

                        $title = $meta_description = $meta_keywords = array();

                        foreach($post_data['category']['category_description'][$category_id] as $language_id => $r) {
                            $title[$language_id] = $r['title'];
                            $meta_keywords[$language_id] = $r['meta_keywords'];
                            $meta_description[$language_id] = $r['meta_description'];
                        }

                        $object->setTitle($title);
                        $object->setId($category_id);
                        $object->setMetaKeywords($meta_keywords);
                        $object->setMetaDescription($meta_description);
                        $object->save();
                    }
                    break;

                case 'tab_manufacturers' :

                        foreach($post_data['manufacturer']['manufacturer_id'] as $manufacturer_id => $keyword) {

                        $object = new SeoManufacturer($this->registry);
                        $object->setSeoKeyword($keyword);
                        $object->setQuery('manufacturer_id='.$manufacturer_id);

                        $title = $meta_description = $meta_keywords = array();

                        foreach($post_data['manufacturer']['manufacturer_description'][$manufacturer_id] as $language_id => $r) {
                            $title[$language_id] = $r['title'];
                            $meta_keywords[$language_id] = $r['meta_keywords'];
                            $meta_description[$language_id] = $r['meta_description'];
                        }

                        $object->setTitle($title);
                        $object->setId($manufacturer_id);
                        $object->setMetaKeywords($meta_keywords);
                        $object->setMetaDescription($meta_description);
                        $object->save();
                        }

                    break;

                case 'tab_information_pages' :

                    foreach($post_data['information']['information_id'] as $information_id => $keyword) {

                        $object = new SeoInformation($this->registry);
                        $object->setSeoKeyword($keyword);
                        $object->setQuery('information_id='.$information_id);

                        $title = $meta_description = $meta_keywords = array();

                        foreach($post_data['information']['information_description'][$information_id] as $language_id => $r) {
                            $title[$language_id] = $r['title'];
                            $meta_keywords[$language_id] = $r['meta_keywords'];
                            $meta_description[$language_id] = $r['meta_description'];
                        }

                        $object->setTitle($title);
                        $object->setId($information_id);
                        $object->setMetaKeywords($meta_keywords);
                        $object->setMetaDescription($meta_description);
                        $object->save();

                    }

                    break;

            }

            $this->data['tab'] = $post_data['tab'];
            $this->session->data['success'] = $this->language->get('text_success_'.$post_data['tab']);

            $this->redirect(HTTPS_SERVER . 'index.php?route=catalog/seo/customize&token=' . $this->session->data['token'] . '&tab=' . $this->data['tab']);

        }

        $this->data['success'] = '';
        if(isset($this->session->data['success'])){
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if (isset($this->request->get['tab'])) {
            $tab = $this->request->get['tab'];
        } else {
            $tab = 'tab_general';
        }

        if (isset($this->request->get['filter_keyword'])) {
			$filter_keyword = $this->request->get['filter_keyword'];
		} else {
			$filter_keyword = '';
		}
		
        $url = '';
						
		if (isset($this->request->get['filter_keyword'])) {
			$url .= '&filter_keyword=' . urlencode(html_entity_decode($this->request->get['filter_keyword'], ENT_QUOTES, 'UTF-8'));
		}		

        $this->data['tab'] = isset($this->data['tab']) ? $this->data['tab'] : $tab;

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['description_st'] = $this->language->get('description');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_keyword'] = $this->language->get('entry_keyword');

        $this->data['column_url'] = $this->language->get('column_url');
        $this->data['column_keyword'] = $this->language->get('column_keyword');
        $this->data['column_image'] = $this->language->get('column_image');
        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_title'] = $this->language->get('column_title');
        $this->data['column_keyword'] = $this->language->get('column_keyword');
        $this->data['column_meta_keyword'] = $this->language->get('column_meta_keyword');
        $this->data['column_meta_description'] = $this->language->get('column_meta_description');
        $this->data['custom_url_help'] = $this->language->get('custom_url_help');

        $this->data['tab_general']  = $this->language->get('tab_general');
        $this->data['tab_products'] = $this->language->get('tab_products');
        $this->data['tab_categories'] = $this->language->get('tab_categories');
        $this->data['tab_manufacturers'] = $this->language->get('tab_manufacturers');
        $this->data['tab_information_pages'] = $this->language->get('tab_information_pages');

        $this->data['domain'] = $this->config->get('config_url');

        $this->data['button_autofill'] = $this->language->get('button_autofill');
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_filter'] = $this->language->get('button_filter');
        $this->data['button_reset'] = $this->language->get('button_reset');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_remove'] = $this->language->get('button_remove');
        $this->data['button_add_custom_url_store'] = $this->language->get('button_add_custom_url_store');
        $this->data['button_save_general'] = $this->language->get('button_save_general');
        $this->data['button_save_products'] = $this->language->get('button_save_products');
        $this->data['button_save_categories'] = $this->language->get('button_save_categories');
        $this->data['button_save_manufacturers'] = $this->language->get('button_save_manufacturers');
        $this->data['button_save_information_pages'] = $this->language->get('button_save_information_pages');

        $this->data['title_help'] = $this->language->get('title_help');
        $this->data['keywords_help'] = $this->language->get('keywords_help');
        $this->data['description_help'] = $this->language->get('description_help');

        $this->load->model('localisation/language');

        $this->languages = $this->model_localisation_language->getLanguages();

        foreach($this->languages as $language){
            $this->data['currency'] = $language['language_id'];
            break;
        }

        if (isset($this->error['title'])) {
            foreach($this->error['title'] as $key => $value){
                $this->data['error_'.$key] = $this->error['title'][$key];
            }
        }

        if(isset($this->error['already_exists'])){
            $this->data['error_already_exists'] = $this->error['already_exists'];
        }else{
            $this->data['error_already_exists'] = '';
        }

        $this->data['languages'] = $this->languages;

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['code'])) {
            $this->data['error_code'] = $this->error['code'];
        } else {
            $this->data['error_code'] = '';
        }       

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
            'text'      => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href'      => HTTPS_SERVER . 'index.php?route=catalog/seo/customize&token=' . $this->session->data['token'],
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['autofill'] = HTTPS_SERVER . 'index.php?route=catalog/seo/loadgeneralauto&token=' . $this->session->data['token'];
        $this->data['action'] = HTTPS_SERVER . 'index.php?route=catalog/seo/customize&token=' . $this->session->data['token'];
        $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=catalog/seo/customize&token=' . $this->session->data['token'];
        $this->data['filter'] = 'index.php?route='.$this->request->get['route'].'&token='.$this->request->get['token'].'&tab='.$tab;

        $this->load->model('tool/image');

        $this->config_admin_limit = $this->config->get('config_admin_limit');

        $this->data['custom_url_store_data'] = array();
        $this->data['products'] = array();
        $this->data['categories'] = array();
        $this->data['manufacturers'] = array();
        $this->data['informations'] = array();

        switch ($this->data['tab']) {
            case 'tab_products':
                $this->loadProducts($filter_keyword);
                break;
            case 'tab_categories':
                $this->loadCategories();
                break;
            case 'tab_manufacturers':
                $this->loadManufactures();
                break;
            case 'tab_information_pages':
                $this->loadInformationPages();
                break;
            case 'tab_general':
            default:
                $this->loadGeneral();

        }

        /*------------------------- Product ------------------*/

        /*------------------------- Categories ------------------*/

        /*------------------------- Manufacturer ------------------*/

        /*------------------------- Information ------------------*/


        if (isset($this->request->post['custom_url_store_status'])) {
            $this->data['custom_url_store_status'] = $this->request->post['custom_url_store_status'];
        } else {
            $this->data['custom_url_store_status'] = $this->config->get('custom_url_store_status');
        }
        
        $this->data['filter_keyword'] = $filter_keyword;

        $this->multiPagination('pagination_product',$this->config_admin_limit, $this->product_total, $this->page_product, '&tab=tab_products');
/*        $this->multiPagination('pagination_general',$this->config_admin_limit, $custom_url_total, $page_general, '&tab=tab_general');
        $this->multiPagination('pagination_category',$this->config_admin_limit, $category_total, $page_category, '&tab=tab_categories');
        $this->multiPagination('pagination_manufacturer',$this->config_admin_limit, $manufacturer_total, $page_manufacturer, '&tab=tab_manufacturers');
        $this->multiPagination('pagination_information',$this->config_admin_limit, $information_total, $page_information, '&tab=tab_information_pages');
*/
        $this->template = 'catalog/seo_customize.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }
    
    public function loadGeneralAuto(){
    
        $this->load->model('catalog/seo');
        $this->load->model('localisation/language');
        
        function seo_map($file){
                                
                                $ignore_list = array(
                                    'common/column_left', 'common/column_right', 'common/content_bottom', 'common/content_top', 
                                    'common/footer', 'common/header', 'common/landing', 'common/maintenance', 
                                    'common/seo_content', 'common/seo_url', 'checkout/confirm', 'checkout/guest', 
                                    'checkout/guest_shipping', 'checkout/login', 'checkout/manual', 'checkout/payment_address', 
                                    'checkout/payment_method', 'checkout/register', 'checkout/shipping_address', 'checkout/shipping_method',
                                     'product/product', 'product/category','information/information',
                                );

                                if( !preg_match('/^(module|feed|payment)\/.+$/', $file) && !in_array($file, $ignore_list) ) {
                                    return $file;
                                } else {
                                    return '';
                                }
                        }
                        
          function file_map($file){
                                return basename(dirname($file)). '/' . basename($file, '.php');
                            }
                        
        $languages = $this->model_localisation_language->getLanguages();
        $general_urls = array_filter(array_map('file_map', glob(DIR_CATALOG . 'controller/*/*.php')), 'seo_map');

        $this->model_catalog_seo->clearGeneral();

        foreach($general_urls as $general_url){

            $object = new SeoGeneral($this->registry);
       
            if(dirname($general_url)== "affiliate"){
                $object->setSeoKeyword("affliate-".basename($general_url));
            } elseif ($general_url=="checkout/success") {
                $object->setSeoKeyword("checkout-".basename($general_url));
            } else {
                $object->setSeoKeyword(basename($general_url));
            }
            

            $object->setQuery('route='.$general_url);

            $title = $meta_description = $meta_keywords = array();
            
            foreach($languages as $language) {
                if($language['status']){
                    $heading_title = '';
                    $language_file_path = DIR_CATALOG.'language/'.$language['directory'].'/'.$general_url.'.php';
                    if(file_exists($language_file_path)){
                        $_ = array();
                        $language_data = require_once($language_file_path);
                        $heading_title = isset($_['heading_title'])?$_['heading_title']:'';
                    }
                    $title[$language['language_id']]            = $heading_title;
                    $meta_keywords[$language['language_id']]    = $heading_title;
                    $meta_description[$language['language_id']] = $heading_title;
                }
            }
            
            $object->setTitle($title);
            $object->setId($general_url);
            $object->setMetaKeywords($meta_keywords);
            $object->setMetaDescription($meta_description);
            $object->save();
            
        }
        
        $this->redirect($this->url->link('catalog/seo/customize','token=' . $this->session->data['token'].'&tab=tab_general', 'SSL'));
        
    }

    public function loadGeneral() {
    

        $page_general = 1;

        if(isset($tab) && $tab=='tab_general'){
            $page_general = $page;
        }

        $data = array(
            'start'           => ($page_general - 1) * $this->config_admin_limit,
            'limit'           => $this->config_admin_limit
        );

        $data = array();

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['tab']=='tab_general') {

            $this->data['custom_url_store_data'] = array();

            if(isset($this->request->post['custom_url_store'])){
                foreach ($this->request->post['custom_url_store'] as $index => $info) {
                    $keyword_array = array(
                        'query'     =>  'route='.$info['id']['query'],
                        'keyword'   =>  $info['id']['keyword']
                    );
                    $keyword_query[] = $keyword_array;

                    foreach($info['custom_url_store_description'] as $key => $value) {
                        $description_array = $info['custom_url_store_description'];
                        $custom_url_store_description[] = $description_array;
                    }
                }

                $count = isset($keyword_query) ? COUNT($keyword_query) : 0;

                $custom_url_store_data = array();

                for($i=0; $i<$count; $i++){
                    $custom_url_store_data[$i]['keyword_query'] = $keyword_query[$i];
                    $custom_url_store_data[$i]['custom_url_store_description'] = $custom_url_store_description[$i];
                }

                $custom_url_total = isset($custom_url_store_data) ? COUNT($custom_url_store_data) : 0;

                if($data){
                    $custom_url_store_data = array_slice($custom_url_store_data, $data['start'] , $data['limit']);
                }

                $this->data['custom_url_store_data'] = $custom_url_store_data;
            }

        } else {

            $custom_url_data = Seo::findGeneral($this->registry);
            $custom_url_total = Seo::findGeneralTotal($this->registry);

            foreach($custom_url_data as $object) {

                $custom_url_store_description = array();

                $titles = $object->getTitle();
                $meta_keywords = $object->getMetaKeywords();
                $meta_description = $object->getMetaDescription();

                foreach($this->languages as $language) {
                    $custom_url_store_description[$language['language_id']] = array(
                        'name' => array_key_exists($language['language_id'], $titles) ? $titles[$language['language_id']]:'',
                        'meta_keywords' => array_key_exists($language['language_id'], $meta_keywords) ? $meta_keywords[$language['language_id']]:'',
                        'meta_description' => array_key_exists($language['language_id'], $meta_description) ? $meta_description[$language['language_id']]:''
                    );
                }

                $this->data['custom_url_store_data'][$object->getUrlAliasId()] = array(
                    'keyword_query'         => array('query' => $object->getQuery(), 'keyword' => $object->getSeoKeyword()),
                    'custom_url_store_description'  =>  $custom_url_store_description
                );
            }

        }
    }


    public function loadProducts($keyword = '') {

        $this->load->model('catalog/product');

        $this->page_product = 1;

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->page_product = $page;

        $data = array(
            'start'           => ($this->page_product - 1) * $this->config_admin_limit,
            'limit'           => $this->config_admin_limit,
            'filter_keyword'  => $keyword
        );

        $products = Seo::findProducts($this->registry, $data);
        $this->product_total = Seo::findProductsTotal($this->registry, $data);

        foreach ($products as $product) {

            $product_image = $product->getImage();

            if ($product_image && file_exists(DIR_IMAGE . $product_image)) {
                $image = $this->model_tool_image->resize($product_image, 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
            }

            $current_tab = (isset($this->request->post['tab']) && $this->request->post['tab']=='tab_products') ? 1 : 0;

            if(isset($this->request->post['product']['product_description']) && $current_tab){
                $product_description = $this->request->post['product']['product_description'][$product->getId()];
            }else{
                $titles = $product->getTitle();
                $meta_keywords = $product->getMetaKeywords();
                $meta_description = $product->getMetaDescription();

                $product_description = array();

                foreach($this->languages as $language) {
                    if(array_key_exists($language['language_id'], $titles)) {
                        $product_description[$language['language_id']] = array(
                            'title' => $titles[$language['language_id']],
                            'meta_keywords' => $meta_keywords[$language['language_id']],
                            'meta_description' => $meta_description[$language['language_id']]
                        );
                    } else {
                        $product_description[$language['language_id']] = array(
                            'title' => '',
                            'meta_keywords' => '',
                            'meta_description' => ''
                        );
                    }
                }
            }

            if(isset($this->request->post['product']['product_id']) && $current_tab){
                $keyword = $this->request->post['product']['product_id'][$product->getId()];
            }else{
                $keyword = $product->getSeoKeyword();
            }

            $this->data['products'][] = array(
                'product_id' => $product->getId(),
                'name'       => $product->getName(),
                'model'      => $product->getModel(),
                'keyword'    => $keyword,
                'product_description'   => $product_description,
                'image'      => $image
            );
        }
    }


    public function loadCategories() {

        $this->load->model('catalog/category');

        $page_category = 1;

        if(isset($tab) && $tab=='tab_categories'){
            $page_category = $page;
        }

        $data = array(
            'start'           => ($page_category - 1) * $this->config_admin_limit,
            'limit'           => $this->config_admin_limit
        );

        $data = array();

        $categories = Seo::findCategories($this->registry);
        $category_total = Seo::findCategoriesTotal($this->registry);

        foreach ($categories as $category) {

            $current_tab = (isset($this->request->post['tab']) && $this->request->post['tab']=='tab_categories') ? 1 : 0;

            if(isset($this->request->post['category']['category_description']) && $current_tab){
                $category_description = $this->request->post['category']['category_description'][$category->getId()];
            } else {

                $titles = $category->getTitle();
                $meta_keywords = $category->getMetaKeywords();
                $meta_description = $category->getMetaDescription();

                $category_description = array();

                foreach($this->languages as $language) {
                    if(array_key_exists($language['language_id'], $titles)) {
                        $category_description[$language['language_id']] = array(
                            'title' => $titles[$language['language_id']],
                            'meta_keywords' => $meta_keywords[$language['language_id']],
                            'meta_description' => $meta_description[$language['language_id']]
                        );
                    } else {
                        $category_description[$language['language_id']] = array(
                            'title' => '',
                            'meta_keywords' => '',
                            'meta_description' => ''
                        );
                    }
                }
            }

            if(isset($this->request->post['category']['category_id']) && $current_tab){
                $keyword = $this->request->post['category']['category_id'][$category->getId()];
            }else{
                $keyword = $category->getSeoKeyword();
            }

            $this->data['categories'][] = array(
                'category_id' => $category->getId(),
                'name'       => $category->getName(),
                'keyword'    => $keyword,
                'category_description'  => $category_description
            );
        }

    }


    public function loadManufactures() {

        $this->load->model('catalog/manufacturer');

        $page_manufacturer = 1;

        if(isset($tab) && $tab=='tab_manufacturers'){
            $page_manufacturer = $page;
        }

        $data = array(
            'start'           => ($page_manufacturer - 1) * $this->config_admin_limit,
            'limit'           => $this->config_admin_limit
        );

        $manufacturers = Seo::findManufacturers($this->registry);
        $manufacturer_total = Seo::findManufacturersTotal($this->registry);

        foreach($manufacturers as $manufacturer){

            $current_tab = (isset($this->request->post['tab']) && $this->request->post['tab']=='tab_manufacturers') ? 1 : 0;

            if(isset($this->request->post['manufacturer']['manufacturer_description']) && $current_tab){
                $manufacturer_description = $this->request->post['manufacturer']['manufacturer_description'][$manufacturer->getId()];
            }else{

                $titles = $manufacturer->getTitle();
                $meta_keywords = $manufacturer->getMetaKeywords();
                $meta_description = $manufacturer->getMetaDescription();

                $manufacturer_description = array();

                foreach($this->languages as $language) {
                    $manufacturer_description[$language['language_id']] = array(
                        'title' => array_key_exists($language['language_id'], $titles) ? ($titles[$language['language_id']]):'',
                        'meta_keywords' => array_key_exists($language['language_id'], $meta_keywords) ? $meta_keywords[$language['language_id']] : '' ,
                        'meta_description' => array_key_exists($language['language_id'], $meta_description) ? $meta_description[$language['language_id']] : ''
                    );
                }

            }

            if(isset($this->request->post['manufacturer']['manufacturer_id']) && $current_tab){
                $keyword = $this->request->post['manufacturer']['manufacturer_id'][$manufacturer->getId()];
            }else{
                $keyword = $manufacturer->getSeoKeyword();
            }

            $this->data['manufacturers'][] = array(
                'manufacturer_id' => $manufacturer->getId(),
                'name'       => $manufacturer->getName(),
                'keyword'    => $keyword,
                'manufacturer_description'  => $manufacturer_description
            );
        }
    }


    public function loadInformationPages() {

        $this->load->model('catalog/information');

        $page_information = 1;

        if(isset($tab) && $tab=='tab_information_pages'){
            $page_information = $page;
        }

        $data = array(
            'start'           => ($page_information - 1) * $this->config_admin_limit,
            'limit'           => $this->config_admin_limit
        );

        $data = array();

        $informations = Seo::findInformations($this->registry);
        $information_total = Seo::findInformationsTotal($this->registry);

        foreach($informations as $information){

            $current_tab = (isset($this->request->post['tab']) && $this->request->post['tab']=='tab_information_pages') ? 1 : 0;

            if(isset($this->request->post['information']['information_description']) && $current_tab){
                $information_description = $this->request->post['information']['information_description'][$information->getId()];
            }else{
                $titles = $information->getTitle();
                $meta_keywords = $information->getMetaKeywords();
                $meta_description = $information->getMetaDescription();

                $information_description = array();

                foreach($this->languages as $language) {
                    if(array_key_exists($language['language_id'], $titles)) {
                        $information_description[$language['language_id']] = array(
                            'title' => $titles[$language['language_id']],
                            'meta_keywords' => $meta_keywords[$language['language_id']],
                            'meta_description' => $meta_description[$language['language_id']]
                        );
                    } else {
                        $information_description[$language['language_id']] = array(
                            'title' => '',
                            'meta_keywords' => '',
                            'meta_description' => ''
                        );
                    }
                }
            }

            if(isset($this->request->post['information']['information_id']) && $current_tab){
                $keyword = $this->request->post['information']['information_id'][$information->getId()];
            }else{
                $keyword = $information->getSeoKeyword();
            }

            $this->data['informations'][] = array(
                'name'  =>  $information->getName(),
                'information_id' => $information->getId(),
                'keyword'    => $keyword,
                'information_description'   => $information_description
            );
        }
    }


    public function autogenerate() {
        $this->load->model('catalog/seo');
        $this->model_catalog_seo->createTablesInDatabse();
        $this->load->language('catalog/seo_autogenerate');
        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateAutogenerate())) {
            // Remember that KEY of $pattern array has to be the actual column name of xstore_seo_pattern table

            $dynamic_success = '';

            /*  PRODUCTS  */
            if (isset($this->request->post['products_url'])) {
                $pattern = array(
                    'product_url_keyword'       =>  $this->request->post['products_url_template']
                );
                $this->model_catalog_seo->generateProductUrlKeyword($this->request->post['products_url_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('product_url_keyword');
            }
            if(isset($this->request->post['products_title'])){
                $pattern = array(
                    'product_title'       =>  $this->request->post['products_title_template']
                );
                $this->model_catalog_seo->generateProductTitle($this->request->post['products_title_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('product_title');
            }
            if (isset($this->request->post['product_keywords'])) {
                $pattern = array(
                    'product_meta_keywords' =>  $this->request->post['product_keywords_template']
                );
                if (!isset($this->request->post['yahoo_checkbox'])) {
                    $this->model_catalog_seo->generateProductMetaKeywords($this->request->post['product_keywords_template'], null, $this->request->post['source_language_code'],$pattern);
                } else if (trim($this->request->post['yahoo_id']) != '') {
                    $this->model_catalog_seo->generateProductMetaKeywords($this->request->post['product_keywords_template'], trim($this->request->post['yahoo_id']), $this->request->post['source_language_code'],$pattern);
                } else {
                    $this->error['warning'] = $this->language->get('enter_yahoo_id');
                }
                $dynamic_success = $this->language->get('product_meta_keywords');
            }
            if (isset($this->request->post['product_description'])) {
                $pattern = array(
                    'product_meta_description'  =>  $this->request->post['product_description_template']
                );
                $this->model_catalog_seo->generateProductMetaDescription($this->request->post['product_description_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('product_meta_description');
            }
            if (isset($this->request->post['product_tags'])) {
                $pattern = array(
                    'product_tags'  =>  $this->request->post['product_tags_template']
                );
                $this->model_catalog_seo->generateProductTags($this->request->post['product_tags_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('product_tags');
            }
            if (isset($this->request->post['product_image'])) {
                $pattern = array(
                    'product_image_name'    =>  $this->request->post['product_image_template']
                );
                $this->model_catalog_seo->generateProductImage($this->request->post['product_image_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('product_image_name');
            }

            /*  CATEGORIES  */
            if (isset($this->request->post['categories_url'])) {
                $pattern = array(
                    'category_url_keyword'  =>  $this->request->post['categories_url_template']
                );
                $this->model_catalog_seo->generateCategoryUrlKeyword($this->request->post['categories_url_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('category_url_keyword');
            }

            if (isset($this->request->post['categories_title'])) {
                $pattern = array(
                    'category_title' =>  $this->request->post['categories_title_template']
                );
                $this->model_catalog_seo->generateCategoryTitle($this->request->post['categories_title_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('category_title');
            }

            if (isset($this->request->post['categories_keyword'])) {
                $pattern = array(
                    'category_keyword' =>  $this->request->post['categories_keyword_template']
                );
                $this->model_catalog_seo->generateCategoryMetaKeywords($this->request->post['categories_keyword_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('category_meta_keywords');
            }

            if (isset($this->request->post['category_description'])) {
                $pattern = array(
                    'category_meta_description' =>  $this->request->post['category_description_template']
                );
                $this->model_catalog_seo->generateCategoryMetaDescription($this->request->post['category_description_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('category_meta_description');
            }

            /*  MANUFACTURERS  */
            if (isset($this->request->post['manufacturers_url'])) {
                $pattern = array(
                    'manufacturer_url_keyword'  =>  $this->request->post['manufacturers_url_template']
                );
                $this->model_catalog_seo->generateManufacturerUrlKeyword($this->request->post['manufacturers_url_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('manufacturer_url_keyword');
            }

            /*  INFORMATION PAGES  */
            if (isset($this->request->post['information_pages'])) {
                $pattern = array(
                    'information_page_url_keyword'  =>  $this->request->post['information_pages_template']
                );
                $this->model_catalog_seo->generateInformationPageUrlKeyword($this->request->post['information_pages_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('information_page_url_keyword');
            }

            if (isset($this->request->post['information_pages_title'])) {
                $pattern = array(
                    'information_pages_title'  =>  $this->request->post['information_pages_title_template']
                );
                $this->model_catalog_seo->generateInformationPageTitle($this->request->post['information_pages_title_template'], $this->request->post['source_language_code'],$pattern);
                $dynamic_success = $this->language->get('information_page_title');
            }

            if (isset($this->error['warning'])) {
                $this->data['error_warning'] = $this->error['warning'];
            } else {
                $this->data['success'] = $dynamic_success . ' ' . $this->language->get('text_success');
            }

        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => HTTPS_SERVER . 'index.php?route=catalog/seo/autogenerate&token=' . $this->session->data['token'],
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
            );

            $this->data['warning_clear'] = $this->language->get('warning_clear');
            $this->data['warning_clear_tags'] = $this->language->get('warning_clear_tags');

            $this->data['tags'] = $this->language->get('tags');
            $this->data['generate'] = $this->language->get('generate');
            $this->data['template'] = $this->language->get('template');

            $this->data['available_category_tags'] = $this->language->get('available_category_tags');
            $this->data['available_information_pages_tags'] = $this->language->get('available_information_pages_tags');

            $this->data['source_language'] = $this->language->get('source_language');
            $this->data['autogenerate_help'] = $this->language->get('autogenerate_help');
            $this->data['add_from_yahoo'] = $this->language->get('add_from_yahoo');
            $this->data['your_yahoo_id'] = $this->language->get('your_yahoo_id');
            $this->data['get_yahoo_id'] = $this->language->get('get_yahoo_id');
            $this->data['curl_not_enabled'] = $this->language->get('curl_not_enabled');

            $this->data['text_entity'] = $this->language->get('text_entity');
            $this->data['text_description'] = $this->language->get('text_description');
            $this->data['text_pattern'] = $this->language->get('text_pattern');
            $this->data['text_action'] = $this->language->get('text_action');

            $this->data['text_products'] = $this->language->get('text_products');
            $this->data['text_categories'] = $this->language->get('text_categories');
            $this->data['text_manufacturers'] = $this->language->get('text_manufacturers');
            $this->data['text_information_pages'] = $this->language->get('text_information_pages');
            $this->data['text_url_keyword'] = $this->language->get('text_url_keyword');
            $this->data['text_title'] = $this->language->get('text_title');
            $this->data['text_meta_keywords'] = $this->language->get('text_meta_keywords');
            $this->data['text_meta_description'] = $this->language->get('text_meta_description');
            $this->data['text_tags'] = $this->language->get('text_tags');
            $this->data['text_image_name'] = $this->language->get('text_image_name');

            $this->data['button_cancel'] = $this->language->get('button_cancel');

            if($this->request->server['REQUEST_METHOD'] != 'POST'){
                $this->load->model('catalog/seo');
                $pattern = $this->model_catalog_seo->getSeoPattern();
            }

            $user_defined_text = $this->language->get('user_defined_text');
            $user_defined_text_meta = $this->language->get('user_defined_text_meta');

            // Products

            if (isset($this->request->post['products_url_template'])) {
                $this->data['products_url_template'] = $this->request->post['products_url_template'];
            } elseif(isset($pattern['product_url_keyword']) && $pattern['product_url_keyword']) {
                $this->data['products_url_template'] = $pattern['product_url_keyword'];
            } else{
                $this->data['products_url_template'] = '[product_name], [model_name], [manufacturer_name]';
            }
            $pattern_available = '[product_name], [model_name], [manufacturer_name], [product_price]';
            $this->data['help_product_seo_description'] = sprintf($this->language->get('dymanic_seo_description'), 'products', $pattern_available, $user_defined_text);

            if (isset($this->request->post['products_title_template'])) {
                $this->data['products_title_template'] = $this->request->post['products_title_template'];
            } elseif(isset($pattern['product_title']) && $pattern['product_title']) {
                $this->data['products_title_template'] = $pattern['product_title'];
            } else{
                $this->data['products_title_template'] = '[product_name], [product_description]';
            }
            $pattern_available = '[product_name], [product_description], [product_price], [model_name], [manufacturer_name]';
            $this->data['help_product_title'] = sprintf($this->language->get('dymanic_title'), 'products', $pattern_available, $user_defined_text);

            if (isset($this->request->post['product_keywords_template'])) {
                $this->data['product_keywords_template'] = $this->request->post['product_keywords_template'];
            } elseif(isset($pattern['product_meta_keywords']) && $pattern['product_meta_keywords']) {
                $this->data['product_keywords_template'] = $pattern['product_meta_keywords'];
            } else{
                $this->data['product_keywords_template'] = '[product_name], [model_name], [manufacturer_name], [categories_names]';
            }
            $pattern_available = '[product_name], [model_name], [manufacturer_name], [categories_names], [product_price]';
            $this->data['help_product_keywords_description'] = sprintf($this->language->get('dymanic_keywords_description'), 'products', $pattern_available, $user_defined_text_meta);
            $this->data['note_product_meta_description'] = sprintf($this->language->get('note_meta_description'), 'product');

            $this->data['yahoo_checkbox'] = isset($this->request->post['yahoo_checkbox']) ? 1 : 0;
            if (isset($this->request->post['yahoo_id'])) {
                $this->data['yahoo_id'] = $this->request->post['yahoo_id'];
            } elseif(isset($pattern['yahoo_id']) && $pattern['yahoo_id']) {
                $this->data['yahoo_id'] = $pattern['yahoo_id'];
            } else {
                $this->data['yahoo_id'] = '';
            }

            if (isset($this->request->post['product_description_template'])) {
                $this->data['product_description_template'] = $this->request->post['product_description_template'];
            } elseif(isset($pattern['product_meta_description']) && $pattern['product_meta_description']) {
                $this->data['product_description_template'] = $pattern['product_meta_description'];
            } else{
                $this->data['product_description_template'] = '[product_name], [product_description]';
            }
            $pattern_available = '[product_name], [product_description], [product_price]';
            $this->data['help_product_description'] = sprintf($this->language->get('dymanic_description'), 'products', $pattern_available, $user_defined_text_meta);

            if (isset($this->request->post['product_tags_template'])) {
                $this->data['product_tags_template'] = $this->request->post['product_tags_template'];
            } elseif(isset($pattern['product_tags']) && $pattern['product_tags']) {
                $this->data['product_tags_template'] = $pattern['product_tags'];
            } else{
                $this->data['product_tags_template'] = '[product_name], [model_name], [manufacturer_name], [categories_names]';
            }
            $pattern_available = '[product_name], [model_name], [manufacturer_name], [categories_names]';
            $this->data['help_product_tags'] = sprintf($this->language->get('dymanic_tags'), 'products', $pattern_available, $user_defined_text);

            if (isset($this->request->post['product_image_template'])) {
                $this->data['product_image_template'] = $this->request->post['product_image_template'];
            } elseif(isset($pattern['product_image_name']) && $pattern['product_image_name']) {
                $this->data['product_image_template'] = $pattern['product_image_name'];
            } else{
                $this->data['product_image_template'] = '[product_name]';
            }
            $pattern_available = '[product_name]';
            $this->data['help_product_image_description'] = sprintf($this->language->get('dymanic_image_description'), 'products', $pattern_available, $user_defined_text);

            // Categories

            if (isset($this->request->post['categories_url_template'])) {
                $this->data['categories_url_template'] = $this->request->post['categories_url_template'];
            } elseif(isset($pattern['category_url_keyword']) && $pattern['category_url_keyword']) {
                $this->data['categories_url_template'] = $pattern['category_url_keyword'];
            } else{
                $this->data['categories_url_template'] = '[category_name]';
            }
            $pattern_available = '[category_name]';
            $this->data['help_category_seo_description'] = sprintf($this->language->get('dymanic_seo_description'), 'categories', $pattern_available, $user_defined_text);

            if (isset($this->request->post['categories_title_template'])) {
                $this->data['categories_title_template'] = $this->request->post['categories_title_template'];
            } elseif(isset($pattern['category_title']) && $pattern['category_title']) {
                $this->data['categories_title_template'] = $pattern['category_title'];
            } else{
                $this->data['categories_title_template'] = '[category_name], [category_description]';
            }
            $pattern_available = '[category_name], [category_description]';
            $this->data['help_category_title'] = sprintf($this->language->get('dymanic_title'), 'categories', $pattern_available, $user_defined_text);

            if (isset($this->request->post['categories_keyword_template'])) {
                $this->data['categories_keyword_template'] = $this->request->post['categories_keyword_template'];
            } elseif(isset($pattern['category_keyword']) && $pattern['category_keyword']) {
                $this->data['categories_keyword_template'] = $pattern['category_keyword'];
            } else{
                $this->data['categories_keyword_template'] = '[category_name], [category_description]';
            }
            $pattern_available = '[category_name], [category_description]';
            $this->data['help_category_meta_keyword'] = sprintf($this->language->get('dymanic_keywords_description'), 'categories', $pattern_available, $user_defined_text);

            if (isset($this->request->post['category_description_template'])) {
                $this->data['category_description_template'] = $this->request->post['category_description_template'];
            } elseif(isset($pattern['category_meta_description']) && $pattern['category_meta_description']) {
                $this->data['category_description_template'] = $pattern['category_meta_description'];
            } else{
                $this->data['category_description_template'] = '[category_name], [category_description]';
            }
            $pattern_available = '[category_name], [category_description]';
            $this->data['help_category_description'] = sprintf($this->language->get('dymanic_description'), 'categories', $pattern_available, $user_defined_text_meta);
            $this->data['note_category_meta_description'] = sprintf($this->language->get('note_meta_description'), 'category');

            //Manufacturers

            if (isset($this->request->post['manufacturers_url_template'])) {
                $this->data['manufacturers_url_template'] = $this->request->post['manufacturers_url_template'];
            } elseif(isset($pattern['manufacturer_url_keyword']) && $pattern['manufacturer_url_keyword']) {
                $this->data['manufacturers_url_template'] = $pattern['manufacturer_url_keyword'];
            } else{
                $this->data['manufacturers_url_template'] = '[manufacturer_name]';
            }
            $pattern_available = '[manufacturer_name]';
            $this->data['help_manufacturer_seo_description'] = sprintf($this->language->get('dymanic_seo_description'), 'manufacturers', $pattern_available, $user_defined_text);

            // Information Pages

            if (isset($this->request->post['information_pages_template'])) {
                $this->data['information_pages_template'] = $this->request->post['information_pages_template'];
            } elseif(isset($pattern['information_page_url_keyword']) && $pattern['information_page_url_keyword']) {
                $this->data['information_pages_template'] = $pattern['information_page_url_keyword'];
            }  else{
                $this->data['information_pages_template'] = '[information_page_title]';
            }
            
            $pattern_available = '[information_page_title]';
            $this->data['help_information_seo_description'] = sprintf($this->language->get('dymanic_seo_description'), 'information pages', $pattern_available, $user_defined_text);

            if (isset($this->request->post['information_pages_title_template'])) {
                $this->data['information_pages_title_template'] = $this->request->post['information_pages_title_template'];
            } elseif(isset($pattern['information_pages_title']) && $pattern['information_pages_title']) {
                $this->data['information_pages_title_template'] = $pattern['information_pages_title'];
            }  else{
                $this->data['information_pages_title_template'] = '[information_page_title], [information_page_description]';
            }
            
            $pattern_available = '[information_page_title], [information_page_description]';
            $this->data['help_information_title'] = sprintf($this->language->get('dymanic_title'), 'information pages', $pattern_available, $user_defined_text);
            
            if(isset($this->request->post['source_language_code'])){
                $this->data['source_language_code'] = (int)$this->request->post['source_language_code'];
            } else {
                $this->data['source_language_code'] = $this->config->get('config_language_id');            
            }

            $this->load->model('localisation/language');

            $this->data['languages'] = $this->model_localisation_language->getLanguages();

            $this->data['action'] = HTTPS_SERVER . 'index.php?route=catalog/seo/autogenerate&token=' . $this->session->data['token'];
            $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'];
            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->template = 'catalog/seo_autogenerate.tpl';
            $this->children = array(
            'common/header',
            'common/footer'
            );

            $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    protected function multiPagination($pagination, $config_admin_limit, $product_total, $page, $url){
        $temp = $pagination;
        $pagination = $pagination;
        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $config_admin_limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = HTTPS_SERVER . 'index.php?route=catalog/seo/customize&token=' . $this->session->data['token'] . $url . '&page={page}';

        $this->data[$temp] = $pagination->render();
    }

    protected function validate($data) {

        if (!$this->user->hasPermission('modify', 'catalog/seo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $members = array('custom_url_store','product','category','manufacturer','information');
        $mapping_tab = array(
            'custom_url_store' => 'General',
            'product' => 'Products',
            'category' => 'Categories',
            'manufacturer' => 'Manufacturers',
            'information' => 'Information Pages');

        foreach($members as $member){
            if(isset($data[$member]) && $member != 'custom_url_store'){
                if(isset($data[$member][$member.'_description'])){
                    foreach($data[$member][$member.'_description'] as $id => $language){
                        foreach ($language as $language_id => $value) {
                            $name = 'name';
                            if($member=='information'){
                                $name = 'title';
                            }
                            if(isset($value[$name])){
                                if ((strlen(utf8_decode($value[$name])) < 3)) {
                                    // $this->error['title']['name_'.$member.'_'.$id.'_'.$language_id] = $this->language->get('error_name');
                                }
                            }
                        }
                    }
                }
                if(isset($data[$member][$member.'_id'])){
                    $keyword_validate = $this->validate->keyword_validate($data[$member][$member.'_id'],$member.'_id');
                    if($keyword_validate){
                        if($keyword_validate['existing_keyword']){
                            $this->data['existing_keyword'] = $keyword_validate['existing_keyword'];
                            $this->error['already_exists'] = sprintf($this->language->get($keyword_validate['error']),$this->data['existing_keyword'],$mapping_tab[$member]);
                        }
                        $this->data['tab'] = $data['tab'];
                    }
                }
            }elseif(isset($data[$member]) && $member == 'custom_url_store'){
                $new_member_id = array();
                if($data[$member]){
                    foreach ($data[$member] as $key => $value) {
                        if($value['id']['query']) {
                            if(trim($value['id']['query']) == 'product/category'){
                                $this->error['warning'] = $this->language->get('error_restrict');
                            } elseif(trim($value['id']['keyword']) == '') {
                                $this->error['warning'] = $this->language->get('error_empty');
                            }
                            $new_member_id[] = array(
                                'query' => $value['id']['query'],
                                'keyword' => $value['id']['keyword']
                            );
                        }
                    }
                    if($new_member_id) {
                        $keyword_validate = $this->validate->keyword_validate($new_member_id,$member);
                        if($keyword_validate){
                            if($keyword_validate['existing_keyword']){
                                $this->data['existing_keyword'] = $keyword_validate['existing_keyword'];
                                $this->error['already_exists'] = sprintf($this->language->get($keyword_validate['error']),$this->data['existing_keyword'],$mapping_tab[$member]);
                            }
                            $this->data['tab'] = $data['tab'];
                        }
                    }
                }
            }
        }
        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function validateAutogenerate() {
        if (!$this->user->hasPermission('modify', 'catalog/seo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
?>
