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
 * Redirect to Secureebs
 *
 * @category   Mage * @package    WTC_Hdfc * @name       WTC_Hdfc_Block_Standard_Redirect * @author     Vinay Sikarwar <shop@webtechnologycodes.com>
 */

class WTC_Hdfc_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {		$url = Mage::getSingleton('hdfc/config')->getPaymentGatewayUrl();		$transportalId = Mage::getSingleton('hdfc/config')->getTransportalId();		$password = Mage::getSingleton('hdfc/config')->getPassword();		if(!empty($url) && !empty($transportalId) && !empty($password)){
		$standard = Mage::getModel('hdfc/standard');
		$form = new Varien_Data_Form();
		$servermode=Mage::getSingleton('hdfc/config')->getServerMode();
		$strResponseIPAdd = getenv('REMOTE_ADDR');
		$order = $this->getOrder();
		$shipping_address = $order->getShippingAddress();
		$address = $shipping_address->getStreet();
		$address1 = $address[0];
		$address2 = $address[1];
		$city = $shipping_address->getCity();
		$region = $shipping_address->getRegion();
		$postcode = $shipping_address->getPostcode();
		$udf1 = $order->getCustomerEmail();
		$udf2 = $address1;
		$udf3 = $address2.','. $city.'-'.$postcode ;
		$udf4 = $region;
		$TranTrackid=$order->getIncrementId(); 
		$TranAmount=$order->getBaseGrandTotal();		
		$ReqTranportalId="id=$transportalId";
		$ReqTranportalPassword="password=$password";
		$ReqAmount="amt=".$TranAmount;
		$ReqTrackId="trackid=".$TranTrackid;
		$ReqCurrency="currencycode=356";
		$ReqLangid="langid=USA";
		$ReqAction="action=1";
		$ReqResponseUrl="responseURL=".Mage::getBaseUrl().'hdfc/standard/response';
		$ReqErrorUrl="errorURL=". Mage::getBaseUrl().'hdfc/standard/response';
		$ReqUdf1=$order->getCustomerEmail();
		$ReqUdf2=$address1;
		$ReqUdf3=$address2.','. $city.'-'.$postcode ;
		$ReqUdf4=$region;
		if($TranTrackid)
		{
			$hdfcModel = Mage::getModel('hdfc/hdfcresponse')->saveTrackId($TranTrackid);
		}
		/*==============================HASHING LOGIC CODE START===========================================*/
		/*Below are the fields/prametres which will use for hashing using (GetSHA256) hashing 
		Algorithm,and need to pass same hashed valued in UDF5 filed only*/
		
		$strhashTID=trim($transportalId); 			 //USE Tranportal ID FIELD Value FOR HASHING 
		$strhashtrackid=trim($TranTrackid);	 //USE Trackid FIELD Value FOR HASHING 
		$strhashamt=trim($TranAmount);  		 //USE Amount FIELD Value FOR HASHING 
		$strhashcurrency=trim("356");			 //USE Currencycode FIELD Value FOR HASHING 
		$strhashaction=trim("1");				 //USE Action code FIELD Value FOR HASHING 
		//Create a Hashing String to Hash
		$str = trim($strhashTID.$strhashtrackid.$strhashamt.$strhashcurrency.$strhashaction);
		//Use hash method which is defined below for Hashing ,It will return Hashed valued of above string
		$hashstring= hash('sha256', $str); 
		$ReqUdf5="udf5=".$hashstring;      // Passed Calculated Hashed Value in UDF5 Field 
		/*==============================HASHING LOGIC CODE END==============================================*/
		
		$param=$ReqTranportalId."&".$ReqTranportalPassword."&".$ReqAction."&".$ReqLangid."&".$ReqCurrency."&".$ReqAmount."&".$ReqResponseUrl."&".$ReqErrorUrl."&".$ReqTrackId."&".$ReqUdf1."&".$ReqUdf2."&".$ReqUdf3."&".$ReqUdf4."&".$ReqUdf5;
		
		$ch = curl_init() or die(curl_error()); 
		curl_setopt($ch, CURLOPT_POST,1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS,$param); 
		curl_setopt($ch, CURLOPT_PORT, 443); // port 443
		curl_setopt($ch, CURLOPT_URL,$url);// here the request is sent to payment gateway 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0); //create a SSL connection object server-to-server
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0); 
		$data1=curl_exec($ch) or die(curl_error());
		curl_close($ch); 
		$response = $data1;
        try
		{
			$index=strpos($response,"!-");
			$ErrorCheck=substr($response, 1, $index-1);//This line will find Error Keyword in response
			
			if($ErrorCheck == 'ERROR')//This block will check for Error in response
			{				
				// here redirecting the error page 
				$failedurl=Mage::getBaseUrl().'checkout/onepage/failure?ResTrackId='.$TranTrackid.'&ResAmount='.$TranAmount.'&ResError='.$response;				Mage::app()->getResponse()->setRedirect($failedurl)->sendResponse();				exit;
			}
			else
			{
				// If Payment Gateway response has Payment ID & Pay page URL		
				$i =  strpos($response,":");
				// Merchant MUST map (update) the Payment ID received with the merchant Track Id in his database at this place.
				$paymentId = substr($response, 0, $i);
				$paymentPage = substr( $response, $i + 1);
				// here redirecting the customer browser from ME site to Payment Gateway Page with the Payment ID
				$r = $paymentPage . "?PaymentID=" . $paymentId;
				
				header("location:". $r );
			}
		}
		catch(Exception $e)
		{
		}
		}		else{			$url = Mage::getBaseUrl();			Mage::app()->getResponse()->setRedirect($url)->sendResponse();
		}
    }
}