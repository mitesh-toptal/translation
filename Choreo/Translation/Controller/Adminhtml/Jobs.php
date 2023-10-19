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

namespace Choreo\Translation\Controller\Adminhtml;

abstract class Jobs extends \Magento\Backend\App\Action
{
    protected $coreRegistry;
    protected $resultForwardFactory;
    protected $resultPageFactory;
    protected $_downloader;
    protected $_directory;
    protected $jobFactory;
    protected $request;
    protected $_filesystem;
    protected $productRepository;
    protected $categoryRepository;
    protected $pageFactory;
    protected $blockFactory;
    protected $messageManager;
    protected $storeRepository;
    protected $pageRepositoryInterface;
    protected $blockRepositoryInterface;
    protected $searchCriteriaBuilder;
    protected $_coreSession;
    protected $resultJsonFactory;
    protected $publisher;
    protected $categoryFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Choreo\Translation\Model\Createtranslator $createtranslator,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\DirectoryList $directory,
        \Choreo\Translation\Model\JobsFactory $jobFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\Category $categoryRepository,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepositoryInterface,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\MessageQueue\Publisher $publisher,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resourceConnection   = $resourceConnection;
        $this->createtranslator     = $createtranslator;
        $this->resultPageFactory    = $resultPageFactory;
        $this->_downloader          = $fileFactory;
        $this->directory            = $directory;
        $this->jobFactory           = $jobFactory;
        $this->request              = $request;
        $this->_filesystem          = $filesystem;
        $this->productRepository    = $productRepository;
        $this->categoryRepository   = $categoryRepository;
        $this->pageFactory          = $pageFactory;
        $this->blockFactory         = $blockFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->storeRepository = $storeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->blockRepositoryInterface = $blockRepositoryInterface; 
        $this->_coreSession = $coreSession;  
        $this->resultJsonFactory = $resultJsonFactory;     
        $this->publisher = $publisher; 
        $this->categoryFactory = $collecionFactory;   
    }
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Choreo_Translation::jobs')->_addBreadcrumb(__('Jobs'), __('Jobs'));
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Choreo_Translation::jobs');
    }
}
