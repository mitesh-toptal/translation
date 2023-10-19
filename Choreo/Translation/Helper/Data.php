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

namespace Choreo\Translation\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constants
     */
    const Translation_CREDENTIALS_API_URL = 'https://lilt.com/2/';

    protected $_curl;
    protected $_backendUrl;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\DirectoryList $directory
    ) {
        $this->_curl              = $curl;
        $this->curlFactory        = $curlFactory;
        $this->jsonHelper         = $jsonHelper;
        $this->_backendUrl        = $backendUrl;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager       = $storeManager;
        $this->directory          = $directory;
        parent::__construct($context);
    }

    public function getConfig($config_path, $store_code)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_code
        );
    }
    public function GetAPIUrl()
    {
        return self::Translation_CREDENTIALS_API_URL;
    }

    public function GetAPIKey()
    {
        return $this->getConfig('choreotranslation/activation/activationkey', 0);
    }

    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('choreo_translation/jobs/products', ['_current' => true]);
    }
    public function getPagesUrl()
    {
        return $this->_backendUrl->getUrl('choreo_translation/jobs/pages', ['_current' => true]);
    }
    public function getBlockUrl()
    {
        return $this->_backendUrl->getUrl('choreo_translation/jobs/blocks', ['_current' => true]);
    }

    public function getlanguageslists()
    {
        $url = $this->GetAPIUrl();
        $key = $this->GetAPIKey();
        if ($url != '' && $key != '') {
            $dynamicUrl = $url . 'languages';
            $ch         = curl_init($dynamicUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/octet-stream'));
            curl_setopt($ch, CURLOPT_USERPWD, $key . ":" . $key);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            $response = $this->jsonHelper->jsonDecode($result);
            if (isset($response['error'])) {
                $response = array();
            }
            return $response;
        }
    }
    public function getMemorylists()
    {
        $url = $this->GetAPIUrl();
        $key = $this->GetAPIKey();
        if ($url != '' && $key != '') {
            $dynamicUrl = $url . 'memories';
            $ch         = curl_init($dynamicUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/octet-stream'));
            curl_setopt($ch, CURLOPT_USERPWD, $key . ":" . $key);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            $response = $this->jsonHelper->jsonDecode($result);
            if (isset($response['error'])) {
                $response = array();
            }
            return $response;
        }
    }
    public function createMemoryid($postdata)
    {
        $url = $this->GetAPIUrl();
        $key = $this->GetAPIKey();
        if ($url != '' && $key != '') {

            try {
                $dynamicUrl = $url . 'memories';
                $ch         = curl_init();
                curl_setopt($ch, CURLOPT_URL, $dynamicUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_USERPWD, $key . ":" . $key);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                curl_close($ch);
                $response = $this->jsonHelper->jsonDecode($result);
                if (isset($response['error'])) {
                    $api_response['status']     = 'failed';
                    $api_response['error_mass'] = $response['error'];
                } else {
                    $api_response['status']   = 'success';
                    $api_response['response'] = $response;
                }
                return $api_response;
            } catch (\Exception $e) {
                $response['status']     = 'failed';
                $response['error_mass'] = $e;
                return $response;
            }
        }
    }

    public function CreateProjects($postdata)
    {
        $apiurl = $this->GetAPIUrl();
        $apikey = $this->GetAPIKey();
        if ($apiurl != '' && $apikey != '') {
            try {
                $dynamicUrl = $apiurl . 'projects';
                $ch         = curl_init();
                curl_setopt($ch, CURLOPT_URL, $dynamicUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_USERPWD, $apikey . ":" . $apikey);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                curl_close($ch);
                $response                = $this->jsonHelper->jsonDecode($result);
                $apiresponse['status']   = 'success';
                $apiresponse['response'] = $response;
                return $apiresponse;
            } catch (\Exception $e) {
                $response['status']     = 'failed';
                $response['error_mass'] = $e;
                return $response;
            }
        }
    }

    public function uploadfile($postdata)
    {
        $apiurl     = $this->GetAPIUrl();
        $apikey     = $this->GetAPIKey();
        $project_id = $postdata['id'];
        $file_name  = $postdata['file_name'];
        if ($apiurl != '' && $apikey != '') {
            $query = [
                'key' => $apikey,
            ];
            try {
                $dynamicUrl   = $apiurl . 'documents/files?' . http_build_query($query);
                $httpAdapter  = $this->curlFactory->create();
                $absolutepath = $this->directory->getPath("media") . "/jobs/" . $postdata['file_name'];

                $httpAdapter->write(\Zend_Http_Client::POST, $dynamicUrl, 'CURL_HTTP_VERSION_1_1', [
                    "Content-Type:application/octet-stream",
                    'LILT-API:' . json_encode(array('name' => $file_name, 'project_id' => $project_id))],
                    file_get_contents($absolutepath)
                );

                $result   = $httpAdapter->read();
                $body     = \Zend_Http_Response::extractBody($result);
                $response = $this->jsonHelper->jsonDecode($body);

                $apiresponse['status']   = 'success';
                $apiresponse['response'] = $response;
                return $apiresponse;
            } catch (\Exception $e) {
                $response['status']     = 'failed';
                $response['error_mass'] = $e;
                return $response;
            }
        }
    }

    public function getProjectStatus($projectId)
    {
        $url      = $this->GetAPIUrl();
        $key      = $this->GetAPIKey();
        $response = array();
        if ($url != '' && $key != '') {
            $query = [
                'id' => $projectId,                
            ];
            $dynamicUrl = $url . 'projects?' . http_build_query($query);
            $ch         = curl_init($dynamicUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/octet-stream'));
            curl_setopt($ch, CURLOPT_USERPWD, $key . ":" . $key);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            $response = $this->jsonHelper->jsonDecode($result);
            if (isset($response['error'])) {
                $response = array();
            }
        }
        return $response;

    }

    public function downloadJsonFile($file_id)
    {
        $url = $this->GetAPIUrl();
        $key = $this->GetAPIKey();
        if ($url != '' && $key != '') {
            $query = [                
                'id'       => $file_id,
                'is_xliff' => false,
            ];

            $dynamicUrl = $url . 'documents/files?' . http_build_query($query);

            $ch = curl_init($dynamicUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/octet-stream'));
            curl_setopt($ch, CURLOPT_USERPWD, $key . ":" . $key);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            $response = $this->jsonHelper->jsonDecode($result);
            if (!isset($response['error'])) {
                $file = $this->directory->getPath("media") . "/jobs/" . $file_id . '.json+html';
                $fp   = fopen($file, 'w+');
                fwrite($fp, $result);
                fclose($fp);
            }
        }
    }
}
