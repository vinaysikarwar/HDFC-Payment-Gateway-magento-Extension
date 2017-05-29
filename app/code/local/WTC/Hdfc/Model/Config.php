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
 * @name       WTC_Hdfc_Model_Config
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class WTC_Hdfc_Model_Config extends Varien_Object
{
    /**
     *  Return config var
     *
     *  @param    string Var key
     *  @param    string Default value for non-existing key
     *  @return	  mixed
     */
    public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
            $value = Mage::getStoreConfig('payment/hdfc_standard/'.$key);
            if (is_null($value) || false===$value) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     *  Return Transaction Mode registered in Secure Hdfc Admnin Panel
     *
     *  @param    none
     *  @return	  string Transaction Mode
     */
    public function getTransactionMode ()
    {
        return $this->getConfigData('mode');
    }

    /**
     *  Return Secret Key registered in Secure Hdfc Admnin Panel
     *
     *  @param    none
     *  @return	  string Secret Key
     */
    public function getPassword()
    {
        return $this->getConfigData('password');
    }
	
	public function getPaymentGatewayUrl()
    {
        return $this->getConfigData('payment_url');
    }

 /**
     *  Return Account ID (general type payments) registered in Secure Hdfc Admnin Panel
     *
     *  @param    none
     *  @return	  string Account ID
     */
    public function getTransportalId ()
    {
        return $this->getConfigData('transportal_id');
    }
    /**
     *  Return Store description sent to SecureHdfc
     *
     *  @return	  string Description
     */
    public function getDescription ()
    {
        $description = $this->getConfigData('description');
        return $description;
    }

    /**
     *  Return new order status
     *
     *  @return	  string New order status
     */
    public function getNewOrderStatus ()
    {
        return $this->getConfigData('order_status');
    }
    /**
     *  Return accepted currency
     *
     *  @param    none
     *  @return	  string Currenc
     */
    public function getCurrency ()
    {
        return $this->getConfigData('currencycode');
    }
}