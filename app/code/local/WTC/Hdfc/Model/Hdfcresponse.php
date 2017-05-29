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
 * @package    Hcl_Hdfc
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Secureebs Configuration Model
 *
 * @category   Mage
 * @package    WTC_Hdfc
 * @name       WTC_Hdfc_Model_Hdfcresponse
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class WTC_Hdfc_Model_Hdfcresponse extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("hdfc/hdfcresponse");

    }
	
	public function loadHdfcModel()
	{
		return Mage::getModel('hdfc/hdfcresponse');
	}
	
	public function saveTrackId($trackId)
	{
		$hdfcModel = $this->loadHdfcModel();
		$hdfcModel->setTrackid($trackId);
		$hdfcModel->save();
		return $trackId;
	}
	
	public function savePaymentData($data)
	{
		$paymentid = $data['paymentid'];
		$result = $data['result'];
		$auth = $data['auth'];
		$avr = $data['avr'];
		$ref = $data['ref'];
		$tranid = $data['tranid'];
		$postdate = $data['postdate'];
		$restrackid = $data['trackid'];
		$amt = $data['amt'];
		
		$hdfcModel = $this->loadHdfcModel();
		$collection = $hdfcModel->getCollection();
		foreach($collection as $pgDetail)
		{
			$trackId = $pgDetail->getTrackid();
			if($trackId == $restrackid)
			{
				$pgDetail->setPaymentid($paymentid);
				$pgDetail->setResult($result);
				$pgDetail->setAuth($auth);
				$pgDetail->setAvr($avr);
				$pgDetail->setRef($ref);
				$pgDetail->setTranid($tranid);
				$pgDetail->setPostdate($postdate);
				$pgDetail->setTrackid($restrackid);
				$pgDetail->setAmt($amt);
				$pgDetail->save();
			}
		}
	}
}
	 