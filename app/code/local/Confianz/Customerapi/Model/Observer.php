<?php

/**
 * Customer api
 * 
 * @category    Confianz
 * @package     Confianz_Customer observer
 *
 * @author rabeesh@confianzit.biz 
 *
 */
class Confianz_Customerapi_Model_Observer extends Mage_Customer_Model_Customer_Api {

    public function __construct() {

    }

    /**
     * Export Order Details into Oper Erp module.
     *
     * @param Varien_Object $observer
     * @return Wineshop_Erporders_Model_Observer
     */
    public function exportCustomerToErp(Varien_Event_Observer $observer) {
	if(Mage::getSingleton('customer/session')->isLoggedIn()){
    		return;
	}
        $customer = $observer->getEvent()->getCustomer();
	//if($customer->isObjectNew()) {
		$name = $customer->getName();
		$email = $customer->getEmail();
		$id = $customer->getId();
		//$observer contains the object returns in the event.
		$customer = $observer->getEvent()->getOrder();
		$store_id = Mage::app()->getStore()->getData('store_id');
                $store_groupid = Mage::app()->getStore()->getData('group_id');
		$website = Mage::app()->getWebsite()->getCode();
                if($website == 'admin'){
                    return;
                }
		//Getting stored configurations..
		$server = Mage::getStoreConfig('openerpconnector/erpconfig/servername');
		$port = Mage::getStoreConfig('openerpconnector/erpconfig/port');
		$db = Mage::getStoreConfig('openerpconnector/erpconfig/dbname');
		$user = Mage::getStoreConfig('openerpconnector/erpconfig/username');
		$pwd = Mage::getStoreConfig('openerpconnector/erpconfig/password');
		$errors = array();
		// change it to suit your RPC service
		$client = new Zend_XmlRpc_Client($server . ':' . $port . '/xmlrpc/common');
		$client_object = new Zend_XmlRpc_Client($server . ':' . $port . '/xmlrpc/object');
		try {
		    //Function call request for login.
		    $login_param = array(
		        'dbname' => $db,
		        'username' => $user,
		        'password' => $pwd,
		    );
		    $uid = $client->call('login', $login_param);
		    //Function call request for import_order_from_magento().
		    $params = array($db, $uid, $pwd, 'res.partner', 'create_customer' ,$id, $name, $email, $store_groupid, $website);
		    $client_object->call('execute', $params);
		} catch (Zend_XmlRpc_Client_FaultException $e) {
		    $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
		} catch (Zend_XmlRpc_Client_HttpException $e) {
		    $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
		}
		### print out errors if any 
		if ($errors) {
		    //print_r($errors);
		}
	//}
        return $this;
    }

}