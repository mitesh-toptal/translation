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

class Languageform implements \Magento\Framework\Option\ArrayInterface
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
		$source_countries_array[0]['label'] = 'Please Select';
		$source_countries_array[0]['value'] = '';
    	return $source_countries_array;
		/* $post = $this->request->getParams();
		if(isset($post['store'])){
		    $store_code = $post['store'];
		}else{
			$store_code = 0;
		}
		$sourcelang = $this->_helper->getConfig('choreotranslation/enable/languagefrom',$store_code);
		$targetlang = $this->_helper->getConfig('choreotranslation/enable/languageto',$store_code);
		if($sourcelang == ''){
		    $currentStore = $this->_store->getLocale();
		    $cur_lang = strstr($currentStore, '_', true);
		}else{
			$cur_lang = '';
		}
		$source_countries_response = $this->_helper->getlanguageslists();
		$source_countries_array = [];
    	$only_translate_lang = array();
		if(isset($source_countries_response['code_to_name'])){
		    $code_to_name = $source_countries_response['code_to_name'];
		    if(isset($source_countries_response['source_to_target'])){
		        $source_to_target = $source_countries_response['source_to_target'];
		    	foreach($source_to_target as $sourcelangcode => $value){
		    	    if($cur_lang != '' && $cur_lang == $sourcelangcode){
						$cur_lang_name = $code_to_name[$sourcelangcode];
						$source_countries_array[0]['label'] = $cur_lang_name;
		                $source_countries_array[0]['value'] = $cur_lang;
					}else{
						$only_translate_lang[$sourcelangcode] = $code_to_name[$sourcelangcode];
					}
		    	}
		    }
		}
		//echo '<pre>'; print_r($only_translate_lang);die("testtt");
		if(!empty($only_translate_lang)){
    	    $i = 1;
    		$source_countries = $source_countries_response['code_to_name'];
    	    foreach($only_translate_lang as $country_code => $country_name ){
    	        $source_countries_array[$i]['label'] = $country_name;
    	        $source_countries_array[$i]['value'] = $country_code;
    	    	$i++;
    	    }
    	}else{
		    $source_countries_array[1]['value'] = 'af';
    	    $source_countries_array[1]['label'] = 'Afrikaans';
		    $source_countries_array[2]['value'] = 'sq';
    	    $source_countries_array[2]['label'] = 'Albanian';
		    $source_countries_array[3]['value'] = 'ar';
    	    $source_countries_array[3]['label'] = 'Arabic';
		    $source_countries_array[4]['value'] = 'az';
    	    $source_countries_array[4]['label'] = 'Azerbaijani';
		    $source_countries_array[5]['value'] = 'bn';
    	    $source_countries_array[5]['label'] = 'Bangla';
		    $source_countries_array[6]['value'] = 'eu';
    	    $source_countries_array[6]['label'] = 'Basque';
		}
    	return $source_countries_array; */
	}
}