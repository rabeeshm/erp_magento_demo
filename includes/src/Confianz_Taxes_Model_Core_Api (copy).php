<?php

/**
 *
 * @author rabeesh@confianzit.biz 
 *
 */
class Wineshop_Taxes_Model_Core_Api extends Mage_Api_Model_Resource_Abstract {

    public function create($params) {
        //return $params;
        //get the product tax class
        $productClass = Mage::getModel('tax/class')
                        ->setData(array(
                            "class_name" => $params['code'],
                            "class_type" => "PRODUCT",
                        ))->save();

        $productTaxClass = Mage::getModel('tax/class')
                ->getCollection()
                ->addFieldToFilter('class_name', $params['code'])
                ->load()
                ->getFirstItem();

        //get the customer tax class
        $customerTaxClass = Mage::getModel('tax/class')
                ->getCollection()
                ->addFieldToFilter('class_name', 'Retail Customer')
                ->load()
                ->getFirstItem();

        //create a new australia tax rate/zone
        $taxCalculationRate = Mage::getModel('tax/calculation_rate')
                        ->setData(array(
                            "code" => $params['code'],
                            "tax_country_id" => $params['tax_country_id'],
                            "tax_region_id" => "0",
                            "zip_is_range" => "0",
                            "tax_postcode" => "*",
                            "rate" => $params['rate'],
                            //"rate_type" => $params['type'],
                        ))->save();
        //create a new tax rule
        $ruleModel = Mage::getModel('tax/calculation_rule')
                        ->setData(array(
                            "code" => $params['code'],
                            "tax_customer_class" => array($customerTaxClass->getId()),
                            "tax_product_class" => array($productTaxClass->getId()),
                            "tax_rate" => array($taxCalculationRate->getId()),
                            "priority" => "0",
                            "position" => "0",
                        ))->save();
        
        $classData = array();
//        if ($params['type'] == 'percentage') {
//            $classList = Mage::getModel('tax/class')->getCollection()
//                            ->addFieldToFilter('class_type', 'PRODUCT')->addFieldToSelect('class_name');
//            foreach ($classList as $allClasses) {
//                if ($params['code'] != $allClasses['class_name']) {
//                    $productTaxClassRelation = Mage::getModel('tax/class')
//                            ->getCollection()
//                            ->addFieldToFilter('class_name', $allClasses['class_name'])
//                            ->load()
//                            ->getFirstItem();
//                    //$this->saveCalculation($productTaxClassRelation->getId());
//                    $calculation = Mage::getModel('tax/calculation')->getCollection()
//                            ->addFieldToFilter('product_tax_class_id', $productTaxClassRelation->getId())
//                            ->addFieldToSelect('tax_calculation_id')
//                            ->addFieldToSelect('tax_calculation_rate_id')
//                            ->addFieldToSelect('tax_calculation_rule_id')
//                            ->addFieldToSelect('product_tax_class_id')
//                            ->addFieldToSelect('customer_tax_class_id')
//                            ->setPageSize(1);
//                    foreach ($calculation as $taxcalculation) {
//                        //if (is_duplicate($taxcalculation)) {
//                        //ADD VAT into calculation relation 
//                        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
//                        $connectionWrite->beginTransaction();
//                        $data = array();
//                        $data['tax_calculation_rate_id'] = $taxCalculationRate->getId();
//                        $data['tax_calculation_rule_id'] = $taxcalculation['tax_calculation_rule_id'];
//                        $data['customer_tax_class_id'] = $taxcalculation['customer_tax_class_id'];
//                        $data['product_tax_class_id'] = $taxcalculation['product_tax_class_id'];
//                        $connectionWrite->insert('wine_tax_calculation', $data);
//                        $connectionWrite->commit();
//                        //}
//                    }
//                }
//            }
//        }
//         else {
//            $data = array();
//            $rateList = Mage::getModel('tax/calculation_rate')->getCollection()
//                            ->addFieldToFilter('rate_type', 'percentage')->addFieldToSelect('*');
//            foreach ($rateList as $rateLists) {
//                //$this->saveCalculation($productTaxClass->getId(), $rateLists->getId());
//                $calculation = Mage::getModel('tax/calculation')->getCollection()
//                        ->addFieldToFilter('product_tax_class_id', $productTaxClass->getId())
//                        ->addFieldToSelect('tax_calculation_id')
//                        ->addFieldToSelect('tax_calculation_rate_id')
//                        ->addFieldToSelect('tax_calculation_rule_id')
//                        ->addFieldToSelect('product_tax_class_id')
//                        ->addFieldToSelect('customer_tax_class_id')
//                        ->setPageSize(1);
//                foreach ($calculation as $taxcalculation) {
//                    //if (is_duplicate($taxcalculation)) {
//                    //ADD VAT into calculation relation 
//                    $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
//                    $connectionWrite->beginTransaction();
//                    $data = array();
//                    $data['tax_calculation_rate_id'] = $rateLists->getId();
//                    $data['tax_calculation_rule_id'] = $taxcalculation['tax_calculation_rule_id'];
//                    $data['customer_tax_class_id'] = $taxcalculation['customer_tax_class_id'];
//                    $data['product_tax_class_id'] = $taxcalculation['product_tax_class_id'];
//                    $connectionWrite->insert('wine_tax_calculation', $data);
//                    $connectionWrite->commit();
//                    //}
//                }
//            }
//        }
        return $taxCalculationRate->getId();
    }

    public function is_duplicate($taxcalculation) {
        return true;
    }

    public function items() {
        //get the product tax class
        $productTaxClass = Mage::getModel('tax/class')
                ->getCollection()
                ->addFieldToFilter('class_name', 'Taxable Goods')
                ->load()
                ->getFirstItem();
        return $productTaxClass->gettId();
    }

   public function update($id, $params) {
        //Updating Class names
        $class = Mage::getModel('tax/class')->load($id);
        $class->setClassName($params['code']);
        $class->save();
        //Update tax rate/zone
        $taxCalculationRate = Mage::getModel('tax/calculation_rate')->load($id);
        $taxCalculationRate->setCode($params['code']);
        $taxCalculationRate->setTaxCountryId($params['tax_country_id']);
        $taxCalculationRate->setTaxRegionId($params['tax_region_id']);
        $taxCalculationRate->setZipIsRange($params['zip_is_range']);
        $taxCalculationRate->setTaxPostcode($params['tax_postcode']);
        $taxCalculationRate->setRate($params['rate']);
        $taxCalculationRate->save();
    }

    public function delete($id) {
        return "delete";
    }

}

?>
