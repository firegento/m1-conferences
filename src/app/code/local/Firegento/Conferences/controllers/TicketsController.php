<?php


class FireGento_Conferences_TicketsController extends Mage_Core_Controller_Front_Action {

    public function statsAction()
    {
        $sku = $this->getRequest()->getParam('sku');
        $sku = preg_replace('/[^a-z0-9\-_]/i', '', $sku);

        if(!$sku) {
            $this->getResponse()->setHeader('Content-Type', 'text/xml');
            $this->getResponse()->setBody('<error>no sku given</error>');
            return;
        }

        $service = Mage::getModel('firegento_conferences/getTicketDataForBaseSkuService');
        $data = $service->getTicketDataSet($sku);
        $this->getResponse()->setHeader('Content-Type', 'text/xml');
        $this->getResponse()->setBody($data);
    }

}