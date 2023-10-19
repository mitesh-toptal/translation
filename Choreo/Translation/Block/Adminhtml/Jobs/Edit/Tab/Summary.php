<?php
namespace Choreo\Translation\Block\Adminhtml\Jobs\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Choreo\Translation\Model\Editformlan;
class Summary extends Generic implements TabInterface
{
    protected $storeRepository;
    protected $selectedProduct;
    protected $selectedBlock;
    protected $selectedPages;
    protected $_template = 'jobs/summary.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Choreo\Translation\Helper\Data $helper,
		Editformlan $importoptions,
        array $data = [],
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Choreo\Translation\Block\Adminhtml\Jobs\Edit\Tab\Products $selectedProduct,
        \Choreo\Translation\Block\Adminhtml\Jobs\Edit\Tab\Blocks $selectedBlock,
        \Choreo\Translation\Block\Adminhtml\Jobs\Edit\Tab\Pages $selectedPages
    ) {
        $this->_systemStore = $systemStore;
        $this->_coreRegistry = $registry;
		$this->_helper = $helper;
		$this->_importoptions = $importoptions;
        $this->storeRepository = $storeRepository;
        $this->selectedProduct = $selectedProduct;
        $this->selectedBlock = $selectedBlock;
        $this->selectedPages = $selectedPages;
        parent::__construct($context, $registry, $formFactory, $data);
    }
	public function getTabLabel()
    {
        return __('6. Summary');
    }
     public function getTabTitle()
    {
        return __('6. Summary');
    } 
    
    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getSelectedProducts(){
        return $this->selectedProduct->getSelectedProducts();
    }
    public function getSelectedPages(){
        return $this->selectedPages->getSelectedPages();
    }    
    public function getSelectedBlocks(){
        return $this->selectedBlock->getSelectedBlocks();
    }   

}