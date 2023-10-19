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

namespace Choreo\Translation\Block\Adminhtml\Jobs;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    
    public $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_jobs';
        $this->_blockGroup = 'Choreo_Translation';

        parent::_construct();

        $this->buttonList->remove('save');       
        $this->buttonList->add(
            'next',
            [
                'class' => 'next-button primary',
                'label' => __('Next'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => '', 'target' => '#edit_form']],
                ]
            ],
            20
        );
        $this->buttonList->add(
            'save',
            [
                'class' => 'save-button primary',
                'label' => __('Create Job'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            20
        );
        
    }

    public function getHeaderText()
    {
        $item = $this->_coreRegistry->registry('current_job');
        if ($item->getId()) {
            return __("Edit Item '%1'", $this->escapeHtml($item->getName()));
        } else {
            return __('New Item');
        }
    }
}
