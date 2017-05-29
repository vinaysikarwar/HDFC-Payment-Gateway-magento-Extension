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
 * @category   Mage * @package    WTC_Hdfc * @name       WTC_Hdfc_Block_Standard_Response * @author     Vinay Sikarwar <shop@webtechnologycodes.com>
*/

class WTC_Hdfc_Block_Standard_Response extends Mage_Core_Block_Template
{
	protected function _toHtml()
    {
		print_r($_POST);
		$strResponseIPAdd = getenv('REMOTE_ADDR');
		echo '<br/>test'.$ResErrorText= isset($_POST['ErrorText']) ? $_POST['ErrorText'] : ''; 	//Error Text/message
		echo '<br/>'.$ResPaymentId = isset($_POST['paymentid']) ? $_POST['paymentid'] : '';	//Payment Id
		echo '<br/>'.$ResTrackID = isset($_POST['trackid']) ? $_POST['trackid'] : '';        //Merchant Track ID
		echo '<br/>'.$ResErrorNo = isset($_POST['Error']) ? $_POST['Error'] : '';            //Error Number
		echo '<br/>'.$ResResult = isset($_POST['result']) ? $_POST['result'] : '';           //Transaction Result
		echo '<br/>'.$ResPosdate = isset($_POST['postdate']) ? $_POST['postdate'] : '';      //Postdate
		echo '<br/>'.$ResTranId = isset($_POST['tranid']) ? $_POST['tranid'] : '';           //Transaction ID
		echo '<br/>'.$ResAuth = isset($_POST['auth']) ? $_POST['auth'] : '';                 //Auth Code		
		echo '<br/>'.$ResAVR = isset($_POST['avr']) ? $_POST['avr'] : '';                    //TRANSACTION avr					
		echo '<br/>'.$ResRef = isset($_POST['ref']) ? $_POST['ref'] : '';                    //Reference Number also called Seq Number
		echo '<br/>'.$ResAmount = isset($_POST['amt']) ? $_POST['amt'] : '';                 //Transaction Amount
		echo '<br/>'.$Resudf1 = isset($_POST['udf1']) ? $_POST['udf1'] : '';                  //UDF1
		echo '<br/>'.$Resudf2 = isset($_POST['udf2']) ? $_POST['udf2'] : '';                  //UDF2
		echo '<br/>'.$Resudf3 = isset($_POST['udf3']) ? $_POST['udf3'] : '';                  //UDF3
		echo '<br/>'.$Resudf4 = isset($_POST['udf4']) ? $_POST['udf4'] : '';                  //UDF4
		echo '<br/>'.$Resudf5 = isset($_POST['udf5']) ? $_POST['udf5'] : '';                  //UDF5		
		die;

		/* Check whether the IP Address from where response is received is PG IP */
		//if ($strResponseIPAdd == "221.134.101.174" && $strResponseIPAdd == "221.134.101.169" && $strResponseIPAdd == "198.64.129.10" && $strResponseIPAdd == "198.64.133.213")
		if ($strResponseIPAdd == "221.134.101.175" && $strResponseIPAdd == "221.134.101.166" && $strResponseIPAdd == "221.134.101.187")	
		{
			
			$failedUrl = Mage::getBaseUrl().'checkout/onepage/failure?ResError=--IP MISSMATCH-- Response IP Address is: '.$strResponseIPAdd;
			header('location:'.$failedUrl);
			exit(0);
			
		}
		else
		{
			
			$responseURL = Mage::getBaseUrl().'hdfc/standard/response';
			$ResResult = $_GET['ResResult'];
			$ResTrackId = $_GET['ResTrackId'];
			$ResAmount = round($_GET['ResAmount']);
			$ResPaymentId = $_GET['ResPaymentId'];
			$ResRef = $_GET['ResRef'];
			$ResTranId = $_GET['ResTranId'];
			$ResError = $_GET['ResError'];
			$data = 'resResult:<b>'.$ResResult.'</b>, '.'ResTrackId:<b>'.$ResTrackId.'</b>, '.'ResAmount:<b>'.$ResAmount.'</b>, '.'ResPaymentId:<b>'.$ResPaymentId.'</b>, '.'ResRef:<b>'.$ResRef.'</b>, '.'ResTranId:<b>'.$ResTranId.'</b>, '.'ResError:Hashing Response Successful';
			$order = $this->getOrder();
			//$paymentData = $_GET;
			$grand_total = round($order->getGrandTotal());
			if($grand_total != $ResAmount)
			{
				$failedUrl = Mage::getBaseUrl().'checkout/onepage/failure?ResError=--Amount MISSMATCH-- Response Amount is: '.$ResAmount;
				$errorMsg = Mage::helper('hdfc')->__('There was an error occurred during paying process.');

				if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
					$order->addStatusToHistory($order->getStatus(), $errorMsg);
					$order->cancel();
					$order->save();
				}
				$failureUrl = Mage::getBaseUrl().'checkout/onepage/failure';
				header('location:'.$failureUrl);
				exit(0);
			}
			$hdfcModel = Mage::getModel('hdfc/hdfcresponse')->savePaymentData($_GET);
			
			if($ResResult == 'CAPTURED')
			{
				$session = Mage::getSingleton('checkout/session');
				$session->setQuoteId($session->getHdfcStandardQuoteId());
				$session->unsHdfcStandardQuoteId();
				if (!$order->getId())
				{
					$this->norouteAction();
					return;
				}
				$order->addStatusToHistory(
					$order->getStatus(),
					Mage::helper('hdfc')->__('Customer successfully returned from hdfc with following details: '.$data)
				);
				
				$order->save();
				$order->sendNewOrderEmail();
				
				$successUrl = Mage::getBaseUrl().'checkout/onepage/success?TrackId='.$ResTrackId;
				header('location:'.$successUrl);		
				exit(0);
			}
			else
			{
				$errorMsg = Mage::helper('hdfc')->__('There was an error occurred during paying process.');

				if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
					$order->addStatusToHistory($order->getStatus(), $errorMsg);
					$order->cancel();
					$order->save();
				}
				$failureUrl = Mage::getBaseUrl().'checkout/onepage/failure';
				header('location:'.$failureUrl);
				exit(0);
			}
		
		}
		
		
		
	}	
}