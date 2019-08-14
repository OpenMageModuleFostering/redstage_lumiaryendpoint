<?php

class Redstage_LumiaryEndpoint_OrderController
    extends Mage_Core_Controller_Front_Action
{
    public function listAction()
    {
        $defaultParams = array("from" => null, "to" => date("Y-m-d"), "page" => 1, "limit" => 1);
        $params = array_merge($defaultParams, $this->getRequest()->getParams());
        
        if (!Mage::helper('lumiaryendpoint')->isEnabled()
            || !Mage::helper('lumiaryendpoint')->checkToken($params['token'])
        ) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
        
        $exporter = Mage::getModel('lumiaryendpoint/export_order_exporter');
        
        $data = $exporter->exportOrders($params['from'], $params['to'], $params['page'], $params['limit']);

        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($data));
        //exit;
    }

}
