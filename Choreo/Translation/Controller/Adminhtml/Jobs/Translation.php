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

use Magento\Framework\App\Filesystem\DirectoryList;

class Translation extends \Choreo\Translation\Controller\Adminhtml\Jobs
{
    public function execute()
    {
        try {
            $jobNumber = (int) $this->request->getParam('job_number');
            $this->publisher->publish('choreo_translation_functionality', $jobNumber);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $this->messageManager->addSuccessMessage('Translation has been added in consumer queue runner');

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('choreo_translation/jobs/index');
        return $resultRedirect;        
    }
}
