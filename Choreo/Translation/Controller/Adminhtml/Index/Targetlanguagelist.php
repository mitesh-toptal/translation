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

class Targetlanguagelist extends \Magento\Backend\App\Action
{
    protected $_curl;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Choreo\Translation\Helper\Data $helper,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
		$this->_curl = $curl;
        $this->resultPageFactory = $resultPageFactory;
		$this->_helper = $helper;
		$this->jsonHelper = $jsonHelper;
		$this->resultJsonFactory = $resultJsonFactory;
    }
    public function execute()
    {
	    $source_countries_response = $this->_helper->getlanguageslists();
		$sourcelang = $this->getRequest()->getParam('sourcelang');
		$current_timespam = $this->getRequest()->getParam('current_timespam');
		$response = array();
		$targetcodes = array();
		if(!empty($source_countries_response)){
		    $code_to_name = $source_countries_response['code_to_name'];
		    $source_to_target = $source_countries_response['source_to_target'];
			if(array_key_exists($sourcelang,$source_to_target)){
			    $depended_lang = $source_to_target[$sourcelang];
				$i = 0; 
				foreach($depended_lang as $target_lang_code=>$val){
					$targetcodes[$target_lang_code] = $code_to_name[$target_lang_code];
				}
				$option = '';
				$option .= '<option value= "0">--Select--</option>';
				if(!empty($targetcodes)){
					$response['status'] = 'success';
					foreach($targetcodes as $val=>$label){
				        $option .= '<option value= "'.$val.'">'.$label.'</option>';
						if($i < 1){
						   $response['current_timespam_name'] = $sourcelang.'_'.$val.'_'.$current_timespam;
					    }
					    $i++;
					}
			        $response['target_options'] = $option;
				}else{
					$response['status'] = 'nodata';
			        $response['error_mass'] = 'No found any target languages.';
				}
			}else{
			    $response['status'] = 'failed';
			    $response['error_mass'] = 'Sorry you can\'t this translate language. please choose other language.';
			}
		}
		$resultJson = $this->resultJsonFactory->create();
		$resultJson->setData($response);
		return $resultJson;
    }
}