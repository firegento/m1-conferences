<?php
declare(strict_types=1);

class FireGento_Conferences_Model_GetTicketDataForBaseSkuService extends Mage_Core_Model_Abstract
{
    public function getTicketDataSet(string $sku): string
    {
        //create collection of order items with a filter for sku
        $orderItems = Mage::getModel('sales/order_item')->getCollection()
            ->addFieldToFilter('sku', ['like' => '%'.$sku.'%']);
        $dataset = [];
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach($orderItems as $orderItem) {
            $index = ($orderItem->getBaseOriginalPrice() - $orderItem->getBaseDiscountAmount()) * 1000;
            if(!$dataset[$index]) { $dataset[$index] = 0; }
            $dataset[$index] += (int) $orderItem->getQtyOrdered();
        }

        return $this->convertArrayIntoXml($dataset);
    }

    public function convertArrayIntoXml(array $dataset): string
    {

        $xml = new SimpleXMLElement('<root/>');
        foreach($dataset as $key => $value) {
            $xml->addChild('item', (string) $value)->addAttribute('price', (string) $key);
        }
        return $xml->asXML();
    }
}