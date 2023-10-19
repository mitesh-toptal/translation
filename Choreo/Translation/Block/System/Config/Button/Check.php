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
 
namespace Choreo\Translation\Block\System\Config\Button;
use Magento\Framework\App\Config\ScopeConfigInterface;
class Check extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to block template
     */
    const CHECK_TEMPLATE = 'system/config/button/check.phtml';
    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::CHECK_TEMPLATE);
        }
        return $this;
    }
    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
           [
                'button_label' => __($originalData['button_label']),
                'api_key_url' => $originalData['api_key_url'],
                'api_key_id' => $originalData['api_key_id'],
                'intern_url' => $this->getUrl($originalData['button_url']),
                'load_config_url' => $this->getUrl($originalData['load_config_url']),
                'html_id' => $element->getHtmlId(),
            ]
        );
        return $this->_toHtml();
    }
}