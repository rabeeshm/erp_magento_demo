<?php

/**
 * @author rabeesh@confianzit.biz 
 * @category    Wineshop
 * @package     Wineshop_Api
 */
class Wineshop_Erporders_Model_Core_Api extends Mage_Core_Model_Abstract {

    /**
     * Capture payment using ePay webservice API
     * @param int $orderIds
     * @return array
     */
    public function capture($orderIds) {
        $output = array();
        foreach ($orderIds['order_id'] as $orderId) {
            $test[] = $orderId;
            $order = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', $orderId)->getFirstItem();
            $merchantnumber = Mage::getStoreConfig('payment/epay_standard/merchantnumber');
            $_totalData = $order->getData();
            if (!empty($_totalData)) {
                $order_id = $_totalData['entity_id'];
                //$amount = $_totalData['base_total_paid'];
                $transactionId = $order->getPayment()->getLastTransId();
                $order->setData('state', Mage_Sales_Model_Order::STATE_PROCESSING);
                $order->setStatus('processing');
                $this->createShipment($orderId);
            }
            $amount = $order->getGrandTotal();
            $epay_params = array();
            $epay_params['merchantnumber'] = $merchantnumber;
            $epay_params['transactionid'] = $transactionId;
            $epay_params['amount'] = $amount;
            $epay_params['pbsResponse'] = "-1";
            $epay_params['epayresponse'] = "-1";
            $client = new SoapClient('https://ssl.ditonlinebetalingssystem.dk/remote/payment.asmx?WSDL');
            $result = $client->capture($epay_params);
            if ($result->captureResult == true) {
                $order->setStatus('complete');
                $order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
                $this->createInvoice($orderId);
                $output[] = array("transaction_id" => $transactionId, 'order_id' => $orderId, 'success' => true);
            } else {
                $output[] = array("transaction_id" => $transactionId, 'order_id' => $orderId, 'success' => false);
            }
        }
        return $output;
    }

    /**
     * Create shipment
     * @param int $orderIds
     * @return boolean
     */
    private function createShipment($orderId) {
        $order = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', $orderId)->getFirstItem();
        try {
            if ($order->canShip()) {
                //Create shipment
                $shipmentid = Mage::getModel('sales/order_shipment_api')
                        ->create($order->getIncrementId(), array());
                //Add tracking information
                $ship = Mage::getModel('sales/order_shipment_api')
                        ->addTrack($order->getIncrementId(), array());
            }
        } catch (Mage_Core_Exception $e) {
            
        }
    }

    /**
     * Create Invoice
     * @param int $orderIds
     * @return boolean
     */
    private function createInvoice($orderId) {
        $order = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', $orderId)->getFirstItem();
        try {
            if (!$order->canInvoice()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
            }
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
            if (!$invoice->getTotalQty()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
            }
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
            $transactionSave->save();
        } catch (Mage_Core_Exception $e) {
            
        }
    }

}

