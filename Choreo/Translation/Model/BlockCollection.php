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

use Magento\Framework\DB\Select;

class BlockCollection extends \Magento\Cms\Model\ResourceModel\Block\Collection
{

    public function is_translated($job_id)
    {
		
        $allJobs = $this->_resource->getTable('choreo_translation_jobs');
        $blockTrans = $this->_resource->getTable('choreo_block_attachment_rel');
		if($job_id != ''){
            $this->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(
                ['main_table.block_id', 'main_table.title', 'main_table.creation_time', 'MAX(IF((blockTrans.job_id) IS NULL, 0, 1)) AS is_translated']
            )->joinLeft(
                ['blockTrans' => $blockTrans],
                'main_table.block_id=blockTrans.block_id',
                []
            )->joinLeft(
                ['allJob' => $allJobs],
                'blockTrans.job_id='.$job_id,
                []
            )->group('block_id');
		}else{
			$this->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(
                ['main_table.block_id', 'main_table.title', 'main_table.creation_time', 'MAX(IF((blockTrans.job_id) IS NULL, 0, 1)) AS is_translated']
            )->joinLeft(
                ['blockTrans' => $blockTrans],
                'main_table.block_id=blockTrans.block_id',
                []
            )->group('block_id');
		}
        return $this;
    }

    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();

        $select = clone $this->getSelect();
        $select
            ->reset(Select::ORDER)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET);

        $countSelect
            ->reset()
            ->from(['s' => $select])
            ->reset(Select::COLUMNS)
            ->columns('COUNT(DISTINCT block_id)');

        return $countSelect;
    }

    function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
//        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
}