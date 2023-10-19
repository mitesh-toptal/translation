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

namespace Choreo\Translation\Controller\Adminhtml\Jobs;

class Productcount extends \Choreo\Translation\Controller\Adminhtml\Jobs
{

    public function execute()
    {
    	$resultJson = $this->resultJsonFactory->create();
    	$response = [];
        if (!empty($this->_coreSession->getSelectedProductsArray())) {
        	$response = ['count' => count($this->_coreSession->getSelectedProductsArray())];            
        } else {
        	$response = ['count' => 0];            
        }
        return $resultJson->setData($response);
    }
}
