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

namespace Choreo\Translation\Controller\Adminhtml\Index;
use Magento\Framework\Serialize\SerializerInterface;
class Memoryids extends \Magento\Backend\App\Action
{
    protected $_curl;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Choreo\Translation\Helper\Data $helper,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		SerializerInterface $serializer,
		\Magento\Store\Api\StoreRepositoryInterface $storeRepository,
		\Magento\Framework\Serialize\Serializer\Json $json,
		\Magento\Framework\Json\Decoder $jsonDecoder,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
		$this->_curl = $curl;
        $this->resultPageFactory = $resultPageFactory;
		$this->_helper = $helper;
		$this->storeRepository= $storeRepository;
		$this->jsonHelper = $jsonHelper;
		$this->serializer = $serializer;
		$this->json = $json;
		$this->_jsonDecoder = $jsonDecoder;
		$this->resourceConnection = $resourceConnection;
		$this->resultJsonFactory = $resultJsonFactory;
    }
    public function execute()
    {
		$connection  = $this->resourceConnection->getConnection();
        $tableName   = $connection->getTableName('choreo_memory_ids');
		$job_form = $this->getRequest()->getParam('job_form');
		$sourcelang = $this->getRequest()->getParam('sourcelang');
		$targetlang = $this->getRequest()->getParam('targetlang');
		$responses = $this->_helper->getMemorylists();
		$memoryid = array();
		if(!empty($responses)){
		    if($job_form == 1){		    	
		    	$option = '';
				foreach($responses as $key=>$values){
		            $srclang = $values['srclang'];
		        	$trglang = $values['trglang'];
		        	if($srclang == $sourcelang && $trglang == $targetlang){
		        		$option .= '<option value= "'.$values['id'].'">'.$values['id'].'</option>';
		        		$memoryid[] = $values['id'];		        	   
		        	}
				}				
				if(empty($option)){
		    		$params['name'] = $sourcelang.'_'.$targetlang;
		        	$params['srclang'] = $sourcelang; 
		        	$params['trglang'] = $targetlang;
					$response = $this->_helper->createMemoryid($params);					
					if($response['status'] == 'success'){
						$option .= '<option value= "'.$response['response']['id'].'">'.$response['response']['id'].'</option>';
		        		$memoryid[] = $response['response']['id'];		        
		        	}else{
		    			$response['status'] = 'falied';
		    			$response['error_mass'] = $response['error'];
		    		}
		        }
		        if(!empty($option)){
		        	$response['status'] = 'success';
		    	    $response['jobmemoryid'] = $option;
		    	    if(!empty($memoryid)){

		    	    	$response['firstmoryid'] = reset($memoryid);	
		    	    }		    	    
		        }

		        $resultJson = $this->resultJsonFactory->create();
		        $resultJson->setData($response);
		        return $resultJson;
		    }
		    $storecode = $this->getRequest()->getParam('storecode');
		    $selected_memory = $this->_helper->getConfig('ch_memories/enable/memories',$storecode);
		    $memoryids = '';
		    $search_key = $sourcelang.'_'.$targetlang;
		    $select_query = "SELECT * FROM $tableName WHERE `sourcetaget` LIKE '$search_key'";
		    $results = $this->resourceConnection->getConnection()->fetchAll($select_query);
		    if(!empty($results)){
		    	$tab_memory_id = $results[0]['memoryid'];
		    }else{
		    	$tab_memory_id = 0;
		    }
		    foreach($responses as $key=>$values){
		        $srclang = $values['srclang'];
		    	$trglang = $values['trglang'];
		    	if($srclang == $sourcelang && $trglang == $targetlang){
		    	   $apimemory = $values['id'];
		    	    if($tab_memory_id > 0 && $tab_memory_id == $apimemory){
		    		    $memoryids .= '<option value="'.$values['id'].'" selected>'.$values['id'].'</option>';
		    	    }else{
		    	        if($selected_memory == $apimemory){
		    	            $memoryids .= '<option value="'.$values['id'].'" selected>'.$values['id'].'</option>';
		    	        }else{
		    		    	$memoryids .= '<option value="'.$values['id'].'">'.$values['id'].'</option>';
		    		    }
		    		}
		    	}
		    }
		    if($memoryids != ''){
		    	$response['status'] = 'success';
		    	$response['memoryid'] = $memoryids;
		    }else{
		    	$params['name'] = $sourcelang.'_'.$targetlang;
		    	$params['srclang'] = $sourcelang; 
		    	$params['trglang'] = $targetlang;
		    	$response = $this->_helper->createMemoryid($params);
		    	if($response['status'] == 'success'){
		    		$memoryids = $response['response']['id'];
		    		$response['status'] = 'success';
		    	    $response['memoryid'] = '<option value="'.$memoryids.'">'.$memoryids.'</option>';
		    	}
		    }
		}else{
			$params['name'] = $sourcelang.'_'.$targetlang;
		    $params['srclang'] = $sourcelang; 
		    $params['trglang'] = $targetlang;
		    $response = $this->_helper->createMemoryid($params);
		    if($response['status'] == 'success'){
		    	$memoryids = $response['response']['id'];
		    	$response['status'] = 'success';
				$response['jobmemoryid'] = $memoryids;
		        $response['memoryid'] = '<option value="'.$memoryids.'">'.$memoryids.'</option>';
		    }
		}		
		$resultJson = $this->resultJsonFactory->create();
		$resultJson->setData($response);
		return $resultJson;
    }
	
	public function deleteMemoryid($memory_id){
	    $api_url = $this->_helper->GetAPIUrl();
		$url = $api_url.'memories?method=memories&id='.$memory_id;
	    $this->_curl->get($url);
		$body = $this->_curl->getBody();
		$response = $this->jsonHelper->jsonDecode($body);
		echo '<pre>'; print_r($response); die("testt");
	}
}