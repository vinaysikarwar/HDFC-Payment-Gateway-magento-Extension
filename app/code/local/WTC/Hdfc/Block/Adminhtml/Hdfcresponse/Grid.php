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

 * @package    WTC_Hdfc

 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)

 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 */

/**

 * Failure Response from Secureebs

 *

 * @category   Mage

 * @package    WTC_Hdfc

 * @name       WTC_Hdfc_Block_Adminhtml_Hdfcresponse_Grid

 * @author     Vinay Sikarwar <shop@webtechnologycodes.com>

*/



class WTC_Hdfc_Block_Adminhtml_Hdfcresponse_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("hdfcresponseGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("hdfc/hdfcresponse")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("hdfc")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("error_text", array(
				"header" => Mage::helper("hdfc")->__("Error Text"),
				"index" => "error_text",
				));
				$this->addColumn("error", array(
				"header" => Mage::helper("hdfc")->__("Error"),
				"index" => "error",
				));
				$this->addColumn("trackid", array(
				"header" => Mage::helper("hdfc")->__("Track ID"),
				"index" => "trackid",
				));
				$this->addColumn("paymentid", array(
				"header" => Mage::helper("hdfc")->__("Payment Id"),
				"index" => "paymentid",
				));
				$this->addColumn("result", array(
				"header" => Mage::helper("hdfc")->__("Result"),
				"index" => "result",
				));
				$this->addColumn("postdate", array(
				"header" => Mage::helper("hdfc")->__("Post Date"),
				"index" => "postdate",
				));
				$this->addColumn("tranid", array(
				"header" => Mage::helper("hdfc")->__("Transaction Id"),
				"index" => "tranid",
				));
				$this->addColumn("auth", array(
				"header" => Mage::helper("hdfc")->__("Auth"),
				"index" => "auth",
				));
				$this->addColumn("avr", array(
				"header" => Mage::helper("hdfc")->__("avr"),
				"index" => "avr",
				));
				$this->addColumn("ref", array(
				"header" => Mage::helper("hdfc")->__("Ref"),
				"index" => "ref",
				));
				$this->addColumn("amt", array(
				"header" => Mage::helper("hdfc")->__("Amount"),
				"index" => "amt",
				));
				
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_hdfc Payment', array(
					 'label'=> Mage::helper('hdfc')->__('Remove Hdfc payment'),
					 'url'  => $this->getUrl('*/adminhtml_hdfcpayment/massRemove'),
					 'confirm' => Mage::helper('hdfc')->__('Are you sure?')
				));
			return $this;
		}
			

}