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

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Choreo\Translation\Model\Editformlan;
class Main extends Generic implements TabInterface
{
    protected $storeRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Choreo\Translation\Helper\Data $helper,
		Editformlan $importoptions,
        array $data = [],
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    ) {
        $this->_systemStore = $systemStore;
        $this->_coreRegistry = $registry;
		$this->_helper = $helper;
		$this->_importoptions = $importoptions;
        $this->storeRepository = $storeRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }
	public function getTabLabel()
    {
        return __('1. Specify Job Details');
    }

    public function getTabTitle()
    {
        return __('Job Destination');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
    
    public function _prepareForm()
    {
        $stores = $this->storeRepository->getList();
        $storeViewArray = array();
        foreach ($stores as $store) {
            $storeViewArray[$store->getStoreId()] = $store->getName();                        
        }       
       
        $model = $this->_coreRegistry->registry('current_job');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('jobs_');
        $sourceset = $form->addFieldset('source_fieldset', ['legend' => __('Translation Job Details')]);
		if ($model->getId()) {
            $sourceset->addField('id', 'hidden', ['name' => 'id']);
        }
		/*$sourceset->addField('memoryid', 'hidden', ['name' => 'memoryid']);*/
        unset($storeViewArray[0]);        
		
		$sourceset->addField(
            'sourcelanguage',
            'select',
            [
                'name' => 'sourcelanguage',
                'label' => __('Source Language'),
                'title' => __('Source Language'),
                'required' => true,
                'values' => $this->_importoptions->toOptionArray(),
            ]
        );

        $sourceset->addField(
            'sourcestore',
            'select',
            [
                'name' => 'sourcestore',
                'label' => __('Source Store View'),
                'title' => __('Source Store View'),
                'required' => true,
                'values' => $storeViewArray
            ]
        );

        $sourceset1 = $form->addFieldset('source_fieldset1', ['legend' => __('')]);
        //unset($storeViewArray[1]);
		$sourceset1->addField(
            'targetlanguage',
            'select',
            [
                'name' => 'targetlanguage',
                'label' => __('Target Language'),
                'title' => __('Target Language'),
                'required' => true,
                'values' => $this->_importoptions->sourcetoOptionArray(),
            ]
        );
         
        $sourceset1->addField(
            'targetstore',
            'select',
            [
                'name' => 'targetstore',
                'label' => __('Target Store View'),
                'title' => __('Target Store View'),
                'required' => true,
                'values' => $storeViewArray
            ]
        );

        $sourceset2 = $form->addFieldset('source_fieldset2', ['legend' => __('')]);
        $sourceset2->addField(
            'memoryid',
            'select',
            [
                'label' => __('Translation Memory Id'),
                'title' => __('Translation Memory Id'),
                'name' => 'memoryid',
                'required' => true,
                'values' => $this->_importoptions->getMemorylist($model->getData('sourcelanguage'),$model->getData('targetlanguage')),
            ]
        );

        $sourceset3 = $form->addFieldset('source_fieldset3', ['legend' => __('')]);
		$sourceset3->addField(
            'translationjobname',
            'text',
            [
                'label' => __('Translation Job Name'),
                'title' => __('Translation Job Name'),
                'name' => 'translationjobname',
                'required' => true
            ]
        );

		$form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}