<?php
namespace Choreo\Translation\Block\Adminhtml\Jobs;

use Magento\Framework\App\Filesystem\DirectoryList;

class Preview extends \Magento\Backend\Block\Template
{
    protected $_template = 'jobs/preview.phtml';
    protected $request;
    protected $jobFactory;
    protected $_filesystem;
    protected $importoptions;
    protected $urlBuider;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Choreo\Translation\Model\JobsFactory $jobFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Choreo\Translation\Model\Editformlan $importoptions,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->request       = $request;
        $this->jobFactory    = $jobFactory;
        $this->_filesystem   = $filesystem;
        $this->importoptions = $importoptions;
        $this->urlBuilder    = $urlBuilder;
        parent::__construct($context);
    }

    public function getJob($jobNumber)
    {
        $jobs = $this->jobFactory->create()->getCollection()->addFieldToFilter('job_number', $jobNumber);
        return $jobs;
    }

    public function getJobData()
    {
        $jobNumber = $this->request->getParam('job_number');
        $jobs      = $this->getJob($jobNumber);
        $dataArray = array();
        if (count($jobs)) {
            $originalFileName = '';
            foreach ($jobs->getData() as $key => $value) {
                $dataArray['source-language'] = $this->getLanguageName($value['sourcelanguage']);
                $dataArray['target-language'] = $this->getLanguageName($value['targetlanguage']);
                $originalFile                 = $value['translationjobname'] . ".json+html";
            }

            $mediaPath          = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
            $translatedFileName = $jobNumber . ".json+html";
            $filePath           = $mediaPath . "/jobs/";

            
             if (file_exists($filePath . $translatedFileName) && file_exists($filePath . $originalFile)) {                

                $originalFileContent   = file_get_contents($filePath . $originalFile);                
                $translatedFileContent = file_get_contents($filePath . $translatedFileName);

                $originalConvertedPhpArray   = json_decode($originalFileContent);
                $TranslatedConvertedPhpArray = json_decode($translatedFileContent);
                foreach ($TranslatedConvertedPhpArray as $key => $value) {
                    $extractArray                                     = explode("_", $key, 3);
                    $dataArray['data'][$extractArray[0].'_'.$extractArray[1]]['type']      = $extractArray[0];
                    $dataArray['data'][$extractArray[0].'_'.$extractArray[1]]['target_attr'][$extractArray[2]] = $value;                    
                }

                foreach ($originalConvertedPhpArray as $key => $value) {
                    $extractArray                                  = explode("_", $key, 3);
                    $dataArray['data'][$extractArray[0].'_'.$extractArray[1]]['source_attr'][$extractArray[2]] = $value;
                }
            }
        }
        return $dataArray;
    }
    public function getLanguageName($code)
    {
        return $this->importoptions->getLangeaugeName($code);
    }

    public function applyTranslationUrl()
    {
        return $this->urlBuilder->getUrl('*/*/translation', ["job_number" => $this->request->getParam('job_number')]);
    }
}
