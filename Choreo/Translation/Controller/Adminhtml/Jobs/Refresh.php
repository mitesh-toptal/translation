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

class Refresh extends \Magento\Backend\App\Action
{
    protected $jobFactory;
    protected $_helper;
    protected $resultRedirectFactory;

    public function __construct(
        \Choreo\Translation\Model\JobsFactory $jobFactory,
        \Magento\Backend\App\Action\Context $context,
        \Choreo\Translation\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
    ) {
        parent::__construct($context);
        $this->jobFactory            = $jobFactory;
        $this->_helper               = $helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function execute()
    {
        $jobCollection = $this->jobFactory->create()->getCollection();
        $jobCollection->addFieldToFilter('status', array('nin' => array('Translation Done', 'Translations applied')));
        if ($jobCollection->getSize()) {
            foreach ($jobCollection as $job) {
                if ($job->getJobNumber()) {
                    $statusResponses = $this->_helper->getProjectStatus($job->getProjectId());
                    foreach ($statusResponses as $statusResponse) {
                        if (isset($statusResponse['id']) && $job->getProjectId() == $statusResponse['id']) {
                            if (md5($statusResponse['state']) === md5('backlog')) {
                                $statusCode = "Not yet started";
                                $job->setStatus($statusCode)->save();
                                break;
                            } elseif (md5($statusResponse['state']) === md5('inProgress')) {
                                $statusCode = "Translation in Progress";
                                $job->setStatus($statusCode)->save();
                                break;
                            } elseif (md5($statusResponse['state']) === md5('inReview')) {
                                $statusCode = "Translation in Review";
                                $job->setStatus($statusCode)->save();
                                break;
                            } elseif (md5($statusResponse['state']) === md5('done')) {
                                if (!empty($statusResponse['document'])) {
                                    foreach ($statusResponse['document'] as $documentKey => $documentValue) {
                                        if ($documentValue['id'] == $job->getJobNumber()) {
                                            $this->_helper->downloadJsonFile($job->getJobNumber());
                                            $statusCode = "Translation Done";
                                            $job->setStatus($statusCode)->save();
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }

                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('choreo_translation/jobs/index');
        return $resultRedirect;
    }
}
