<?php

class Redstage_LumiaryEndpoint_Model_Export_Order_Exporter
{    
    public function prepareOrder($orderId)
    {
        $translator = Mage::getModel('lumiaryendpoint/export_order_data_translator_magento');
        $data = $translator->toOrderData($orderId);

        return $data;
    }

    public function exportOrders($fromDate, $toDate, $pageNumber = 1, $limit = 1)
    {
        /* Timer */
        $timer = Mage::getModel('lumiaryendpoint/operation_timer')->start();

        /* Default Parameters */
        $limit = (int) $limit ? $limit : 1;
        $pageNumber = (int) $pageNumber ? $pageNumber : 1;

        $data = array();
        $fromDate = date('Y-m-d H:i:s', strtotime($fromDate));
        $toDate = date('Y-m-d H:i:s', strtotime($toDate));
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('updated_at', array('from'=>$fromDate, 'to'=>$toDate))
            ->setPageSize($limit)
            ->setCurPage($pageNumber);
            //->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE));

        if ($orders->getLastPageNumber() >= $pageNumber) {
            foreach ($orders as $order) {
                $data[] = $this->prepareOrder($order->getId())->getData();
            }
        }

        return $data;
    }
}
