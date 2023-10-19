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

class Productattribute implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory
    ) {
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
    }
	
    public function toOptionArray()
    {
	    $attributes = $this->getFilterableAttributes();
		$finaly_array = array();
		$i = 0;
		foreach ($attributes as $attribute) {
		    if($attribute->getAttributeCode() != 'sku' && $attribute->getAttributeCode() != 'category_ids' && $attribute->getAttributeCode() != 'url_key'){
                $finaly_array[$i]['value'] = $attribute->getAttributeCode();
                $finaly_array[$i]['label'] = $attribute->getFrontendLabel();
			    $i++;
			}
        }
		return $finaly_array;
    }
	
	public function getFilterableAttributes()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributes */
        $productAttributes = $this->productAttributeCollectionFactory->create();
        $productAttributes->addFieldToFilter(
            ['frontend_input'],
            [['text', 'textarea', 'texteditor']]
        );
        return $productAttributes;
    }
}