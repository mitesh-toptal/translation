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

class Save extends \Choreo\Translation\Controller\Adminhtml\Jobs
{
    public function execute()
    {
		$data = $this->getRequest()->getPostValue();		
		//$uploadfile = $this->createtranslator->createDom($data);
		$uploadfile = $this->createtranslator->createJsonFile($data);
		$xml_downloadlink = $uploadfile['file_link'];		
		$jobdata = array();
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Choreo\Translation\Model\Jobs');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
				$model->setData($data);
            }else{
				$jobdata['type_name'] = 'All';
                $jobdata['sourcestore'] = $data['sourcestore'];
		        $jobdata['sourcelanguage'] = $data['sourcelanguage']; 
		        $jobdata['targetstore'] = $data['targetstore']; 
		        $jobdata['targetlanguage'] = $data['targetlanguage']; 
		        $jobdata['memoryid'] = $uploadfile['memoryid'];
		        $jobdata['translationjobname'] = $data['translationjobname'];
		        $jobdata['status'] = 'Not yet started';
		        $jobdata['file_id'] = $uploadfile['file_id'];
		        $jobdata['job_number'] = $uploadfile['file_id'];
		        $jobdata['project_id'] = $uploadfile['project_id'];
				$model->setData($jobdata);
			}
            try {
                $model->save();
                $this->saveProducts($model, $data);
                $this->messageManager->addSuccess(__('The job has been saved successfully!'));
				$this->messageManager->addSuccess(__('Please check JSON file. <a href="%1" target="_blank">click here</a>.',$xml_downloadlink));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                $this->_coreSession->unsSelectedProductsArray();
                return $resultRedirect->setPath('*/*/index');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the job.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/index');
    }
	public function saveProducts($model, $data){
		$job_id = $model->getId();
		$connection  = $this->resourceConnection->getConnection();
		if(isset($data['products']) && $data['products'] != ''){
		    $products = $data['products'];
		    if($products != ''){
		    	$products_arr = explode("&",$products);
		    	$product_table = $connection->getTableName('choreo_product_attachment_rel');
		    	foreach($products_arr as $key=>$id){
		    	    $insert_data = [
                        'job_id' =>$job_id,
                        'product_id' => $id
                    ];
		    		$connection->insert($product_table, $insert_data);
		    		$insert_data = array();
		    	}
		    }
		}
		if(isset($data['categories']) && $data['categories'] != ''){
		   $categories = $data['categories'];
			$cat_table = $connection->getTableName('choreo_category_attachment_rel');
			$categories_arr = array_filter(explode(",",$categories));
			foreach($categories_arr as $key=>$id){
			    $insert_data = [
                    'job_id' =>$job_id,
                    'cate_id' => $id
                ];
				$connection->insert($cat_table, $insert_data);
				$insert_data = array();
			}
		}
		if(isset($data['pages']) && $data['pages'] != ''){
		    $pages = $data['pages'];
			$page_table = $connection->getTableName('choreo_page_attachment_rel');
			$pages_arr = explode("&",$pages);
			foreach($pages_arr as $key=>$id){
			    $insert_data = [
                    'job_id' =>$job_id,
                    'page_id' => $id
                ];
				$connection->insert($page_table, $insert_data);
				$insert_data = array();
			}
		}
		if(isset($data['blocks']) && $data['blocks'] != ''){
		    $blocks = $data['blocks'];
			$block_table = $connection->getTableName('choreo_block_attachment_rel');
			$blocks_arr = explode("&",$blocks);
			foreach($blocks_arr as $key=>$id){
			    $insert_data = [
                    'job_id' =>$job_id,
                    'block_id' => $id
                ];
				$connection->insert($block_table, $insert_data);
				$insert_data = array();
			}
		}
	}
}