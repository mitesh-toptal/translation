<?php
namespace Choreo\Translation\Ui\Component\Listing\Column;

class Sourcelang extends \Magento\Ui\Component\Listing\Columns\Column {   
    protected $importoptions;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [],
        \Choreo\Translation\Model\Editformlan $importoptions
    ){
        $this->importoptions = $importoptions;  
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            $langOptions = $this->importoptions->toLangArray();
            foreach ($dataSource['data']['items'] as & $item) {                
                $item['sourcelanguage'] = $langOptions[$item['sourcelanguage']];
            }            
        }
        return $dataSource;
    }
}