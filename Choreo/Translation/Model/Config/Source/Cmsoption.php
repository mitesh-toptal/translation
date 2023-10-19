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

class Cmsoption implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'title', 'label' => __('Page Title')],
            ['value' => 'content_heading', 'label' => __('Content Heading')],
            ['value' => 'content', 'label' => __('Content')],
            ['value' => 'meta_title', 'label' => __('Meta Title')],
            ['value' => 'meta_keywords', 'label' => __('Meta Keywords')],
            ['value' => 'meta_description', 'label' => __('Meta Description')]
        ];
    }
}