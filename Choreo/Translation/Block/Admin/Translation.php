<?php
/*************************************************************************
 *
 * Choreo, Inc CONFIDENTIAL
 * __________________
 *
 *  2020 - Choreo, Inc Incorporated
 *  All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of Choreo, LLC.
 * The intellectual and technical concepts contained herein are
 * proprietary to Choreo, LLC and may be covered by
 * U.S. and Foreign Patents, patents in process, and are protected
 * by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Choreo, LLC.
 *
 *************************************************************************/

namespace Choreo\Translation\Block\Admin;
use Magento\Cms\Model\PageFactory;
class Translation extends \Magento\Backend\Block\Template
{
    protected $_localeResolver;
    protected $_helper;
    protected $_jsonHelper;
	protected $_requestInterface;	
	protected $scopeConfig; 	
	protected $_backendUrl;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
		\Choreo\Translation\Helper\Data $helper,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\App\RequestInterface $requestInterface ,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Store\Api\StoreRepositoryInterface $storeRepository,
		\Magento\Cms\Model\BlockRepository $staticBlockRepository,
		PageFactory $pageFactory,
        array $data = [],        
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,        
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        parent::__construct($context, $data);
        $this->_localeResolver = $localeResolver;
        $this->_helper = $helper;
        $this->_jsonHelper = $jsonHelper;
		$this->_requestInterface  = $requestInterface;
		$this->_coreRegistry = $registry;
		$this->request = $request;
		$this->storeRepository= $storeRepository;
		$this->staticBlockRepository = $staticBlockRepository;
		$this->pageFactory = $pageFactory;		
		$this->scopeConfig = $scopeConfig; 		
		$this->_backendUrl = $backendUrl;
    }
	public function getPageid(){
	    return $this->_requestInterface->getFullActionName();
	}	

	public function getProductCountUrl(){
		return $this->_backendUrl->getUrl("*/*/productcount");
	}

	public function getProductGridUrl(){
		return $this->_backendUrl->getUrl("*/*/productsgrid");
	}

	public function getConfigvalues(){
		$page_ref = $this->_requestInterface->getFullActionName();
		$config_array = array();
		$memories_json = '';
		$post = $this->getRequest()->getParams();
		
		$config_array['store_name'] = $this->scopeConfig->getValue('general/store_information/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if(isset($post['store'])){
		    $storecode = $post['store'];
		}else{
			$storecode = 0;
		}
		if($page_ref == 'cms_page_edit' || $page_ref == 'cms_block_edit' || $page_ref == 'catalog_category_edit' || $page_ref == 'catalog_product_edit' || $page_ref == 'adminhtml_system_config_edit' || $page_ref == 'choreo_translation_jobs_edit'){
			$languageto = $this->_helper->getConfig('ch_memories/enable/languageto',$storecode);
	        $languagefrom = $this->_helper->getConfig('ch_memories/enable/languagefrom',$storecode);
	        $memories_json = $this->_helper->getConfig('ch_memories/enable/memories_json',$storecode);
		    $source_countries_response = $this->_helper->getlanguageslists();
		    if($languagefrom == ''){
		        $currentStore = $this->_localeResolver->getLocale();
		        $cur_lang = strstr($currentStore, '_', true);
		    	$languagefrom = $cur_lang;
		    }else{
		    	$cur_lang = '';
		    }
		    if(isset($source_countries_response['code_to_name'])){
		        $code_to_name = $source_countries_response['code_to_name'];
		    	$languagetofullname = '';
		    	$languagefromfullname = '';
		    	if(array_key_exists($languageto, $code_to_name)){
		    	    $languagetofullname = $code_to_name[$languageto];
		    	}
		    	if(array_key_exists($languagefrom, $code_to_name)){
		    	    $languagefromfullname = $code_to_name[$languagefrom];
		    	}
		    	$languagefromoptionhtml = '';
		    	$languagetoptionhtml = '';
		        if(isset($source_countries_response['source_to_target'])){
		            $source_to_target = $source_countries_response['source_to_target'];
		        	foreach($source_to_target as $sourcelangcode => $value){
		        	    $option_label = $code_to_name[$sourcelangcode];
		    			$option_value = $sourcelangcode;
		    			if($cur_lang != '' && $cur_lang == $sourcelangcode){
		    				$languagefromoptionhtml .= '<option value="'.$option_value.'" selected>'.$option_label.'</option>';
		    			}else if($sourcelangcode == $languagefrom){
		    				$languagefromoptionhtml .= '<option value="'.$option_value.'" selected>'.$option_label.'</option>';
		    			}else{
		    				$languagefromoptionhtml .= '<option value="'.$option_value.'">'.$option_label.'</option>';
		    			}
		        	}
		    		$target_array = $source_to_target[$languagefrom];
		        	foreach($target_array as $sourcelangcode => $value){
		    			$to_option_label = $code_to_name[$sourcelangcode];
		    			$to_option_value = $sourcelangcode;
		    			if($sourcelangcode == $languageto){
		    				$languagetoptionhtml .= '<option value="'.$to_option_value.'" selected>'.$to_option_label.'</option>';
		    			}else{
		    			    $languagetoptionhtml .= '<option value="'.$to_option_value.'">'.$to_option_label.'</option>';
		    			}
		        	}
		        }
		    	$config_array['languagefromoptionhtml'] = $languagefromoptionhtml;
		    	$config_array['languagetoptionhtml'] = $languagetoptionhtml;
		    	$config_array['languageto'] = $this->_helper->getConfig('ch_memories/enable/languageto',$storecode);
	            $config_array['languagefrom'] = $languagefrom;
	            $config_array['languagetofullname'] = $languagetofullname;
	            $config_array['languagefromfullname'] = $languagefromfullname;
				$config_array['storecode'] = $storecode;
				$config_array['memories_json'] = $memories_json;
		    }
		    if($page_ref == 'adminhtml_system_config_edit' || $page_ref == 'choreo_translation_jobs_edit'){
			    $config_array['activation'] = 1;
				$config_array['translateURL'] = $this->getUrl('choreo_translation/index/targetlanguagelist');
				$config_array['creatememory'] = $this->getUrl('choreo_translation/index/memoryids');
			    $encodedData = $this->_jsonHelper->jsonEncode($config_array);
				return $encodedData;
		    }else if($page_ref == 'cms_page_edit'){
			    $id = $this->request->getParam('page_id');
			    if($id){
				    $page = $this->pageFactory->create()->load($id);
					$store_ids = $page->getStoreId();
					$current_store = $store_ids[0];
				    $storecode = $this->storeRepository->get($current_store)->getCode();
				}
				$config_array['activation'] = $this->_helper->getConfig('ch_memories/enable/activation',$storecode);
			    $config_array['languagefields'] = $this->_helper->getConfig('ch_attributes/attributes/cmspagefields',$storecode);
			}else if($page_ref == 'cms_block_edit'){			    
				$id = $this->request->getParam('block_id');
			    if($id){
				    $store_ids = $this->staticBlockRepository->getById($id)->getStoreId();
					$current_store = $store_ids[0];
					$storecode = $this->storeRepository->get($current_store)->getCode();
				}
				$config_array['activation'] = $this->_helper->getConfig('ch_memories/enable/activation',$storecode);
			    $config_array['languagefields'] = $this->_helper->getConfig('ch_attributes/attributes/cmspagefields',$storecode);
			}else if($page_ref == 'catalog_category_edit'){
			    $current_store = $this->request->getParam('store');
			    if($current_store){
				    $storecode = $this->storeRepository->get($current_store)->getCode();
				}
	            $config_array['activation'] = $this->_helper->getConfig('ch_memories/enable/activation',$storecode);
		        $config_array['languagefields'] = $this->_helper->getConfig('ch_attributes/attributes/categoryfields',$storecode);
			}else{
			    $current_store = $this->request->getParam('store');
			    if($current_store){
				    $storecode = $this->storeRepository->get($current_store)->getCode();
				}
	            $config_array['activation'] = $this->_helper->getConfig('ch_memories/enable/activation',$storecode);
		        $config_array['languagefields'] = $this->_helper->getConfig('ch_attributes/attributes/productattributes',$storecode);
			}
	        $config_array['translatebutton'] = $this->_helper->getConfig('ch_memories/enable/translatebutton',$storecode);
	        $config_array['enablestore'] = $this->_helper->getConfig('ch_memories/enable/enablestore',$storecode);
		    $config_array['translateURL'] = $this->getUrl('choreo_translation/index/index');
		    $config_array['pageid'] = $this->_requestInterface->getFullActionName();
		    $config_array['storecode'] = $storecode;
			
		    $encodedData = $this->_jsonHelper->jsonEncode($config_array);
		}else{
		    $config_array['activation'] = 0;
			$encodedData = $this->_jsonHelper->jsonEncode($config_array);
		}
        return $encodedData;
	}
}