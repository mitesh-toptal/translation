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

class Index extends \Magento\Backend\App\Action
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
	    $post = $this->getRequest()->getParams();
		$convert_string_status = array();
		$text = $post['value'];
		$final_array['id'] = $post['id'];
		$storecode = $post['storecode'];
		$languageto = $post['langto'];
		$languagefrom = $post['langfrom'];
		$memoryid = $this->_helper->getConfig('ch_memories/enable/memories',$storecode);
		if($memoryid == ''){
			$memoryid = $this->_helper->getConfig('ch_memories/enable/memories',0);
		}
		if($memoryid > 0){
		    $value = $post['value'];
			$convert_string_status['languagefromfullname'] = $post['languagefromfullname'];
			$convert_string_status['languagetofullname'] = $post['languagetofullname'];
			$update_text = str_replace(' ', '%20', $value);
			$content_array = $this->_helper->Translationcontent($memoryid,$update_text);
			if(!empty($content_array)){
			    $conveted_text = $content_array[0];
			    $conveted_text = preg_replace('/[^"\'A-Za-z0-9\-]/', ' ', $conveted_text);
			    $convert_string_status['text'] = trim($conveted_text);
		        $convert_string_status['status'] = 'success';
		        $final_array['value'] = $convert_string_status; 
		        $final_array['status'] = 'success';
			}else{
				$final_array['status'] = 'failed';
			}
		}else{
		    $final_array['status'] = 'failed';
		}
		$resultJson = $this->resultJsonFactory->create();
		$response = $final_array;
        $resultJson->setData($response);
		return $resultJson;
	    echo '<pre>'; print_r($post); die("testt");
    }
}
