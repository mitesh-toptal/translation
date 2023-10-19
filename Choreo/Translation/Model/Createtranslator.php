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

use Magento\Cms\Model\PageFactory;
use Magento\Framework\DomDocument\DomDocumentFactory;

class Createtranslator
{
    protected $pageFactory;
    public function __construct(
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Convert\ConvertArray $convertArray,
        \Choreo\Translation\Helper\Data $helper,
        DomDocumentFactory $domFactory,
        PageFactory $pageFactory,
        \Magento\Cms\Model\BlockRepository $blockRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\DirectoryList $directory
    ) {
        $this->file            = $file;
        $this->_helper         = $helper;
        $this->convertArray    = $convertArray;
        $this->domFactory      = $domFactory;
        $this->pageFactory     = $pageFactory;
        $this->blockRepository = $blockRepository;
        $this->_storeManager   = $storeManager;
        $this->_downloader     = $fileFactory;
        $this->directory       = $directory;
    }

    public function checkmemory_id($postdata)
    {
        $sourcelang = $postdata['sourcelanguage'];
        $targetlang = $postdata['targetlanguage'];
        $responses  = $this->_helper->getMemorylists();
        if (!empty($responses)) {
            $memoryid = '';
            foreach ($responses as $key => $values) {
                $srclang = $values['srclang'];
                $trglang = $values['trglang'];
                if ($srclang == $sourcelang && $trglang == $targetlang) {
                    $memoryid = $values['id'];
                    break;
                }
            }
            if ($memoryid == '') {
                $params['name']    = $sourcelang . '_' . $targetlang;
                $params['srclang'] = $sourcelang;
                $params['trglang'] = $targetlang;
                $response          = $this->_helper->createMemoryid($params);
                if ($response['status'] == 'success') {
                    $memoryid = $response['response']['id'];
                } else {
                    return false;
                }
            }
            return $memoryid;
        }
    }

    public function createJsonFile($assocArray)
    {
        if (isset($assocArray['memoryid']) && $assocArray['memoryid'] == '') {
            $memoryid = $this->checkmemory_id($assocArray);
        } else {
            $memoryid = $assocArray['memoryid'];
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $jsonDataArray = array();
        // products
        if (isset($assocArray['products']) && $assocArray['products'] != '') {
            $attrs = explode(",", $this->_helper->getConfig('ch_attributes/attributes/productattributes', 0));
            $Ids   = explode("&", trim($assocArray['products'], '&'));

            foreach ($Ids as $key => $Id) {
                $model = $objectManager->create('\Magento\Catalog\Model\Product')->load($Id);

                foreach ($attrs as $attribute) {
                    if ($model->getData($attribute)) {
                        $value                      = $model->getData($attribute);
                        $DynamicKey                 = "products_" . $Id . "_" . $attribute;
                        $jsonDataArray[$DynamicKey] = $value;
                    }
                }
            }
        }

        // categories
        if (isset($assocArray['categories']) && $assocArray['categories'] != '') {
            $attrs = explode(",", $this->_helper->getConfig('ch_attributes/attributes/categoryfields', 0));
            $Ids   = explode(",", trim($assocArray['categories'], ','));

            foreach ($Ids as $key => $Id) {
                $model = $objectManager->create('\Magento\Catalog\Model\Category')->load($Id);

                foreach ($attrs as $attribute) {
                    if ($model->getData($attribute)) {
                        $value                      = $model->getResource()->getAttribute($attribute)->getFrontend()->getValue($model);
                        $DynamicKey                 = "categories_" . $Id . "_" . $attribute;
                        $jsonDataArray[$DynamicKey] = $value;
                    }
                }
            }
        }

        // Pages
        if (isset($assocArray['pages']) && $assocArray['pages'] != '') {
            $attrs = explode(",", $this->_helper->getConfig('ch_attributes/attributes/cmspagefields', 0));
            $Ids   = explode("&", trim($assocArray['pages'], '&'));

            foreach ($Ids as $key => $Id) {
                $model = $this->pageFactory->create()->load($Id);

                foreach ($attrs as $attribute) {
                    if ($model->getData($attribute)) {
                        $value                      = $model->getData($attribute);
                        $DynamicKey                 = "pages_" . $Id . "_" . $attribute;
                        $jsonDataArray[$DynamicKey] = $value;
                    }
                }
            }
        }

        // Blocks
        if (isset($assocArray['blocks']) && $assocArray['blocks'] != '') {
            $attrs = explode(",", $this->_helper->getConfig('ch_attributes/attributes/cmspagefields', 0));
            $Ids   = explode("&", trim($assocArray['blocks'], '&'));

            foreach ($Ids as $key => $Id) {
                $model = $this->blockRepository->getById($Id)->setStoreId(0);

                foreach ($attrs as $attribute) {
                    if ($model->getData($attribute)) {
                        $value                      = $model->getData($attribute);
                        $DynamicKey                 = "blocks_" . $Id . "_" . $attribute;
                        $jsonDataArray[$DynamicKey] = $value;
                    }
                }
            }
        }

        $dirname = $this->directory->getPath("media") . "/jobs/";
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }

        $filename     = $assocArray['translationjobname'] . '.json+html';
        $filePath     = $dirname . $filename;
        $projectName  = $assocArray['translationjobname'];
        $newJsonArray = json_encode($jsonDataArray);

        $fp = fopen($filePath, 'w');
        fwrite($fp, $newJsonArray);
        fclose($fp);

        $mediaUrl = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $create_projects['name']      = $projectName;
        $create_projects['memory_id'] = $memoryid;
        $upload_status                = $this->_helper->CreateProjects($create_projects);
        if ($upload_status['status'] == 'success') {
            $respon_da['project_id']      = $upload_status['response']['id'];
            $post_uploaddata['id']        = $upload_status['response']['id'];
            $post_uploaddata['name']      = $mediaUrl . 'jobs/' . $filename;
            $post_uploaddata['file_name'] = $filename;
            $upload_status                = $this->_helper->uploadfile($post_uploaddata);
            if ($upload_status['status'] == 'success') {
                $respon_da['file_id'] = $upload_status['response']['id'];
            } else {
                $respon_da['file_id'] = '';
            }
        } else {
            $respon_da['project_id'] = '';
        }

        $respon_da['memoryid']  = $memoryid;
        $respon_da['file_link'] = $mediaUrl . 'jobs/' . $filename;
        $respon_da['file_link'] = $mediaUrl . 'jobs/' . $filename;
        return $respon_da;

    }
}
