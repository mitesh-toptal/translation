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

namespace Choreo\Translation\Setup;
 
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
/**
 * Class UpgradeSchema
 * @package Magebay\Hello\Setup
 */
class UpgradeSchema implements  UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $choreo_pro_attachment_rel = $setup->getTable('choreo_product_attachment_rel');
            if ($setup->getConnection()->isTableExists($choreo_pro_attachment_rel) != true) {
                $table = $setup->getConnection()
                ->newTable($choreo_pro_attachment_rel)
                ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true])
                ->addColumn('job_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Job Id')
                ->addColumn('product_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Magento Product Id')
                ->setComment('Product Attachment relation table');
				$setup->getConnection()->createTable($table);
                $setup->endSetup();
			}
        }
		if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $choreo_category_attachment_rel = $setup->getTable('choreo_category_attachment_rel');
            if ($setup->getConnection()->isTableExists($choreo_category_attachment_rel) != true) {
                $cate_table = $setup->getConnection()
                ->newTable($choreo_category_attachment_rel)
                ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true])
				->addColumn('job_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Job Id')
                ->addColumn('cate_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Magento Category Id')
                ->setComment('Pages Attachment relation table');
				$setup->getConnection()->createTable($cate_table);
			}
            $choreo_page_attachment_rel = $setup->getTable('choreo_page_attachment_rel');
            if ($setup->getConnection()->isTableExists($choreo_page_attachment_rel) != true) {
                $page_table = $setup->getConnection()
                ->newTable($choreo_page_attachment_rel)
                ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true])
				->addColumn('job_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Job Id')
                ->addColumn('page_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Magento Page Id')
                ->setComment('Pages Attachment relation table');
				$setup->getConnection()->createTable($page_table);
			}
			$choreo_block_attachment_rel = $setup->getTable('choreo_block_attachment_rel');
            if ($setup->getConnection()->isTableExists($choreo_block_attachment_rel) != true) {
                $satic_table = $setup->getConnection()
                ->newTable($choreo_block_attachment_rel)
                ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true])
				->addColumn('job_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Job Id')
                ->addColumn('block_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Magento Block Id')
                ->setComment('Block Attachment relation table');
				$setup->getConnection()->createTable($satic_table);
			}
        }
	}
}