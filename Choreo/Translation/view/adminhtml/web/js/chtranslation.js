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
define([
    'jquery',
    'uiComponent',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert',
    'ko',
    'uiRegistry',
    'domReady!'
],
function ($, Component,translate, modal, alert,ko,uiRegistry) {
    'use strict';
    $(document).ready(function(){
        // $('#choreotranslation_enable_memories, #jobs_translationjobname').css("pointer-events", "none");
        $('#choreotranslation_enable_memories').css("pointer-events", "none");
        enableDisableNextButton();
        $('#back span').html("Cancel");
        $('#back').attr("title","Cancel");

        $('#jobs_translationjobname').keydown(function (e) {            
            if (e.keyCode == 13) {
                e.preventDefault();
                var value = $(this).val();
                if ( value.length > 0){
                    $('#next').trigger( "click" );    
                }                
                return false;
            }
        });

    });
    function enableDisableNextButton() {
        var jobs_sourcelanguage = $('#jobs_sourcelanguage').val();
        var jobs_sourcestore = $('#jobs_sourcestore').val();
        var jobs_targetlanguage = $('#jobs_targetlanguage').val();
        var jobs_targetstore = $('#jobs_targetstore').val();
        var jobs_memoryid = $('#jobs_memoryid').val();
        var jobs_translationjobname = $('#jobs_translationjobname').val();
        if(jobs_sourcelanguage!=0 && jobs_sourcestore!='' && jobs_targetlanguage!=0 && jobs_targetstore!='' && (jobs_memoryid>0 || jobs_memoryid!=null) && jobs_translationjobname!='')
        {
            $('#next').show('slow');
        }
        else
        {
            $('#next').hide('show');
        }
    }
    Window.keepMultiModalWindow = true;
    var ChtranslationForm = {
        overlayShowEffectOptions : null,
        overlayHideEffectOptions : null,
        modal: null,
        init : function(form, chtranslationConfig){
            if(typeof chtranslationConfig === 'string'){
                chtranslationConfig = JSON.parse(chtranslationConfig);
            }
            var activation = chtranslationConfig.activation;
            if(activation == 0){
                return false;
            }
            
            var languageToCode = chtranslationConfig.languageto;
            var pageid = chtranslationConfig.pageid;
            var translatedFieldsNames = chtranslationConfig.languagefields.split(',');
            var languageToFullName = chtranslationConfig.languagetofullname;
            var translateBtnText = chtranslationConfig.translatebutton;
            var languagefrom = chtranslationConfig.languagefrom;
            var languagefromfullname = chtranslationConfig.languagefromfullname;
            var translateURL = chtranslationConfig.translateURL;
            var storecode = chtranslationConfig.storecode;
            var button = '';
            $.each(translatedFieldsNames, function(index, val) {
                var elId = $('[data-index="'+val+'"] input[type="text"]').attr("id");
                if(!elId){
                    elId = $('[data-index="'+val+'"] textarea').attr("id");
                }
                if(val == "content"){
                    elId = $('[name="'+val+'"]').attr("id");
                }
                elId = "#"+ elId;
                if (languageToCode === 'no-language' || languageToCode === 'null' || languageToCode === 'undefined') {
                    button = "<span style='padding-right: 10px;'><i>" + $.mage.translate.translate('Select Language for this store in System->Config->Translator') + "</i></span>";
                } else {
                    button = '<button id="cht_'+ index +'" title="' + $.mage.translate.translate('Translate to ') + languageToFullName + '" type="button" class="chtranslate cht-translate" onclick="ChtranslationForm._submit(\''+ translateURL +'\',\''+ elId +'\',\''+ languageToCode +'\',\''+ languagefrom +'\',\''+ storecode +'\',\''+ languageToFullName +'\',\''+ languagefromfullname +'\')" ><span>'+ $.mage.translate.translate(translateBtnText) + '</span></button>';
                }
                $(elId).siblings(".cht-translate").remove();
                if ($(elId).parents('.admin__control-wysiwig').length > 0) {
                    $(elId).parents('.admin__control-wysiwig').find('button.action-add-image').hide();
                    $(elId).parents('.admin__control-wysiwig').find('button.add-variable').hide();
                    $(elId).parents('.admin__control-wysiwig').parent().closest('div').siblings(".cht-translate").remove();
                    $(elId).parents('.admin__control-wysiwig').parent().closest('div').after(button);
                } else {
                    $(elId).after(button);
                }
            });
        },
        _submit : function(url,el,languageToCode,languagefrom,storecode,languagetofullname,languagefromfullname){
            var formdata = {
                'langto' : languageToCode,
                'langfrom' : languagefrom,
                'id' : el,
                'storecode' : storecode,
                'languagetofullname' : languagetofullname,
                'languagefromfullname' : languagefromfullname,
                'value': $(el).val()
            };
            $.ajax({
                url : url,
                type: 'POST',
                data : formdata,
                showLoader : true,
                success : this._processResult.bind(this)
            }).fail(function(data){
                result = $.parseJSON(data);
                if( result.value.status === 'fail' ){
                    alert({
                        content : $.mage.translate.translate('Unknown Error!')
                    });
                }
            });
        },
        _processResult : function(transport){           
            var response = transport,e='';          
            try {
                if( response.value.status == 'success' ){
                    this.openDialogWindow(response);
                } else {
                    alert({
                        content : response.value.text
                    });
                }
            } catch (e){
                alert({
                    content : e.message
                });
            }
        },
        openDialogWindow : function (responseData) {
            var popupOverlay = $('#chtranslation-translator');
            var self = this;

            if (this.modal) {
                this.modal.html($(popupOverlay).html());
            } else {
                this.modal = $(popupOverlay).modal({
                    title : 'Choreo Translator',
                    modalClass: 'ch-translate-popup',
                    type: 'popup',
                    firedElementId: responseData.id,
                    elID : responseData.id,
                    buttons: [{
                        text: $.mage.translate.translate('Apply Translate'),
                        click: function () {
                            self.okDialogWindow(this);
                        }
                    }],
                    closed: function () {
                        self.closeDialogWindow(this);
                    }
                });
            }
            if(responseData.value.status=='success'){               
                this.modal.modal('openModal');
                this.modal.find('#translation-lang').html(responseData.value.languagefromfullname);
                this.modal.find('#original-lang').html(responseData.value.languagetofullname);
                this.modal.find('.old-text').val($(responseData.id).val());             
                this.modal.find('.translated-textarea').val(responseData.value.text);
            }else{
                this.modal.modal('openModal');
                this.modal.content('responseData.value.text');
            }
        },
        closeDialogWindow : function(dialogWindow){
            var Windows='';
            this.modal = null;
            if($.isFunction(dialogWindow.closeModal)){
                dialogWindow.closeModal();
            }
            //Windows.overlayShowEffectOptions = null;
            //Windows.overlayHideEffectOptions = null;
        },
        okDialogWindow : function(dialogWindow){
            if( dialogWindow.options.firedElementId ){
                if ($(dialogWindow.options.firedElementId+'_ifr').length) {
                    var editor = $.mage.translate.tinyMCE.get($(dialogWindow.options.firedElementId).attr('id'));
                    if( editor !== null ){
                        editor.execCommand( 'mceSetContent' , true, this.modal.find('.translated-textarea').val() );
                    } else {
                        $(dialogWindow.options.firedElementId).val(this.modal.find('.translated-textarea').val());
                         $(dialogWindow.options.firedElementId).change();
                    }
                } else {
                    $(dialogWindow.options.firedElementId).val(this.modal.find('.translated-textarea').val());
                    $(dialogWindow.options.firedElementId).change();
                }
            }
            this.closeDialogWindow(dialogWindow);
        },
        checkactivation : function(params){
            $('.error_check_icon').remove();
            $('.choreo_check_icon').remove();
            var activation_url = params.internUrl;
            var apiKey = $(params.apiKeyId).val();
            var apiKeyUrl = $(params.apiKeyUrl).val();
            var formdata = {
                'apiKey' : apiKey,
                'apiKeyUrl' : apiKeyUrl
            };
            $.ajax({
                url : activation_url,
                type: 'POST',
                data : formdata,
                showLoader : true,
                success: function(response) {
                    try {
                        if(response.status == 'success'){
                            $('#choreotranslation_activation_check').after(response.check_icon);
                        } else {
                            $('#choreotranslation_activation_check').before(response.error_mass);
                        }
                    } catch (e){
                        alert({
                            content : e.message
                        });
                    }
                }
            }).fail(function(data){
                result = $.parseJSON(data);
                if( result.value.status === 'fail' ){
                    alert({
                        content : $.mage.translate.translate('Unknown Error!')
                    });
                }
            });
        },
        targetlanglist : function(configdata){
            
            var url = configdata.translateURL;

            var creatememory_url = configdata.creatememory;
            var storecode = configdata.storecode;
            var sourcelang = '';
            //$('#choreotranslation_activation_apiendpoint').css("pointer-events", "none");
            $('#ch_memories_enable_languagefrom').html(configdata.languagefromoptionhtml);
            $('#ch_memories_enable_languageto').html(configdata.languagetoptionhtml);
            $('#ch_memories_enable_languagefrom, #jobs_sourcelanguage').change(function() {                
                var current_timespam = new Date().valueOf();
                var sourcelang = $(this).val();
                var formdata = {
                    'sourcelang' : sourcelang,
                    'current_timespam' : current_timespam
                };
                $.ajax({
                    url : url,
                    type: 'POST',
                    data : formdata,
                    showLoader : true,
                    success: function(response) {
                        try {
                            if(response.status == 'success' ){
                                var options = response.target_options;
                                var current_timespam_name = response.current_timespam_name;
                                $('#ch_memories_enable_languageto').html(options);
                                $('#jobs_targetlanguage').html(options);
                                $('#jobs_translationjobname').val(current_timespam_name);
                                enableDisableNextButton();
                            } else {
                                alert({
                                    content : response.error_mass
                                });
                            }
                        } catch (e){
                            alert({
                                content : e.message
                            });
                        }
                    }
                }).fail(function(data){
                    result = $.parseJSON(data);
                    if( result.value.status === 'fail' ){
                        alert({
                            content : $.mage.translate.translate('Unknown Error!')
                        });
                    }
                });
            });
            $('#jobs_targetlanguage').change(function() {
                var sourcelang = $('#jobs_sourcelanguage').val();
                var jobs_sourcestore = $('#jobs_sourcestore').val();
                var targetlang = $(this).val();
                var current_timespam = new Date().valueOf();
                var final_time = '';
                if (configdata.store_name != null){
                    var final_time = configdata.store_name + '-' + sourcelang +'-'+ targetlang+'-'+current_timespam;
                }else{
                    var final_time = window.location.host + '-' + sourcelang +'-'+ targetlang+'-'+current_timespam;
                }
                
                $('#jobs_translationjobname').val(final_time);
                var formdata = {
                    'sourcelang' : sourcelang,
                    'storecode':storecode,
                    'targetlang' : targetlang,
                    'jobs_sourcestore' : jobs_sourcestore,
                    'job_form':1
                };
                $.ajax({
                    url : creatememory_url,
                    type: 'POST',
                    data : formdata,
                    showLoader : true,
                    success: function(response) {
                        try {
                            if(response.status == 'success' ){
                                var memoryid = response.jobmemoryid;
                                var final_time = '';
                                if (configdata.store_name != null){
                                    var final_time = configdata.store_name + '-' + sourcelang +'-'+ targetlang + '-' + response.firstmoryid +'-'+current_timespam;
                                }else{
                                    var final_time = window.location.host + '-' + sourcelang +'-'+ targetlang + '-' + response.firstmoryid +'-'+current_timespam;
                                }

                                $('#jobs_translationjobname').val(final_time);
                                $('#jobs_memoryid').html(memoryid);
                                //$('#choreotranslation_enable_memories').css("pointer-events", "none");
                                enableDisableNextButton();
                            } else {
                                alert({
                                    content : response.error_mass
                                });
                            }
                        } catch (e){
                            alert({
                                content : e.message
                            });
                        }
                    }
                }).fail(function(data){
                    result = $.parseJSON(data);
                    if( result.value.status === 'fail' ){
                        alert({
                            content : $.mage.translate.translate('Unknown Error!')
                        });
                    }
                });
            });

            $('#jobs_memoryid').change(function() {
                var sourcelang = $('#jobs_sourcelanguage').val();                
                var targetlang = $('#jobs_targetlanguage').val();
                var memoryId = $(this).val();
                var current_timespam = new Date().valueOf();               

                var final_time = '';
                if (configdata.store_name != null){
                    var final_time = configdata.store_name + '-' + sourcelang +'-'+ targetlang+'-'+ memoryId +'-'+ current_timespam;
                }else{
                    var final_time = window.location.host + '-' + sourcelang +'-'+ targetlang+'-'+ memoryId +'-'+ current_timespam;
                }

                $('#jobs_translationjobname').val(final_time); 
            });


            $('#ch_memories_enable_languageto').change(function() {
                var targetlang = $(this).val();
                var sourcelang = $('#ch_memories_enable_languagefrom').val();
                //var memory_json = $('#ch_memories_enable_memories_json').val();
                var formdata = {
                    'sourcelang' : sourcelang,
                    'storecode':storecode,
                    'targetlang' : targetlang,
                    'job_form':0
                };
                $.ajax({
                    url : creatememory_url,
                    type: 'POST',
                    data : formdata,
                    showLoader : true,
                    success: function(response) {
                        try {
                            if(response.status == 'success' ){
                                var memoryid = response.memoryid;
                                $('#ch_memories_enable_memories').html(memoryid);
                                //$('#choreotranslation_enable_memories').css("pointer-events", "none");
                            } else {
                                alert({
                                    content : response.error_mass
                                });
                            }
                        } catch (e){
                            alert({
                                content : e.message
                            });
                        }
                    }
                }).fail(function(data){
                    result = $.parseJSON(data);
                    if( result.value.status === 'fail' ){
                        alert({
                            content : $.mage.translate.translate('Unknown Error!')
                        });
                    }
                });
            });         
        }
    };
    window.ChtranslationForm = ChtranslationForm;
    return {
        ChtranslationForm : ChtranslationForm
    };
});