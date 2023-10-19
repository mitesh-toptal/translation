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

namespace Choreo\Translation\Model;

class Editformlan implements \Magento\Framework\Option\ArrayInterface
{
	public function __construct(
        \Choreo\Translation\Helper\Data $helper,
		\Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->_helper = $helper;
		$this->_localeResolver = $localeResolver;
    }
    public function toOptionArray()
    {
		$targetlists = $this->getTargetlist();
		$option = array();
		
		$option[0]['value'] = 0;
		$option[0]['label'] = "--Select--";

		$i = 1;
		foreach($targetlists as $lancode=>$lanname){
		    $option[$i]['value'] = $lancode;
		    $option[$i]['label'] = $lanname;
			$i++;
		}
		return $option;
    }

    public function toLangArray()
    {
		$targetlists = $this->getTargetlist();
		$option = array();		
		foreach($targetlists as $lancode=>$lanname){
		    $option[$lancode] = $lanname;		    			
		}
		return $option;
    }

    public function getLangeaugeName($code)
    {
		$targetlists = $this->getTargetlist();
		$option = array();		
		foreach($targetlists as $lancode=>$lanname){
			if($lancode == $code){
				return $lanname;
			}		    
		}
		return $code;
    }
	
    public function sourcetoOptionArray()
    {
		$targetlists = $this->getTargetlist();
		$option = array();
		$option[0]['value'] = 0;
		$option[0]['label'] = "--Select--";
		$i = 1;
		foreach($targetlists as $lancode=>$lanname){
		    if($lancode != 'en'){
			    $option[$i]['value'] = $lancode;
		        $option[$i]['label'] = $lanname;
			    $i++;
			}
		}
		return $option;
    }

    public function getMemorylist($sourceLang, $taregetLang)
    {
    	$option = array();		
    	if(!empty($sourceLang) || !empty($taregetLang)){
    		$responses = $this->_helper->getMemorylists();
	    	$i = 0;
			foreach($responses as $key=>$values){
	            $srclang = $values['srclang'];
	        	$trglang = $values['trglang'];
	        	if($srclang == $sourceLang && $trglang == $taregetLang){	        		
	        		$option[$i]['value'] = $values['id'];
			        $option[$i]['label'] = $values['id'];
				    $i++;        	    
	        	}
			}
    	}
    	return $option;    	
    }
	
	public function getTargetlist()
    {
		$languageto = $this->_helper->getConfig('ch_memories/enable/languageto',0);
	    $languagefrom = $this->_helper->getConfig('ch_memories/enable/languagefrom',0);
		$source_countries_response = $this->_helper->getlanguageslists();
		if($languagefrom == ''){
		    $currentStore = $this->_localeResolver->getLocale();
		    $cur_lang = strstr($currentStore, '_', true);
			$languagefrom = $cur_lang;
		}else{
			$cur_lang = '';
		}
		$targetlist = array();
		if(isset($source_countries_response['code_to_name'])){
		    $code_to_name = $source_countries_response['code_to_name'];
			if(isset($source_countries_response['source_to_target'])){
		        $source_to_target = $source_countries_response['source_to_target'];
		    	foreach($source_to_target as $sourcelangcode => $value){
		    	    $option_label = $code_to_name[$sourcelangcode];
					$targetlist[$sourcelangcode] = $option_label;
		    	}
		    }
		}
		return $targetlist;
    }
}