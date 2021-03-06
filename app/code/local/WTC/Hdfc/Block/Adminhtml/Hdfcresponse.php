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

 * @name       WTC_Hdfc_Block_Adminhtml_Hdfcresponse

 * @author     Vinay Sikarwar <shop@webtechnologycodes.com>

*/


class WTC_Hdfc_Block_Adminhtml_Hdfcresponse extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_hdfcresponse";
	$this->_blockGroup = "hdfc";
	$this->_headerText = Mage::helper("hdfc")->__("Hdfc Payment Manager");
	$this->_addButtonLabel = Mage::helper("hdfc")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}