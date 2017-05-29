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
 * SecureHdfc Standard Model
 *
 * @category   Mage
 * @package    WTC_Hdfc
 * @name       WTC_Hdfc_Model_Standard
 * @author     Vinay Sikarwar<shop@webtechnologycodes.com>
 */
class WTC_Hdfc_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'hdfc_standard';
    protected $_formBlockType = 'hdfc/standard_form';

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_order = null;


    /**
     * Get Config model
     *
     * @return object WTC_Hdfc_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('hdfc/config');
    }

    /**
     * Payment validation
     *
     * @param   none
     * @return  WTC_Hdfc_Model_Standard
     */
    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
       // if ($currency_code != $this->getConfig()->getCurrency()) {
         //   Mage::throwException(Mage::helper('hdfc')->__('Selected currency //code ('.$currency_code.') is not compatabile with SecureEbs'));
       // }
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture (Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getSecureHdfcUrl ($server)
    {
        if($server == 0)
			return 'https://securepgtest.fssnet.co.in/pgway/servlet/PaymentInitHTTPServlet';
		else
			return 'https://securepg.fssnet.co.in/pgway/servlet/PaymentInitHTTPServlet';
    }
	

    /**
     *  Return URL for SecureHdfc success response
     *
     *  @return	  string URL
     */
    protected function getSuccessURL ()
    {
        return Mage::getUrl('hdfc/standard/success', array('_secure' => true));
    }
    /**
     *  Return URL for SecureHdfc failure response
     *
     *  @return	  string URL
     */
    protected function getFailureURL ()
    {
        return Mage::getUrl('hdfc/standard/failure', array('_secure' => true));
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('hdfc/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('hdfc/standard/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to SecureHdfc
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields ()
    {
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }

        $billingAddress = $order->getBillingAddress();

        $streets = $billingAddress->getStreet();
        $street = isset($streets[0]) && $streets[0] != ''
                  ? $streets[0]
                  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');

        if ($this->getConfig()->getDescription()) {
            $transDescription = $this->getConfig()->getDescription();
        } else {
            
			//$transDescription = Mage::helper('hdfc')->__('Order #%s', $order->getRealOrderId());
        }

        if ($order->getCustomerEmail()) {
            $email = $order->getCustomerEmail();
        } elseif ($billingAddress->getEmail()) {
            $email = $billingAddress->getEmail();
        } else {
            $email = '';
        }

        $fields = array(
						'id'       		=> $this->getConfig()->getAccountId(),
						'password'      => $this->getConfig()->getPassword(),
						'currencycode'  => $this->getConfig()->getCurrencycode(),
                       	'responseURL'   => Mage::getUrl('hdfc/standard/success',array('_secure' => true)),
                        'udf1'     		=> $order->getRealOrderId(),
						'action'     	=> '1',
                        'amt'    		=> $order->getBaseGrandTotal(),
                        'langid'        => 'USA',
                        'errorURL'      => $this->getFailureURL(),
                      	'trackid'       => $order->getRealOrderId()               );

        if ($this->getConfig()->getDebug()) {
            $debug = Mage::getModel('hdfc/api_debug')
                ->setRequestBody($this->getSecureHdfcUrl()."\n".print_r($fields,1))
                ->save();
            $fields['cs2'] = $debug->getId();
        }

        return $fields;
    }

}