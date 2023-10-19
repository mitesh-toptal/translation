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

class Activation extends \Magento\Backend\App\Action
{
    protected $_curl;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Choreo\Translation\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->curlFactory       = $curlFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper           = $helper;
        $this->jsonHelper        = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $api_url   = $this->_helper->GetAPIUrl();
        $clientkey = $this->getRequest()->getParam('apiKey');

        if ($api_url) {
            $query = [
                'key' => $clientkey,
            ];
            $dynamicUrl = $api_url . '?' . http_build_query($query);

            $ch = curl_init($dynamicUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/octet-stream'));
            curl_setopt($ch, CURLOPT_USERPWD, $clientkey . ":" . $clientkey);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            $responses = $this->jsonHelper->jsonDecode($result);

            if (isset($responses['api_name'])) {
                $response['status']     = 'success';
                $response['check_icon'] = '<p class="choreo_check_icon">✓</p>';
            } else {
                $response['error_mass'] = '<p class="error_check_icon" style="color:red;">Verification Failed</p>';
            }
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
    }
}
