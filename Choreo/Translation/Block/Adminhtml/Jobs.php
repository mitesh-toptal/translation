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

namespace Choreo\Translation\Block\Adminhtml;

class Jobs extends \Magento\Backend\Block\Widget\Grid\Container
{
    
    public function _construct()
    {
        $this->_controller = 'jobs';
        $this->_headerText = __('Jobs');
        $this->_addButtonLabel = __('Add New Job');
        parent::_construct();
    }
}
