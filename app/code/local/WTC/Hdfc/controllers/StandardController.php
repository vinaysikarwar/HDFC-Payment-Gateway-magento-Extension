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

class WTC_Hdfc_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Return debug flag
     *
     *  @return  boolean
     */
    public function getDebug ()
    {
        return Mage::getSingleton('hdfc/config')->getDebug();
    }

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder ()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }
	
// The response action is triggered when FSS Net sends a response
	public function responseAction() 
	{
		
		$ResErrorText= isset($_POST['ErrorText']) ? $_POST['ErrorText'] : ''; 	//Error Text/message
		$ResPaymentId = isset($_POST['paymentid']) ? $_POST['paymentid'] : '';	//Payment Id
		$ResTrackID = isset($_POST['trackid']) ? $_POST['trackid'] : '';        //Merchant Track ID
		$ResErrorNo = isset($_POST['Error']) ? $_POST['Error'] : '';            //Error Number
		$ResResult = isset($_POST['result']) ? $_POST['result'] : '';           //Transaction Result
		$ResPosdate = isset($_POST['postdate']) ? $_POST['postdate'] : '';      //Postdate
		$ResTranId = isset($_POST['tranid']) ? $_POST['tranid'] : '';           //Transaction ID
		$ResAuth = isset($_POST['auth']) ? $_POST['auth'] : '';                 //Auth Code		
		$ResAVR = isset($_POST['avr']) ? $_POST['avr'] : '';                    //TRANSACTION avr					
		$ResRef = isset($_POST['ref']) ? $_POST['ref'] : '';                    //Reference Number also called Seq Number
		$ResAmount = isset($_POST['amt']) ? $_POST['amt'] : '';                 //Transaction Amount
		$Resudf1 = isset($_POST['udf1']) ? $_POST['udf1'] : '';                  //UDF1
		$Resudf2 = isset($_POST['udf2']) ? $_POST['udf2'] : '';                  //UDF2
		$Resudf3 = isset($_POST['udf3']) ? $_POST['udf3'] : '';                  //UDF3
		$Resudf4 = isset($_POST['udf4']) ? $_POST['udf4'] : '';                  //UDF4
		$Resudf5 = isset($_POST['udf5']) ? $_POST['udf5'] : '';                  //UDF5		
		
		$transportalId = Mage::getSingleton('hdfc/config')->getTransportalId();
		
		
		$hdfcModel = Mage::getModel('hdfc/hdfcresponse')
					->load($_POST['trackid'], 'trackid')
					->setErrorText($ResErrorText)
					->setPaymentid($ResPaymentId)
					->setTrackid($ResTrackID)
					->setError($ResErrorNo)
					->setResult($ResResult)
					->setPostdate($ResPosdate)
					->setTranid($ResTranId)
					->setAuth($ResAuth)
					->setAvr($ResAVR)
					->setRef($ResRef)
					->setAmt($ResAmount)
					->setUdf1($Resudf1)
					->setUdf2($Resudf2)
					->setUdf3($Resudf3)
					->setUdf4($Resudf4)
					->setUdf5($Resudf5)					
					->save(); 
		try
		{
			/* Capture the IP Address from where the response has been received */
			$strResponseIPAdd = getenv('REMOTE_ADDR');
			
			/* Check whether the IP Address from where response is received is PG IP */
			if ($strResponseIPAdd != "221.134.101.175" && $strResponseIPAdd != "221.134.101.166" && $strResponseIPAdd != "221.134.101.187")
			{
				$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'checkout/onepage/failure?ResError=--IP MISSMATCH-- Response IP Address is: '.$strResponseIPAdd;
				echo $REDIRECT;
			}
			else
			{	
				/*Variable Declaration*/
				/*=========================================================================================*/
				
				
			
				if ($ResErrorNo == '')	
				{          
					
					$strHashTraportalID=trim($transportalId); //USE Tranportal ID FIELD FOR HASHING ,Mercahnt need to take this filed value  from his Secure channel such as DATABASE.
					$strhashstring="";            //Declaration of Hashing String 
					
					$strhashstring=trim($strHashTraportalID);
					
					//Below code creates the Hashing String also it will check NULL and Blank parmeters and exclude from the hashing string
					if ($ResTrackID != '' && $ResTrackID != null )
					$strhashstring=trim($strhashstring).trim($ResTrackID);					
					if ($ResAmount != '' && $ResAmount != null )
					$strhashstring=trim($strhashstring).trim($ResAmount);					
					if ($ResResult != '' && $ResResult != null )
					$strhashstring=trim($strhashstring).trim($ResResult);					
					if ($ResPaymentId != '' && $ResPaymentId != null )
					$strhashstring=trim($strhashstring).trim($ResPaymentId);					
					if ($ResRef != '' && $ResRef != null )
					$strhashstring=trim($strhashstring).trim($ResRef);					
					if ($ResAuth != '' && $ResAuth != null )
					$strhashstring=trim($strhashstring).trim($ResAuth);					
					if ($ResTranId != '' && $ResTranId != null )
					$strhashstring=trim($strhashstring).trim($ResTranId);					
										
					//Use sha256 method which is defined below for Hashing ,It will return Hashed valued of above strin					
					$hashvalue= hash('sha256', $strhashstring); 					
					
					/*******************HASHING CODE LOGIC END************************************/
					//$hdfcModel = Mage::getModel('hdfc/hdfcresponse')->savePaymentData($_POST);
					
					
					
					
					
					if ($hashvalue == $Resudf5)
					{
					
					/* Hashing Response Successful	*/
						
						if($ResResult == "CAPTURED" || $ResResult == 'APPROVED')
						{
							$this->orderSuccess($ResTrackID);
							$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'/checkout/onepage/success?ResTrackId='.$ResTrackID;
							echo $REDIRECT;
						}
						else
						{
							$this->cancel_order($ResTrackID);
							$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'/checkout/onepage/failure?ResTrackId='.$ResTrackID;
							echo $REDIRECT;
						}
					}
					else
					{
					/* NOTE - MERCHANT MUST LOG THE RESPONSE RECEIVED IN LOGS AS PER BEST PRACTICE */
					/*Udf5 field values not matched with calculetd hashed valued then show appropriate message to
					Mercahnt for E.g.Hashing Response NOT Successful*/

					/* Hashing Response NOT Successful */
					$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'/checkout/onepage/failure?ResError=Hashing Response Mismatch';
					echo $REDIRECT;														
					}
				}
				else 
				{
								/*ERROR IN TRANSACTION PROCESSING
								IMPORTANT NOTE - MERCHANT SHOULD UPDATE 
								TRANACTION PAYMENT STATUS IN MERCHANT DATABASE AT THIS POSITION 
								AND THEN REDIRECT CUSTOMER ON RESULT PAGE*/
				$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'/checkout/onepage/failure?ResTrackId='.$ResTrackID;		
				echo $REDIRECT;
				}
			}	
		}
		catch(Exception $e)
		{
			var_dump($e->getMessage());
		}
	}
	
	public function failure()
	{
		$ResErrorText= isset($_POST['ErrorText']) ? $_POST['ErrorText'] : ''; 	//Error Text/message
		$ResPaymentId = isset($_POST['paymentid']) ? $_POST['paymentid'] : '';	//Payment Id
		$ResTrackID = isset($_POST['trackid']) ? $_POST['trackid'] : '';        //Merchant Track ID
		$ResErrorNo = isset($_POST['Error']) ? $_POST['Error'] : '';            //Error Number
		$ResResult = isset($_POST['result']) ? $_POST['result'] : '';           //Transaction Result
		$ResPosdate = isset($_POST['postdate']) ? $_POST['postdate'] : '';      //Postdate
		$ResTranId = isset($_POST['tranid']) ? $_POST['tranid'] : '';           //Transaction ID
		$ResAuth = isset($_POST['auth']) ? $_POST['auth'] : '';                 //Auth Code		
		$ResAVR = isset($_POST['avr']) ? $_POST['avr'] : '';                    //TRANSACTION avr					
		$ResRef = isset($_POST['ref']) ? $_POST['ref'] : '';                    //Reference Number also called Seq Number
		$ResAmount = isset($_POST['amt']) ? $_POST['amt'] : '';                 //Transaction Amount
		$Resudf1 = isset($_POST['udf1']) ? $_POST['udf1'] : '';                  //UDF1
		$Resudf2 = isset($_POST['udf2']) ? $_POST['udf2'] : '';                  //UDF2
		$Resudf3 = isset($_POST['udf3']) ? $_POST['udf3'] : '';                  //UDF3
		$Resudf4 = isset($_POST['udf4']) ? $_POST['udf4'] : '';                  //UDF4
		$Resudf5 = isset($_POST['udf5']) ? $_POST['udf5'] : '';                  //UDF5		
		
		$transportalId = Mage::getSingleton('hdfc/config')->getTransportalId();
		
		$this->cancel_order($ResTrackID);
		
		$hdfcModel = Mage::getModel('hdfc/hdfcresponse')
					->load($_POST['trackid'], 'trackid')
					->setErrorText($ResErrorText)
					->setPaymentid($ResPaymentId)
					->setTrackid($ResTrackID)
					->setError($ResErrorNo)
					->setResult($ResResult)
					->setPostdate($ResPosdate)
					->setTranid($ResTranId)
					->setAuth($ResAuth)
					->setAvr($ResAVR)
					->setRef($ResRef)
					->setAmt($ResAmount)
					->setUdf1($Resudf1)
					->setUdf2($Resudf2)
					->setUdf3($Resudf3)
					->setUdf4($Resudf4)
					->setUdf5($Resudf5)					
					->save(); 
		try
		{
			/* Capture the IP Address from where the response has been received */
			$strResponseIPAdd = getenv('REMOTE_ADDR');
			
			/* Check whether the IP Address from where response is received is PG IP */
			if ($strResponseIPAdd != "221.134.101.175" && $strResponseIPAdd != "221.134.101.166" && $strResponseIPAdd != "221.134.101.187")
			{
				$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'checkout/onepage/failure?ResError=--IP MISSMATCH-- Response IP Address is: '.$strResponseIPAdd;
				echo $REDIRECT;
			}
			else
			{	
				/*Variable Declaration*/
				/*=========================================================================================*/
				
				
			
				if ($ResErrorNo == '')	
				{          
					
					$strHashTraportalID=trim($transportalId); //USE Tranportal ID FIELD FOR HASHING ,Mercahnt need to take this filed value  from his Secure channel such as DATABASE.
					$strhashstring="";            //Declaration of Hashing String 
					
					$strhashstring=trim($strHashTraportalID);
					
					//Below code creates the Hashing String also it will check NULL and Blank parmeters and exclude from the hashing string
					if ($ResTrackID != '' && $ResTrackID != null )
					$strhashstring=trim($strhashstring).trim($ResTrackID);					
					if ($ResAmount != '' && $ResAmount != null )
					$strhashstring=trim($strhashstring).trim($ResAmount);					
					if ($ResResult != '' && $ResResult != null )
					$strhashstring=trim($strhashstring).trim($ResResult);					
					if ($ResPaymentId != '' && $ResPaymentId != null )
					$strhashstring=trim($strhashstring).trim($ResPaymentId);					
					if ($ResRef != '' && $ResRef != null )
					$strhashstring=trim($strhashstring).trim($ResRef);					
					if ($ResAuth != '' && $ResAuth != null )
					$strhashstring=trim($strhashstring).trim($ResAuth);					
					if ($ResTranId != '' && $ResTranId != null )
					$strhashstring=trim($strhashstring).trim($ResTranId);					
										
					//Use sha256 method which is defined below for Hashing ,It will return Hashed valued of above strin					
					$hashvalue= hash('sha256', $strhashstring); 					
					
					/*******************HASHING CODE LOGIC END************************************/
					//$hdfcModel = Mage::getModel('hdfc/hdfcresponse')->savePaymentData($_POST);
					
					
					
					
					
					if ($hashvalue == $Resudf5)
					{
					
					/* Hashing Response Successful	*/
									
						$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'/checkout/onepage/failure?ResTrackId='.$ResTrackID;
						echo $REDIRECT;
					}
					else
					{
					/* NOTE - MERCHANT MUST LOG THE RESPONSE RECEIVED IN LOGS AS PER BEST PRACTICE */
					/*Udf5 field values not matched with calculetd hashed valued then show appropriate message to
					Mercahnt for E.g.Hashing Response NOT Successful*/

					/* Hashing Response NOT Successful */
					$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'/checkout/onepage/failure?ResError=Hashing Response Mismatch';
					echo $REDIRECT;														
					}
				}
				else 
				{
								/*ERROR IN TRANSACTION PROCESSING
								IMPORTANT NOTE - MERCHANT SHOULD UPDATE 
								TRANACTION PAYMENT STATUS IN MERCHANT DATABASE AT THIS POSITION 
								AND THEN REDIRECT CUSTOMER ON RESULT PAGE*/
				$REDIRECT = 'REDIRECT='.Mage::getBaseUrl().'/checkout/onepage/failure?ResTrackId='.$ResTrackID;		
				echo $REDIRECT;
				}
			}	
		}
		catch(Exception $e)
		{
			var_dump($e->getMessage());
		}
	
	}

	
    /**
     * When a customer chooses hdfc on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
		
        $session = Mage::getSingleton('checkout/session');
        $session->setHdfcStandardQuoteId($session->getQuoteId());
		$order = $this->getOrder();
		
        
        if (!$order->getId()) {
		
			$this->_forward('failurerefresh');
            return;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('hdfc')->__('Customer was redirected to hdfc')
        );
        $order->save();

        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('hdfc/standard_redirect')
                ->setOrder($order)
                ->toHtml());

        $session->unsQuoteId();
    }
	
	
	public function orderSuccess($orderId)
	{
		$order = Mage::getModel( 'sales/order' )->loadByIncrementId( $orderId );
		if ( $order->getId() ) 
		{
			$order->addStatusToHistory(
				$order->getStatus(),
				Mage::helper('hdfc')->__('Customer successfully returned from hdfc')
			);
			
			$order->save();
			$order->sendNewOrderEmail();
		}
	}
	// This function is triggered when we cancel an order
	public function cancel_order( $order_id ) {
        $order = Mage::getModel( 'sales/order' )->loadByIncrementId( $order_id );
		if ( $order->getId() ) {
			// Flag the order as 'cancelled' and save it
			$order->cancel()->setState( Mage_Sales_Model_Order::STATE_CANCELED, true, 'FSS Net has declined the payment.' )->save();
		}
	}
	
	// Function to check whether a response has come from a valid IP
	private function is_from_valid_ip() {
		$my_ip = getenv( 'REMOTE_ADDR' );
		
		// Check if we are in testing mode, and get list of valid IPs
		//if ( Mage::getStoreConfig( 'payment/fssnet/is_testing' ) )
			$valid_ips = array( '221.134.101.174', '221.134.101.169','198.64.129.10','198.64.133.213' );
		//else
			//$valid_ips = array( '221.134.101.187', '221.134.101.175', '221.134.101.166','198.64.129.10','198.64.133.213' );
		
		// Check if our IP is valid
		if ( in_array( $my_ip, $valid_ips ) )
			return true;
		else
			return false;
	}

}

