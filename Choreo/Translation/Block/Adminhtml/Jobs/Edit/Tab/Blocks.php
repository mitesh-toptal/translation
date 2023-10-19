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

namespace Choreo\Translation\Block\Adminhtml\Jobs\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Choreo\Translation\Model\BlockCollection as BlockCollectionFactory;

class Blocks extends Extended
{

    protected $_massactionBlockName = \Choreo\Translation\Block\Adminhtml\Jobs\Edit\Grid\Massaction\Extended::class;
    protected $_blockCollectionFactory;
    protected $_sourceStoreId;
    protected $targetStoreId;
    protected $jobId;
    protected $sourceStoreId;

    public function __construct(
        Context $context,
        Data $backendHelper,
        BlockCollectionFactory $blockCollectionFactory,
        array $data = []
    ) {
        $this->_blockCollectionFactory = $blockCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('blocksGrid');
        $this->setDefaultSort('block_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->sourceStoreId = $this->getRequest()->getParam('source_store_id');
        $this->targetStoreId = $this->getRequest()->getParam('target_store_id');
        $this->jobId = $this->getRequest()->getParam('id');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_blockCollectionFactory;
        if($this->sourceStoreId){
            $collection->addStoreFilter($this->sourceStoreId);
        }
        $collection->is_translated($this->jobId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'block_id',
            [
                'header' => __('Block ID'),
                'type' => 'number',
                'index' => 'block_id',
                'filter_index'=>'block_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'filter_index'=>'title',
                'class' => 'xxx',
            ]
        );
        $this->addColumn(
            'creation_time',
            [
                'header' => __('Creation Time'),
                'index' => 'creation_time',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'is_translated',
            [
                'header'                    => __('Translated'),
                'index'                     => 'is_translated',
                'width'                     => '50px',
                'type'                      =>'options',
                'options'                   =>  [0 => __('No'), 1 => __('Yes')],
                'filter_condition_callback' => [$this, 'filterIsTranslated']
            ]
        );

        $this->addColumn(
            'store_id',
            [
                'header' => __('Store Views'),
                'index' => 'store_id',                        
                'type' => 'store',
                'store_view' => true,
            ]
        );

        return parent::_prepareColumns();
    }

    function _prepareMassaction()
    {
        $this->setMassactionIdField('block_id');
        $this->getMassactionBlock()->setTemplate('Choreo_Translation::jobs/massaction_extended.phtml');
        $this->getMassactionBlock()->addItem('create', []);

        return $this;
    }

    protected function _getSelectedBlocks()
    {
        $blocks = $this->getRequest()->getPost('job_blocks');
        if (is_array($blocks)) {
            return $blocks;
        }
        return [];
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/blocksgrid', ['_current' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }

    function filterIsTranslated($collection, $column)
    {
        $condition = $column->getFilter()->getCondition();
        $collection->getSelect()->having('`is_translated` =  ?', reset($condition));
        return $this;
    }

    public function _getSerializerBlock()
    {
        return $this->getLayout()->getBlock('blocks_grid_serializer');
    }

    public function _getHiddenInputElementName()
    {
        $serializerBlock = $this->_getSerializerBlock();
        return empty($serializerBlock) ? 'blocks' : $serializerBlock->getInputElementName();
    }

    public function _getReloadParamName()
    {
        $serializerBlock = $this->_getSerializerBlock();
        return empty($serializerBlock) ? 'job_blocks' : $serializerBlock->getReloadParamName();
    }
}