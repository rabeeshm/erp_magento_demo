<?php

/**
 * Magento
 * @category    ERP delete handler
 * @package     Confianz_
 *
 * @author rabeesh@confianzit.biz 
 *
 */
class Confianz_DeleteHandler_Model_Observer {

    protected $_servername;
    protected $_port;
    protected $_db;
    protected $_user;
    protected $_pwd;
    protected $_client;
    protected $_client_object;

    /**
     * Constructor
     */
    public function __construct() {
        $this->_servername = Mage::getStoreConfig('openerpconnector/erpconfig/servername');
        $this->_port = Mage::getStoreConfig('openerpconnector/erpconfig/port');
        $this->_db = Mage::getStoreConfig('openerpconnector/erpconfig/dbname');
        $this->_user = Mage::getStoreConfig('openerpconnector/erpconfig/username');
        $this->_pwd = Mage::getStoreConfig('openerpconnector/erpconfig/password');
        $this->_client = new Zend_XmlRpc_Client($this->_servername . ':' . $this->_port . '/xmlrpc/common');
        $this->_client_object = new Zend_XmlRpc_Client($this->_servername . ':' . $this->_port . '/xmlrpc/object');
    }

    /**
     * Magento passes a Varien_Event_Observer object as
     * the first parameter of dispatched events.
     * @author Rabeesh <rabeesh@confianzit.biz>
     */
    public function delectProduct(Varien_Event_Observer $observer) {

        $productId = $observer->getProduct()->getId();
        try {
            //Function call request for login.
            $login_param = array(
                'dbname' => $this->_db,
                'username' => $this->_user,
                'password' => $this->_pwd,
            );
            //print_r($login_param); exit;
            $uid = $this->_client->call('login', $login_param);
            //Function call request for import_order_from_magento().
            $params = array($this->_db, $uid, $this->_pwd, 'product.product', 'erp_unlink', $productId);
            $this->_client_object->call('execute', $params);
        } catch (Zend_XmlRpc_Client_FaultException $e) {
            $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
        } catch (Zend_XmlRpc_Client_HttpException $e) {
            $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
        }
        if ($errors) {
            //print_r($errors);
        }
    }

    /**
     * @author Rabeesh <rabeesh@confianzit.biz>
     * @param Varien_Event_Observer $observer
     */
    function deleteCategory(Varien_Event_Observer $observer) {

        $categoryId = $observer->getCategory()->getId();
        try {
            //Function call request for login.
            $login_param = array(
                'dbname' => $this->_db,
                'username' => $this->_user,
                'password' => $this->_pwd,
            );
            $uid = $this->_client->call('login', $login_param);
            //Function call request for import_order_from_magento().
            $params = array($this->_db, $uid, $this->_pwd, 'product.category', 'erp_unlink', $categoryId);
            $this->_client_object->call('execute', $params);
        } catch (Zend_XmlRpc_Client_FaultException $e) {
            $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
        } catch (Zend_XmlRpc_Client_HttpException $e) {
            $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
        }
        if ($errors) {
            //print_r($errors);
        }
    }

    /**
     * @author Rabeesh <rabeesh@confianzit.biz> 
     * @param Varien_Event_Observer $observer
     */
    function deleteCustomer(Varien_Event_Observer $observer) {
        $customerId = $observer->getCustomer()->getId();
        try {
            //Function call request for login.
            $login_param = array(
                'dbname' => $this->_db,
                'username' => $this->_user,
                'password' => $this->_pwd,
            );
            $uid = $this->_client->call('login', $login_param);
            //Function call request for import_order_from_magento().
            $params = array($this->_db, $uid, $this->_pwd, 'res.partner', 'erp_unlink', $customerId);
            $this->_client_object->call('execute', $params);
        } catch (Zend_XmlRpc_Client_FaultException $e) {
            $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
        } catch (Zend_XmlRpc_Client_HttpException $e) {
            $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
        }
        if ($errors) {
            //print_r($errors);
        }
    }

}
