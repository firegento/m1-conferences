<?php
declare(strict_types=1);

class FireGento_Conferences_Model_GetTicketDataForBaseSkuService extends Mage_Core_Model_Abstract
{
    public function getTicketDataSet(string $sku): string
    {
        $dataset = [];

        $orderItems = Mage::getModel('sales/order_item')->getCollection()
                        ->join('sales/order', 'main_table.order_id = entity_id', ['status'])
                        ->addFieldToFilter('sku', ['like' => '%' . $sku . '%'])
                        ->addFieldToFilter('status', ['nin' => 'canceled,closed,pending,pending_payment']);
        
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($orderItems as $orderItem) {
            $index = (int)($orderItem->getBaseOriginalPrice() - $orderItem->getBaseDiscountAmount()) * 1000;
            if (!array_key_exists($index, $dataset)) {
                $dataset[$index] = 0;
            }
            $dataset[$index] += (int)$orderItem->getQtyOrdered() - $orderItem->getQtyCanceled();
        }

        return $this->convertArrayIntoXml($dataset);
    }

    public function convertArrayIntoXml(array $dataset): string
    {

        $xml = new SimpleXMLElement('<root/>');
        foreach ($dataset as $key => $value) {
            $xml->addChild('item', (string)$value)->addAttribute('price', (string)$key);
        }

        return $xml->asXML();
    }
}
