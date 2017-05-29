<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_hdfc
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * hdfc Standard Front Controller
 *
 * @category   WTC
 * @package    WTC_Hdfc
 * @name       WTC_Hdfc_StandardController
 * @copyright  Copyright (c) 2014-2015 Web Technology Codes.
 * @author     Vinay Sikarwar <shop@webtechnologycodes.com>
*/

class WTC_Hdfc_Adminhtml_HdfcresponseController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("hdfc/hdfcpayment")->_addBreadcrumb(Mage::helper("adminhtml")->__("Hdfcpayment  Manager"),Mage::helper("adminhtml")->__("Hdfcpayment Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Hdfc"));
			    $this->_title($this->__("Manager Hdfcpayment"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Hdfc"));
				$this->_title($this->__("Hdfcpayment"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("hdfc/hdfcpayment")->load($id);
				if ($model->getId()) {
					Mage::register("hdfcpayment_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("hdfc/hdfcpayment");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Hdfcpayment Manager"), Mage::helper("adminhtml")->__("Hdfcpayment Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Hdfcpayment Description"), Mage::helper("adminhtml")->__("Hdfcpayment Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("hdfc/adminhtml_hdfcpayment_edit"))->_addLeft($this->getLayout()->createBlock("hdfc/adminhtml_hdfcpayment_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("hdfc")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Hdfc"));
		$this->_title($this->__("Hdfcpayment"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("hdfc/hdfcpayment")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("hdfcpayment_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("hdfc/hdfcpayment");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Hdfcpayment Manager"), Mage::helper("adminhtml")->__("Hdfcpayment Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Hdfcpayment Description"), Mage::helper("adminhtml")->__("Hdfcpayment Description"));


		$this->_addContent($this->getLayout()->createBlock("hdfc/adminhtml_hdfcpayment_edit"))->_addLeft($this->getLayout()->createBlock("hdfc/adminhtml_hdfcpayment_edit_tabs"));

		$this->renderLayout();

		}
		
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("hdfc/hdfcpayment")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Hdfcpayment was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setHdfcpaymentData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setHdfcpaymentData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("hdfc/hdfcpayment");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("hdfc/hdfcpayment");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'hdfcresponse.csv';
			$grid       = $this->getLayout()->createBlock('hdfc/adminhtml_hdfcresponse_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'hdfcresponse.xml';
			$grid       = $this->getLayout()->createBlock('hdfc/adminhtml_hdfcresponse_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
