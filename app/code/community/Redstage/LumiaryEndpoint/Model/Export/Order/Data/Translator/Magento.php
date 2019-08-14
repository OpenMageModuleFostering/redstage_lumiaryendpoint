<?php

class Redstage_LumiaryEndpoint_Model_Export_Order_Data_Translator_Magento
    implements Redstage_LumiaryEndpoint_Model_Export_Order_Data_Translator
{
    public function toOrderData()
    {
        $args = func_get_args();

        if (!$args[0]) {
            throw new RuntimeException('Invalid order id argument');
        }

        $orderId = $args[0];
        $orderData = $this->_generateOrderData($orderId);

        return $orderData;
    }
    
    protected function _generateOrderData($orderId)
    {
        $orderData = new Redstage_LumiaryEndpoint_Model_Export_Order_Data();
        $orderData->setOrder($this->_order($orderId));

        return $orderData;
    }
    
    protected function _order($orderId)
    {
        $order = $this->_getOrder($orderId);
        
        $data = array(
            'created_at' => $this->_createdAt($order),
            'updated_at' => $this->_updatedAt($order),
            'currency' => $this->_currency($order),
            'status' => $this->_status($order),
            'id' => $this->_id($order),
            'subtotal_price' => $this->_subtotalPrice($order),
            'taxes_included' => $this->_taxesIncluded($order),
            'total_discounts' => $this->_totalDiscounts($order),
            'total_price' => $this->_totalPrice($order),
            'total_price_usd' => $this->_totalPriceUsd($order),
            'total_tax' => $this->_totalTax($order),
            'total_shipping' => $this->_totalShipping($order),
            'total_weight' => $this->_totalWeight($order),
            'order_number' => $this->_orderNumber($order),
            'store_id' => $this->_storeId($order),
            'line_items' => $this->_lineItems($order),
            'billing_address' => $this->_address($order->getBillingAddress()),
            'shipping_address' => $this->_address($order->getShippingAddress()),
            'payment_details' => $this->_paymentDetails($order),
            'customer' => $this->_customer($order)
        );
        
        return $data;
    }
    
    protected function _getOrder($orderId)
    {
        return Mage::getModel('sales/order')->load($orderId);
    }

    protected function _createdAt(Mage_Sales_Model_Order $order)
    {
        return $order->getCreatedAt();
    }

    protected function _updatedAt(Mage_Sales_Model_Order $order)
    {
        return $order->getUpdatedAt();
    }
    
    protected function _currency(Mage_Sales_Model_Order $order)
    {
        return $order->getOrderCurrencyCode();
    }
    
    protected function _status(Mage_Sales_Model_Order $order)
    {
        return $order->getStatus();
    }
    
    protected function _id(Mage_Sales_Model_Order $order)
    {
        return $order->getId();
    }

    protected function _orderNumber(Mage_Sales_Model_Order $order)
    {
        return $order->getIncrementId();
    }

    protected function _storeId(Mage_Sales_Model_Order $order)
    {
        return $order->getStoreId();
    }
    
    protected function _subtotalPrice(Mage_Sales_Model_Order $order)
    {
        return $order->getSubtotal();
    }
    
    protected function _taxesIncluded(Mage_Sales_Model_Order $order)
    {
        return $order->getTaxAmount()? "1" : "0";
    }
    
    protected function _totalDiscounts(Mage_Sales_Model_Order $order)
    {
        return $order->getDiscountAmount();
    }
    
    protected function _totalPrice(Mage_Sales_Model_Order $order)
    {
        return $order->getGrandTotal();
    }
    
    protected function _totalPriceUsd(Mage_Sales_Model_Order $order)
    {
        return $order->getBaseGrandTotal();
    }
    
    protected function _totalTax(Mage_Sales_Model_Order $order)
    {
        return $order->getTaxAmount();
    }
    
    protected function _totalShipping(Mage_Sales_Model_Order $order)
    {
        return $order->getShippingAmount();
    }    

    protected function _totalWeight(Mage_Sales_Model_Order $order)
    {
        return $order->getWeight();
    }

    protected function _lineItems(Mage_Sales_Model_Order $order)
    {
        $items = array();
        
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            
            $items[] = $this->_item($order, $item);
        }
        
        return $items;
    }
    
    protected function _item(Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Item $item)
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getSku());

        $data = array(
            'product_id' => $this->_productId($item),
            'name' => $this->_productName($item),
            'url' => $this->_productUrl($item),
            'image' => $product?$product->getImageUrl():'',
            'sku' => $this->_sku($item),
            'qty' => $item->getQtyOrdered(),
            'qty_refunded' => $item->getQtyRefunded(),
            'price' => $this->_price($item),
            'sale_expires' => $product && $product->getSpecialToDate() ? $product->getSpecialToDate() : "",
            'sale_price' => $product && $product->getSpecialPrice() ? $product->getSpecialPrice() : $this->_price($item),
            'cost' => $this->_cost($item),
            'discount_amount' => $this->_discountAmount($item),
            'amount_refunded' => $this->_amountRefunded($item),
            'row_total' => $this->_rowTotal($item),
            'source' => 'magento',
            'status' => $product?$product->getStatus():"0",
            'tags' => $this->_tags($item),
            'categories' => $this->_categories($item),
            'attributes' => $this->_attributes($item, array(
                'type_id',
                'entity_type_id',
                'entity_id',
                'old_id',
                'attribute_set_id',
                'name',
                'sku',
                'status',
                'price',
                'special_price',
                'cost',
                'url_key',
                'url_path',
                'category_ids',
                'required_options',
                'has_options',
                'image',
                'image_label',
                'small_image',
                'small_image_label',
                'thumbnail',
                'thumbnail_label',
                'media_gallery',
                'gallery',
                'custom_design',
                'custom_design_from',
                'custom_design_to',
                'custom_layout_update',
                'page_layout',
                'options_container'
            ))
            
        );

        if ($item->getProductType() == 'configurable') {
            $data['options'] = $this->_itemOptions($item);
            $data['configurable_product_data'] = array(
                'sku' => $this->_configurableSku($item),
                'name' => $this->_configurableName($item)
            );
        }

        return $data;
    }

    protected function _productId(Mage_Sales_Model_Order_Item $item)
    {        
        return $item->getProductId();
    }

    protected function _sku(Mage_Sales_Model_Order_Item $item)
    {
        $sku = $item->getProduct()->getSku();
        
        if ($item->getProductType() == 'configurable') {
            $options = $item->getProductOptions();
            
            $sku = $options['simple_sku'];
        }

        return $sku ? $sku : $item->getSku();
    }
    
    protected function _productName(Mage_Sales_Model_Order_Item $item)
    {
        if ($item->getProductType() == 'configurable') {
            $sku = $this->_sku($item);

            $product = Mage::getModel('catalog/product');
            $product->load($product->getIdBySku($sku));
            
            return $product->getName();
        }
        
        return $item->getName();
    }

    protected function _price(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getPrice();
    }
    
    protected function _cost(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getBaseCost();
    }
    
    protected function _discountAmount(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getDiscountAmount();
    }
    
    protected function _amountRefunded(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getAmountRefunded();
    }
    
    protected function _rowTotal(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getRowTotal();
    }

    protected function _productUrl(Mage_Sales_Model_Order_Item $item)
    {
        if ($item->getParentItemId()) {
            $item = Mage::getModel('sales/order_item')->load($item->getParentItemId());
        }
        
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        return $product->getId() ? $product->getProductUrl() : Mage::app()->getStore($item->getStoreId())->getBaseUrl();
    }
    
    protected function _tags(Mage_Sales_Model_Order_Item $item)
    {
        if ($item->getParentItemId()) {
            $item = Mage::getModel('sales/order_item')->load($item->getParentItemId());
        }
        
        $productTags = array();
        $tagCollection = Mage::getModel('tag/tag')->getResourceCollection()
            ->addPopularity()
            ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
            ->addProductFilter($item->getProductId())
            ->setFlag('relation', true)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->setActiveFilter()
            ->load();
        $tags = $tagCollection->getItems();

        foreach ($tags as $tag) {
            $productTags[] = $tag->getName();
        }
        
        return $productTags;
    }
    
    protected function _categories(Mage_Sales_Model_Order_Item $item)
    {
        $product = $item->getProduct();
        
        if (!$product || !$product->getId()) {
            return array();
        }
        
        $data = array();
        
        $categories = $product->getCategoryCollection()
            ->addAttributeToSelect('name');
        foreach($categories as $category) {
            $data[] = array(
                "id" => $category->getId(),
                "name" => $category->getName()
            );
        }
    
        return $data;
    }

    protected function _attributes(Mage_Sales_Model_Order_Item $item, array $exclude = array())
    {
        $product = Mage::getModel('catalog/product');

        if ($item->getProductType() == 'configurable') {
            $sku = $this->_sku($item);

            $product->load($product->getIdBySku($sku));
        } else {
            $product = $item->getProduct();
        }

        $data = array();
        $attributes = $product->getAttributes();
        
        foreach ($attributes as $attribute) {            
            $key = $attribute->getAttributeCode();
            
            if (in_array($key, $exclude)) {
                continue;
            }
            
            $value = $product->getResource()->getAttribute($attribute->getAttributeCode())->getFrontend()->getValue($product);
            
            $data[$key] = $value;
            
        }
        
        return $data;
    }
    
    protected function _address(Mage_Customer_Model_Address_Abstract $address)
    {
        if (!$address) {
            return array();
        }
        
        return array(
            'address1' => $address->getStreet(1),
            'address2' => $address->getStreet(2),
            'city' => $address->getCity(),
            'company' => $address->getCompany(),
            'first_name' => $address->getFirstname(),
            'last_name' => $address->getLastname(),
            'phone' => $address->getTelephone(),
            'state' => $address->getRegion(),
            'zip' => $address->getPostcode(),
            'country_code' => $address->getCountryId(),
        );
    }

    protected function _customer(Mage_Sales_Model_Order $order)
    {
        $customerId = $order->getCustomerId();
        
        if (!$customerId) {
            return array(
                'email' => $order->getCustomerEmail(),
                'first_name' => $order->getCustomerFirstname(),
                'last_name' => $order->getCustomerLastname(),
                'default_address' => $this->_address($order->getBillingAddress())
            );
        }
        
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $group = Mage::getModel('customer/group')->load($customer->getGroupId());

        $data = array(
            'id' => $customerId,
            'created_at' => $customer->getCreatedAt(),
            'email' => $customer->getEmail(),
            'first_name' => $customer->getFirstname(),
            'last_name' => $customer->getLastname(),
            'group' => $group->getCustomerGroupCode(),
            
        );
        
        if ($customer->getDefaultBillingAddress()) {
            $data['default_address'] = $this->_address($customer->getDefaultBillingAddress());
        }
        
        return $data;
    }
    
    protected function _paymentDetails(Mage_Sales_Model_Order $order)
    {
        $payment = $order->getPayment();
        
        if (!$payment->getCcExpYear()) {
            return array();
        }
        
        return array(
            'credit_card_number' => $payment->getCcLast4(),
            'credit_card_company' => '',
            'credit_card_exp' => $payment->getCcExpYear() . "/" . $payment->getCcExpMonth()
        );
    }

    protected function _itemOptions(Mage_Sales_Model_Order_Item $item)
    {
        $options = $item->getProductOptions();
        $info = $options['attributes_info'];
        $data = array();
        
        foreach ($info as $v) {
            $label = $v['label'];
            $data[$label] = $v['value'];
        }
        
        return $data;
    }
    
    protected function _configurableSku(Mage_Sales_Model_Order_Item $item)
    {
        $product = $item->getProduct();
        
        return $product->getSku();
    }
    
    protected function _configurableName(Mage_Sales_Model_Order_Item $item)
    {
        $product = $item->getProduct();
        
        return $product->getName();
    }
}
