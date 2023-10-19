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
use Magento\Backend\Helper\Data;
use Choreo\Translation\Model\PageCollection;

class Pages extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_massactionBlockName = \Choreo\Translation\Block\Adminhtml\Jobs\Edit\Grid\Massaction\Extended::class;
    protected $pageCollection;
    protected $sourceStoreId;
    protected $_configHelper;
    protected $targetStoreId;
    protected $jobId;

    public function __construct(
        Context $context,
        Data $backendHelper,
        PageCollection $pageCollection,
        array $data = []
    ) {
        $this->pageCollection = $pageCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pagesGrid');
        $this->setDefaultSort('page_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->sourceStoreId = 0;//$this->getRequest()->getParam('source_store_id');
        $this->targetStoreId = 0;//$this->getRequest()->getParam('target_store_id');
        $this->jobId = $this->getRequest()->getParam('id');
    }


    protected function _prepareCollection()
    {
        $collection = $this->pageCollection;
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
            'page_id',
            [
                'header' => __('Page ID'),
                'type' => 'number',
                'index' => 'page_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'class' => 'xxx',
                'width' => '50px',
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
        $this->setMassactionIdField('page_id');
        $this->getMassactionBlock()->setTemplate('Choreo_Translation::jobs/massaction_extended.phtml');
        $this->getMassactionBlock()->addItem('create', []);

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/pagesgrid', ['_current' => true]);
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
        $collection->getSelect()->having('`is_translated` = ? ', reset($condition));
        return $this;
    }

    public function _getSerializerBlock()
    {
        return $this->getLayout()->getBlock('pages_grid_serializer');
    }

    public function _getHiddenInputElementName()
    {
        $serializerBlock = $this->_getSerializerBlock();
        return empty($serializerBlock) ? 'pages' : $serializerBlock->getInputElementName();
    }

    public function _getReloadParamName()
    {
        $serializerBlock = $this->_getSerializerBlock();
        return empty($serializerBlock) ? 'job_pages' : $serializerBlock->getReloadParamName();
    }
}
