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

namespace Choreo\Translation\Model\ResourceModel\Jobs;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
    public function _construct()
    {
        $this->_init('Choreo\Translation\Model\Jobs', 'Choreo\Translation\Model\ResourceModel\Jobs');
    }
	public function toOptionArray()
    {
        $data = $this->_toOptionArray('id');
		foreach ($data as $key => $item) {
            $data[$key]['label'] = $this->convertToFrontendLabel($item['value']);
        }
        return $data;
    }

    private function convertToFrontendLabel($label)
    {
        $frontEndLabel = '';
        switch ($label) {
            case '1':
                $frontEndLabel = __(ucwords('enable'));
                break;
            case '0':
                $frontEndLabel = __(ucwords('disable'));
                break;
        }

        return $frontEndLabel;
    }
}
