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

class TranslationApply
{
    protected $jobFactory;
    protected $_filesystem;
    protected $productRepository;
    protected $categoryRepository;
    protected $pageFactory;
    protected $blockFactory;
    protected $storeRepository;
    protected $pageRepositoryInterface;
    protected $blockRepositoryInterface;
    protected $searchCriteriaBuilder;
    protected $directoryList;
    protected $categoryFactory;
    protected $logger;

    public function __construct(
        \Choreo\Translation\Model\JobsFactory $jobFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\Category $categoryRepository,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepositoryInterface,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->jobFactory               = $jobFactory;
        $this->_filesystem              = $filesystem;
        $this->productRepository        = $productRepository;
        $this->categoryRepository       = $categoryRepository;
        $this->pageFactory              = $pageFactory;
        $this->blockFactory             = $blockFactory;
        $this->storeRepository          = $storeRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->pageRepositoryInterface  = $pageRepositoryInterface;
        $this->blockRepositoryInterface = $blockRepositoryInterface;
        $this->directoryList            = $directoryList;
        $this->categoryFactory          = $collecionFactory;
        $this->logger                   = $logger;
    }
    
    public function process(int $jobId)
    {
        $this->logger->info("Job ID : " . $jobId. "has started in queue");

        $translationArray = $this->getTranslationArray($jobId);

        if (!empty($translationArray)) {

            try {
                $storeId       = $translationArray['target-store'];
                $productArray  = array();
                $categoryArray = array();
                $pageArray     = array();
                $blockArray    = array();

                foreach ($translationArray['data'] as $key => $value) {

                    $previewArray = array();
                    foreach ($value['source_attr'] as $key1 => $value1) {
                        $previewArray[$key1]['source'] = $value1;
                    }
                    foreach ($value['target_attr'] as $key2 => $value2) {
                        $previewArray[$key2]['target'] = $value2;
                    }

                    foreach ($previewArray as $attribName => $attribValue) {
                        $explodeArray = explode("_", $key);
                        if ($value['type'] == 'products') {
                            $productArray[$explodeArray[1]][$attribName] = $attribValue['target'];
                        }
                        if ($value['type'] == 'categories') {
                            $categoryArray[$explodeArray[1]][$attribName] = $attribValue['target'];
                        }
                        if ($value['type'] == 'pages') {
                            $pageArray[$explodeArray[1]][$attribName] = $attribValue['target'];
                        }
                        if ($value['type'] == 'blocks') {
                            $blockArray[$explodeArray[1]][$attribName] = $attribValue['target'];
                        }
                    }

                }

                //// Passing Product Data
                if (!empty($productArray)) {
                    foreach ($productArray as $productkey => $value) {                        
                        $product = $this->getProduct($productkey, $storeId);
                        if (!empty($product)) {
                            foreach ($value as $att => $attVal) {

                                $product->setData($att, $attVal);
                                $product->getResource()->saveAttribute($product, $att);
                            }                           
                        }
                    }
                }

                //// Passing Category Data
                if (!empty($categoryArray)) {
                    foreach ($categoryArray as $key => $value) {                        
                        $category = $this->getCategory($key);
                        if (!empty($category)) {
                            $category->setStoreId($storeId);
                            foreach ($value as $att => $attVal) {
                                $category->setData($att, $attVal);
                            }
                            $category->save();
                        }
                    }
                }

                //// Passing CMS Page Data
                if (!empty($pageArray)) {
                    foreach ($pageArray as $key => $value) {                        
                        $this->getCMSPage($key, $storeId, $value);
                    }
                }

                //// Passing CMS Block Data
                if (!empty($blockArray)) {
                    foreach ($blockArray as $key => $value) {                        
                        $this->getCMSBlock($key, $storeId, $value);
                    }
                }
            } catch (\Exception $e) {
                $this->logger->info("Error: " . $e->getMessage());
            }
        }

        $jobNumber = $jobId;
        $jobs      = $this->jobFactory->create()->getCollection()->addFieldToFilter('job_number', $jobNumber);
        if (!empty($jobs)) {
            foreach ($jobs as $job) {
                $job->setStatus("Translations applied")->save();
            }
        }
        $this->logger->info("Job ID : " . $jobId. "has applied translation");        
    }

    public function getProduct($id, $store)
    {
        $prod = $this->productRepository->getById($id, false, $store);
        return $prod;
    }

    public function getCategory($categoryId)
    {    	
        $collection = $this->categoryFactory
            ->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('meta_title')
            ->addAttributeToSelect('meta_keywords')
            ->addAttributeToSelect('meta_description')
            ->addAttributeToFilter('entity_id', ['eq' => $categoryId])
            ->setPageSize(1);

        return $catObj = $collection->getFirstItem();
    }

    public function getCMSPage($pageId, $storeId, $changesArray)
    {
        try {
            $page = $this->pageFactory->create();
            $page->load($pageId);
            $newPageCreate = true;

            if (!empty($page)) {
                $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $page->getData('identifier'), 'eq')->create();
                $currentPages   = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();
                $originalStores = $this->getExistingStores();
                /// get Value of pages which is exist for current stores
                $existStoreArray = array();
                foreach ($currentPages as $pagesData) {
                    foreach ($pagesData->getData('store_id') as $key => $value) {
                        $existStoreArray[] = $value;
                    }
                }

                if (count($currentPages) == 1 && in_array(0, $existStoreArray)) {
                    /// Change store to current page
                    $assignNewStores = $originalStores;
                    $key             = array_search(0, $assignNewStores);
                    if ($key) {
                        unset($assignNewStores[$key]);
                    }
                    $key1 = array_search($storeId, $assignNewStores);
                    if ($key1) {
                        unset($assignNewStores[$key1]);
                    }
                    $page->unsStoreId();
                    $page->setStoreId($assignNewStores)->save();
                } else {
                    foreach ($currentPages as $pagesData) {
                        if (in_array($storeId, $pagesData->getData('store_id'))) {
                            if (count($pagesData->getData('store_id')) == 1) {
                                foreach ($changesArray as $att => $attVal) {
                                    if($att == "content"){
                                        if(str_replace('=" ','="',$attVal)){                                            
                                            $pagesData->setData($att, str_replace('=" ','="',$attVal));
                                        }else{
                                            $pagesData->setData($att, $attVal);
                                        }
                                    }else{
                                        $pagesData->setData($att, $attVal);   
                                    }                                    
                                }
                                $pagesData->save();
                                $newPageCreate = false;
                            } else {
                                $assignNewStores = $pagesData->getData('store_id');
                                $key1            = array_search($storeId, $assignNewStores);
                                if ($key1) {
                                    unset($assignNewStores[$key1]);
                                }
                                $pagesData->unsStoreId();
                                $pagesData->unsStore();
                                $pagesData->unsStores();
                                $pagesData->unsParentId();
                                $pagesData->setStores($assignNewStores);
                                $pagesData->setStore($assignNewStores);
                                $pagesData->setStoreId($assignNewStores)->save();
                            }
                            break;
                        }
                    }

                }

                if ($newPageCreate) {
                    //// New Page creation code
                    $newPage = $this->pageFactory->create();
                    $newPage->setData("title", $page->getData('title'));
                    $newPage->setData("page_layout", $page->getData('page_layout'));
                    $newPage->setData("meta_keywords", $page->getData('meta_keywords'));
                    $newPage->setData("meta_description", $page->getData('meta_description'));
                    $newPage->setData("identifier", $page->getData('identifier'));
                    $newPage->setData("content_heading", $page->getData('content_heading'));
                    $newPage->setData("content", $page->getData('content'));
                    $newPage->setData("is_active", $page->getData('is_active'));
                    $newPage->setData("store_id", array($storeId));
                    /// Translated Value Updated Before Save
                    foreach ($changesArray as $att => $attVal) {
                        if($att == "content"){                            
                            if(str_replace('=" ','="',$attVal)){                                            
                                $newPage->setData($att, htmlspecialchars_decode(str_replace('=" ','="',$attVal)));
                            }else{
                                $newPage->setData($att, htmlspecialchars_decode($attVal));
                            }
                        }else{
                            $newPage->setData($att, htmlspecialchars_decode($attVal));
                        }                        
                    }
                    $newPage->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->info("Error: " . $e->getMessage());
        }
    }

    public function getCMSBlock($blockId, $storeId, $changesArray)
    {
        try {
            $block = $this->blockFactory->create();
            $block->load($blockId);
            $newBlockCreate = true;

            if (!empty($block)) {
                $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $block->getData('identifier'), 'eq')->create();
                $currentBlocks  = $this->blockRepositoryInterface->getList($searchCriteria)->getItems();

                $originalStores = $this->getExistingStores();
                /// get Value of pages which is exist for current stores
                $existStoreArray = array();
                foreach ($currentBlocks as $blockData) {
                    foreach ($blockData->getData('store_id') as $key => $value) {
                        $existStoreArray[] = $value;
                    }
                }

                /// All Store view assign for Block
                if (count($currentBlocks) == 1 && in_array(0, $existStoreArray)) {
                    /// Change store to current page
                    $assignNewStores = $originalStores;
                    $key             = array_search(0, $assignNewStores);
                    if ($key) {
                        unset($assignNewStores[$key]);
                    }
                    $key1 = array_search($storeId, $assignNewStores);
                    if ($key1) {
                        unset($assignNewStores[$key1]);
                    }

                    $block->unsStoreId();
                    $block->unsStore();
                    $block->unsStores();
                    $block->unsParentId();
                    $block->setStores($assignNewStores);
                    $block->setStore($assignNewStores);
                    $block->setStoreId($assignNewStores)->save();
                } else {

                    foreach ($currentBlocks as $blockData) {
                        if (in_array($storeId, $blockData->getData('store_id'))) {
                            if (count($blockData->getData('store_id')) == 1) {
                                foreach ($changesArray as $att => $attVal) {
                                    if($att == "content"){
                                        if(str_replace('=" ','="',$attVal)){                                            
                                            $blockData->setData($att, str_replace('=" ','="',$attVal));
                                        }else{
                                            $blockData->setData($att, $attVal);
                                        }                                        
                                    }else{
                                        $blockData->setData($att, $attVal);    
                                    }
                                    
                                }
                                $blockData->save();
                                $newBlockCreate = false;
                            } else {
                                $assignNewStores = $blockData->getData('store_id');
                                $key1            = array_search($storeId, $assignNewStores);
                                if ($key1) {
                                    unset($assignNewStores[$key1]);
                                }
                                $blockData->unsStoreId();
                                $blockData->unsStore();
                                $blockData->unsStores();
                                $blockData->unsParentId();
                                $blockData->setStores($assignNewStores);
                                $blockData->setStore($assignNewStores);
                                $blockData->setStoreId($assignNewStores)->save();
                            }
                            break;
                        }
                    }
                }

                if ($newBlockCreate) {
                    //// New Page creation code
                    $newBlock = $this->blockFactory->create();
                    $newBlock->setData("title", $block->getData('title'));
                    $newBlock->setData("identifier", $block->getData('identifier'));
                    $newBlock->setData("content_heading", $block->getData('content_heading'));
                    $newBlock->setData("content", $block->getData('content'));
                    $newBlock->setData("is_active", $block->getData('is_active'));
                    $newBlock->setData("store_id", array($storeId));
                    /// Translated Value Updated Before Save
                    foreach ($changesArray as $att => $attVal) {
                        if($att == "content"){
                            if(str_replace('=" ','="',$attVal)){
                                $newBlock->setData($att, htmlspecialchars_decode(str_replace('=" ','="',$attVal)));
                            }else{
                                $newBlock->setData($att, htmlspecialchars_decode($attVal));
                            }
                        }else{
                            $newBlock->setData($att, htmlspecialchars_decode($attVal));
                        }                        
                    }
                    $newBlock->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->info("Error: " . $e->getMessage());
        }

    }

    public function getExistingStores()
    {
        $stores         = $this->storeRepository->getList();
        $storeViewArray = array();
        foreach ($stores as $store) {
            $storeViewArray[] = $store->getStoreId();
        }

        return $storeViewArray;
    }

    public function getJob($jobNumber)
    {
        $jobs = $this->jobFactory->create()->getCollection()->addFieldToFilter('job_number', $jobNumber);
        return $jobs;
    }

    public function getTranslationArray($jobId)
    {
        $jobNumber = $jobId;
        $jobs      = $this->getJob($jobNumber);
        $dataArray = array();
        if (count($jobs)) {
            $originalFile = '';
            foreach ($jobs->getData() as $key => $value) {
                $dataArray['target-store'] = $value['targetstore'];
                $originalFile              = $value['translationjobname'] . ".json+html";
            }           

            $mediaPath          = $this->directoryList->getPath('media');
            $translatedFileName = $jobNumber . ".json+html";
            $filePath           = $mediaPath . "/jobs/";

            if (file_exists($filePath . $translatedFileName) && file_exists($filePath . $originalFile)) {

                $originalFileContent   = file_get_contents($filePath . $originalFile);
                $translatedFileContent = file_get_contents($filePath . $translatedFileName);

                $originalConvertedPhpArray   = json_decode($originalFileContent);
                $TranslatedConvertedPhpArray = json_decode($translatedFileContent);
                foreach ($TranslatedConvertedPhpArray as $key => $value) {
                    $extractArray                                                                                  = explode("_", $key, 3);
                    $dataArray['data'][$extractArray[0] . '_' . $extractArray[1]]['type']                          = $extractArray[0];
                    $dataArray['data'][$extractArray[0] . '_' . $extractArray[1]]['target_attr'][$extractArray[2]] = $value;
                }

                foreach ($originalConvertedPhpArray as $key => $value) {
                    $extractArray                                                                                  = explode("_", $key, 3);
                    $dataArray['data'][$extractArray[0] . '_' . $extractArray[1]]['source_attr'][$extractArray[2]] = $value;
                }

            }
        }
        return $dataArray;
    }
}
