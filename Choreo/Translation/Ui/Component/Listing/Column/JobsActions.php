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

namespace Choreo\Translation\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class JobsActions extends Column
{
    /** Url path */
    const LILT_PREVIEW_FILE     = 'choreo_translation/jobs/preview';
    const LILT_TRANSLATION_FILE = 'choreo_translation/jobs/translation';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $downloadUrl;
    private $previewUrl;
    private $translationUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $previewUrl = self::LILT_PREVIEW_FILE,
        $translationUrl = self::LILT_TRANSLATION_FILE
    ) {
        $this->urlBuilder     = $urlBuilder;
        $this->previewUrl     = $previewUrl;
        $this->translationUrl = $translationUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {

                    if ($item['status'] == "Translation Done" || $item['status'] == "Translations Partially applied") {
                        $item[$name]['preview'] = [
                            'href'  => $this->urlBuilder->getUrl($this->previewUrl, ['job_number' => $item['job_number']]),
                            'label' => __('Preview'),
                        ];
                        $item[$name]['apply'] = [
                            'href'  => $this->urlBuilder->getUrl($this->translationUrl, ['job_number' => $item['job_number']]),
                            'label' => __('Apply'),
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
