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

namespace Choreo\Translation\Model\Config\Source;

class Languageto implements \Magento\Framework\Option\ArrayInterface
{
    protected $_helper;
    public function __construct(
    	\Choreo\Translation\Helper\Data $helper,
		\Magento\Framework\Locale\Resolver $store,
		\Magento\Framework\App\RequestInterface $request
    ) {
        $this->_helper = $helper;
		$this->_store = $store;
		$this->request = $request;
    }
    
	public function toOptionArray()
    {
		$source_countries_array[0]['label'] = 'Please Select Language';
    	$source_countries_array[0]['value'] = '';
		return $source_countries_array;
		/* $post = $this->request->getParams();
		if(isset($post['store'])){
		    $store_code = $post['store'];
		}else{
			$store_code = 0;
		}
		$currentStore = $this->_store->getLocale();
		$cur_lang = strstr($currentStore, '_', true);
        $sourceCode = $this->_helper->getConfig('choreotranslation/enable/languagefrom',$store_code);
		if($sourceCode == ''){
		    $currentStore = $this->_store->getLocale();
		    $sourceCode = strstr($currentStore, '_', true);
		}
		$source_countries_array = [];
		$source_countries_response = $this->_helper->getlanguageslists();
    	$only_translate_lang = array();
		if(isset($source_countries_response['code_to_name'])){
		    $code_to_name = $source_countries_response['code_to_name'];
		    if(isset($source_countries_response['source_to_target'])){
		        $source_to_target = $source_countries_response['source_to_target'];
				$target_array = $source_to_target[$sourceCode];
		    	foreach($target_array as $sourcelangcode => $value){
					$only_translate_lang[$sourcelangcode] = $code_to_name[$sourcelangcode];
		    	}
		    }
		}
		if(!empty($only_translate_lang)){
    	    $i = 1;
    		$source_countries = $source_countries_response['code_to_name'];
			$source_countries_array[0]['label'] = 'Please Select Language';
    	    $source_countries_array[0]['value'] = '';
    	    foreach($only_translate_lang as $country_code => $country_name ){
    	        $source_countries_array[$i]['label'] = $country_name;
    	        $source_countries_array[$i]['value'] = $country_code;
    	    	$i++;
    	    }
    	}else{
		    $source_countries_array[0]['label'] = 'Current locale';
    	    $source_countries_array[0]['value'] = '';
		}
		return $source_countries_array; */
    }
}