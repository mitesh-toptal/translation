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
use \Magento\Store\Model\StoreRepository;
class Storelists implements \Magento\Framework\Option\ArrayInterface
{
    protected $_storeRepository;
    public function __construct(
        StoreRepository $_storeRepository,
		\Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
    ) {
        $this->_storeRepository = $_storeRepository;
		$this->_websiteCollectionFactory = $websiteCollectionFactory;
    }
    
    public function toOptionArray()
    {
        $stores = $this->_storeRepository->getList();
        $websiteIds = array();
        $i =0 ;
        foreach ($stores as $store) {
		    $websiteId = $store["website_id"];
			$storeId = $store["store_id"];
			if($storeId > 0){
			    $storeList = array();
			    $website_name = $this->getWebsiteName($websiteId);
				$websiteIds[$i]['label'] = $website_name;
                $storeName = $store["name"];
                $storeList[$i]['label'] = $storeName;
                $storeList[$i]['value'] = $storeId;
				$websiteIds[$i]['value'] = $storeList;
				$i++;
        	}
        }
		return $websiteIds;
    }
	
	public function getWebsiteName($websiteId)
    {  
       $collection = $this->_websiteCollectionFactory->create();
       $websiteData = $collection->addFieldToFilter('website_id',$websiteId);
       foreach($websiteData->getData() as $websiteName):
          $webName = $websiteName['name'];
       endforeach;
       return $webName;
    }
}