<?php

class Redstage_LumiaryEndpoint_Adminhtml_Lumiary_TokenController
    extends Mage_Adminhtml_Controller_Action
{
    public function generateAction()
    {
        echo Mage::helper('lumiaryendpoint')->generateToken();
    }
}
