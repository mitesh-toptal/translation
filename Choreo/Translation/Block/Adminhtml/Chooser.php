<?php namespace Choreo\Translation\Block\Adminhtml;

class Chooser extends \Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser
{
    protected function _elementHtml()
    {
        $element = $this->_element;
        $htmlId  = $element->getId();
        $data    = $element->getData();

        $data['after_element_js']   = $this->_afterElementJs();
        $data['after_element_html'] = $this->_afterElementHtml();
        $data['readonly']           = 'readonly';

        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $elementFactory = $objectManager->get('\Magento\Framework\Data\Form\Element\Factory');

        $htmlItem = $elementFactory->create('text', ['data' => $data]);
        $htmlItem
            ->setId("{$htmlId}")
            ->setForm($element->getForm())
            ->addClass('required-entry')
            ->addClass('entities');

        $return = <<<HTML
            <div id="{$htmlId}-container" class="chooser_container">
                {$htmlItem->getElementHtml()}
            </div>
HTML;

        return $return;
    }

    protected function _afterElementHtml()
    {
        $element = $this->_element;
        $htmlId  = $element->getId();

        $return = <<<HTML
            <a href="javascript:void(0)" onclick="MultiCategoryChooser.displayChooser('{$htmlId}-container')" class="widget-option-chooser" title="{__('Open Chooser')}">
                <img src="{$this->getViewFileUrl('images/rule_chooser_trigger.gif')}" alt="{__('Open Chooser')}" />
            </a>
            <a href="javascript:void(0)" onclick="MultiCategoryChooser.hideChooser('{$htmlId}-container')" title="{__('Apply')}">
                <img src="{$this->getViewFileUrl('images/rule_component_apply.gif')}" alt="{__('Apply')}">
            </a>

            <div class="chooser"></div>
HTML;

        return $return;
    }

    protected function _afterElementJs()
    {
        $chooserUrl = $this->getUrl('adminhtml/widget_instance/categories', []);
        $element    = $this->_element;
        $htmlId     = $element->getId();

        $return = <<<HTML
            <script>
                require([
                    'jquery',
                    'Magento_Ui/js/modal/alert',
                    "prototype"
                ], function (jQuery, alert) {
                    var MultiCategoryChooser = {
                        displayChooser : function(chooser) {
                            chooser  = $(chooser).down('div.chooser');
                            entities = chooser.up('div.chooser_container').down('input[type="text"].entities').value;
                            postParameters = {selected: entities};
                            url = '{$chooserUrl}';

                            if (chooser && url) {
                                if (chooser.innerHTML == '') {
                                    new Ajax.Request(url, {
                                        method  : 'post',
                                        parameters : postParameters,
                                        onSuccess  : function(transport) {
                                            try {
                                                if (transport.responseText) {
                                                    Element.insert(chooser, transport.responseText);
                                                    chooser.removeClassName('no-display');
                                                    chooser.show();
                                                }
                                            } catch (e) {
                                                alert({
                                                   content: 'Error occurs during loading chooser.'
                                                });
                                            }
                                        }
                                    });
                                } else {
                                    chooser.removeClassName('no-display');
                                    chooser.show();
                                }
                            }
                       },
                        hideChooser : function(chooser) {
                            chooser = $(chooser).down('div.chooser');
                            if (chooser) {
                                chooser.addClassName('no-display');
                                chooser.hide();
                            }
                        },
                        checkCategory : function(event) {
                            node    = event.memo.node;
                            container = event.target.up('div.chooser_container');
                            value   = container.down('input[type="text"].entities').value.strip();
                            if (node.attributes.checked) {
                                if (value) ids = value.split(',');
                                else ids = [];
                                if (-1 == ids.indexOf(node.id)) {
                                    ids.push(node.id);
                                    container.down('input[type="text"].entities').value = ids.join(',');
                                }
                            } else {
                                ids = value.split(',');
                                while (-1 != ids.indexOf(node.id)) {
                                    ids.splice(ids.indexOf(node.id), 1);
                                    container.down('input[type="text"].entities').value = ids.join(',');
                                }
                            }
                        }
                  }

                    window.MultiCategoryChooser = MultiCategoryChooser;
                    jQuery(function() {
                        var container = $('{$htmlId}-container');
                        if (container) {
                            container.up(0).down('.control-value').hide();
                        }

                        Event.observe(document, 'node:changed', function(event){
                            MultiCategoryChooser.checkCategory(event);
                        });
                        Event.observe(document, 'category:beforeLoad', function(event) {
                            container = event.target.up('div.chooser_container');
                            value   = container.down('input[type="text"].entities').value.strip();
                            event.memo.treeLoader.baseParams.selected = value;
                        });
                    });
                });
            </script>
HTML;

        return $return;
    }
}
