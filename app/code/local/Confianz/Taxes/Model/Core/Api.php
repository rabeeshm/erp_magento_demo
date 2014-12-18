<?php

/**
 *
 * @author rabeesh@confianzit.biz 
 * @category    Confianz
 * @package     Confianz_Api
 */
class Confianz_Taxes_Model_Core_Api extends Mage_Api_Model_Resource_Abstract {

    /**
     * Creating new Tax class,Tax rule.
     * @param array $params
     * @return int (tax_rate_id)
     */
    public function create($params) {
        //get the product tax class
        $productTaxClass = Mage::getModel('tax/class')
                ->getCollection()
                ->addFieldToFilter('class_name', $params['code'])
                ->load()
                ->getFirstItem();
        if (!empty($productTaxClass->getId())) {
            $productClass = Mage::getModel('tax/class')
                            ->setData(array(
                                "class_name" => $params['code'],
                                "class_type" => "PRODUCT",
                            ))->save();
        }

        //get the customer tax class
        $customerTaxClass = Mage::getModel('tax/class')
                ->getCollection()
                ->addFieldToFilter('class_name', 'Retail Customer')
                ->load()
                ->getFirstItem();

        //create a new Denmark tax rate/zone
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

        return $taxCalculationRate->getId();
    }

    /**
     * List  Tax class,Tax rule.
     *
     * @param array $params
     * @return int (tax_rate_id)
     */
    public function items() {
        //get the product tax class
        $productTaxClass = Mage::getModel('tax/class')
                ->getCollection()
                ->addFieldToFilter('class_name', 'Taxable Goods')
                ->load()
                ->getFirstItem();
        return $productTaxClass->gettId();
    }

    /**
     * Updating new Tax class,Tax rule.
     *
     * @param array $params
     * @return int (tax_rate_id)
     */
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
