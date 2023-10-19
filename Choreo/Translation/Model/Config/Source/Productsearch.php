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

class Productsearch implements \Magento\Framework\Option\ArrayInterface
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
		    if($attribute->getAttributeCode() != 'category_ids' && $attribute->getAttributeCode() != 'custom_layout' && $attribute->getAttributeCode() != 'custom_layout_update_file' && $attribute->getAttributeCode() != 'gift_message_available' && $attribute->getAttributeCode() != 'has_options' && $attribute->getAttributeCode() != 'image_label' && $attribute->getAttributeCode() != 'links_exist' && $attribute->getAttributeCode() != 'links_purchased_separately' && $attribute->getAttributeCode() != 'links_title' && $attribute->getAttributeCode() != 'media_gallery' && $attribute->getAttributeCode() != 'merchant_center_category' && $attribute->getAttributeCode() != 'msrp_display_actual_price_type' && $attribute->getAttributeCode() != 'news_from_date' && $attribute->getAttributeCode() != 'news_to_date' && $attribute->getAttributeCode() != 'old_id' && $attribute->getAttributeCode() != 'options_container' && $attribute->getAttributeCode() != 'page_layout' && $attribute->getAttributeCode() != 'price_type' && $attribute->getAttributeCode() != 'price_view' && $attribute->getAttributeCode() != 'required_options' && $attribute->getAttributeCode() != 'small_image_label' && $attribute->getAttributeCode() != 'thumbnail_label' && $attribute->getAttributeCode() != 'tier_price' && $attribute->getAttributeCode() != 'url_path' && $attribute->getAttributeCode() != 'weight_type'){
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
        /* $productAttributes->addFieldToFilter(
            ['frontend_input'],
            [['text', 'textarea', 'texteditor']]
        ); */
        return $productAttributes;
    }
}