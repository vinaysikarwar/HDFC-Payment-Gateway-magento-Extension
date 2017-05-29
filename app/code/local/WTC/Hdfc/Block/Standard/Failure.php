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
 * Failure Response from Secureebs
 *
 * @category   Mage
 * @package    WTC_Hdfc
 * @name       WTC_Hdfc_Block_Standard_Failure
 * @author     Vinay Sikarwar <shop@webtechnologycodes.com>
*/

class WTC_Hdfc_Block_Standard_Failure extends Mage_Core_Block_Template
{
	protected function _toHtml()
    {
		$responseURL = Mage::getBaseUrl().'hdfc/standard/response';
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
		
		if ($ResErrorNo == '')	
		{          
					
			$strHashTraportalID=trim("9000880"); //USE Tranportal ID FIELD FOR HASHING ,Mercahnt need to take this filed value  from his Secure channel such as DATABASE.
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
					
			if ($hashvalue == $Resudf5)
			{
				/* NOTE - MERCHANT MUST LOG THE RESPONSE RECEIVED IN LOGS AS PER BEST PRACTICE */
				/*IMPORTANT NOTE - MERCHANT DOES RESPONSE HANDLING AND VALIDATIONS OF 
				TRACK ID, AMOUNT AT THIS PLACE. THEN ONLY MERCHANT SHOULD UPDATE 
				TRANACTION PAYMENT STATUS IN MERCHANT DATABASE AT THIS POSITION 
				AND THEN REDIRECT CUSTOMER ON RESULT PAGE*/

				/* !!IMPORTANT INFORMATION!!
				During redirection, ME can pass the values as per ME requirement.
				NOTE: NO PROCESSING should be done on the RESULT PAGE basis of values passed in the RESULT PAGE from this page. 
				ME does all validations on the responseURL page and then redirects the customer to RESULT PAGE ONLY FOR RECEIPT PRESENTATION/TRANSACTION STATUS CONFIRMATION
				For demonstration purpose the result and track id are passed to Result page	*/
					
				/* Hashing Response Successful	*/
									
				$REDIRECT = 'REDIRECT='.$responseURL.'?ResResult='.$ResResult.'&ResTrackId='.$ResTrackID.'&ResAmount='.$ResAmount.'&ResPaymentId='.$ResPaymentId.'&ResRef='.$ResRef.'&ResTranId='.$ResTranId.'&ResError='.$ResErrorText.'Hashing Response Successful';
				echo $REDIRECT;
			}
			else
			{
				/* NOTE - MERCHANT MUST LOG THE RESPONSE RECEIVED IN LOGS AS PER BEST PRACTICE */
				/*Udf5 field values not matched with calculetd hashed valued then show appropriate message to
				Mercahnt for E.g.Hashing Response NOT Successful*/
				/* Hashing Response NOT Successful */
				$REDIRECT = 'REDIRECT='.$responseURL.'.?ResError=Hashing Response Mismatch';
				echo $REDIRECT;														
			}
		}
		else 
		{
			/*ERROR IN TRANSACTION PROCESSING
			IMPORTANT NOTE - MERCHANT SHOULD UPDATE 
			TRANACTION PAYMENT STATUS IN MERCHANT DATABASE AT THIS POSITION 
			AND THEN REDIRECT CUSTOMER ON RESULT PAGE*/
			$REDIRECT = 'REDIRECT='.responseURL.'?ResResult='.$ResResult.'&ResTrackId='.$ResTrackID.'&ResAmount='.$ResAmount.'&ResPaymentId='.$ResPaymentId.'&ResRef='.$ResRef.'&ResTranId='.$ResTranId.'&ResError='.$ResErrorText;		
			echo $REDIRECT;
		}
	}	
}