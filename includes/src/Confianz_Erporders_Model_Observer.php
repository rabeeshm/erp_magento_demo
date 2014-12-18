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
class Confianz_Erporders_Model_Observer extends Mage_Sales_Model_Order_Api {

    public function __construct() {
        
    }

    /**
     * Export Order Details into Oper Erp module.
     *
     * @param Varien_Object $observer
     * @return Confianz_Erporders_Model_Observer
     */
    public function exportOrderToErp(Varien_Event_Observer $observer) { 
        //$observer contains the object returns in the event.
        $order = $observer->getEvent()->getOrder();
        //print_r($order);
        $order_code = Mage::getModel("sales/order")->getCollection()->getLastItem()->getIncrementId();
        $result = $this->info($order_code);
        
        //print_r($result); exit;
        
        $store_id = Mage::app()->getStore()->getData('store_id');
        $store_groupid = Mage::app()->getStore()->getData('group_id');
        $website = Mage::app()->getWebsite()->getCode();
        $orderIncrementId = $order_code;       
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
            $params = array($db, $uid, $pwd, 'sale.order', 'import_order_from_magento', $website, $order_code, $store_groupid, $result);
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
        return $this;
    }

}
