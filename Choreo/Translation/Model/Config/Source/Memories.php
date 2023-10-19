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

class Memories implements \Magento\Framework\Option\ArrayInterface
{
    protected $_helper;
    public function __construct(
    	\Choreo\Translation\Helper\Data $helper,
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\App\RequestInterface $request
    ) {
        $this->_helper = $helper;
		$this->_curl = $curl;
		$this->jsonHelper = $jsonHelper;
		$this->request = $request;
    }
    
	public function toOptionArray()
    {
		$post = $this->request->getParams();
		if(isset($post['store'])){
		    $store_code = $post['store'];
		}else{
			$store_code = 0;
		}
		$sourcelang = $this->_helper->getConfig('ch_memories/enable/languagefrom',$store_code);
		$targetlang = $this->_helper->getConfig('ch_memories/enable/languageto',$store_code);
		if($sourcelang != '' && $targetlang != ''){
		    $responses = $this->_helper->getMemorylists();
			$memoryids = array();
		    foreach($responses as $key=>$values){
		        $srclang = $values['srclang'];
		    	$trglang = $values['trglang'];
		    	if($srclang == $sourcelang && $trglang == $targetlang){
		    	   $memoryids[] = $values['id'];
		    	}
		    }
            $memoryids_array = [];
            if(empty($memoryids)){
    	        $params = array();
				$params['name'] = $sourcelang.'_'.$targetlang;
			    $params['srclang'] = $sourcelang; 
			    $params['trglang'] = $targetlang;
                try{
                    $this->_curl->post($url, $params);
                    $body = $this->_curl->getBody();
			    	$response = $this->jsonHelper->jsonDecode($body);
			    	$memoryids = $response['id'];
					$memoryids_array[0]['label'] = $memoryids;
    	            $memoryids_array[0]['value'] = $memoryids;
                }catch (\Exception $e) {
			    	$response['status'] = 'failed';
			        $response['error_mass'] = $e;
                }
		    }else{
				$i =0;
		        foreach($memoryids as $key => $value){
		            $memoryids_array[$i]['label'] = $value;
    	            $memoryids_array[$i]['value'] = $value;
    	        	$i++;
		        }
		    }
	    }else{
			$memoryids_array[0]['label'] = 'None';
    	    $memoryids_array[0]['value'] = '0';
		}
		return $memoryids_array;
    }
}