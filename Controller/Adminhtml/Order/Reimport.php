<?php
/**
 * Copyright 2017 Lengow SAS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Lengow
 * @package     Lengow_Connector
 * @subpackage  Controller
 * @author      Team module <team-module@lengow.com>
 * @copyright   2017 Lengow SAS
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Lengow\Connector\Controller\Adminhtml\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Sales\Model\OrderFactory;
use Lengow\Connector\Model\Import\OrderFactory as LengowOrderFactory;

class Reimport extends Action
{
    /**
     * @var OrderFactory Magento order factory instance
     */
    protected $_orderFactory;

    /**
     * @var LengowOrderFactory Lengow order factory instance
     */
    protected $_lengowOrderFactory;

    /**
     * Constructor
     *
     * @param Context $context Magento action context instance
     * @param OrderFactory $orderFactory Magento order factory instance
     * @param LengowOrderFactory $lengowOrderFactory Lengow order factory instance
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        LengowOrderFactory $lengowOrderFactory
    )
    {
        $this->_orderFactory = $orderFactory;
        $this->_lengowOrderFactory = $lengowOrderFactory;
        parent::__construct($context);
    }

    /**
     * Resend action
     *
     * @return Redirect
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $lengowOrderId = $this->getRequest()->getParam('lengow_order_id');
        $order = $this->_orderFactory->create()->load((int)$orderId);
        $lengowOrder = $this->_lengowOrderFactory->create()->load((int)$lengowOrderId);
        $newOrderId = $this->_lengowOrderFactory->create()->cancelAndReImportOrder($order, $lengowOrder);
        $newOrderId = !$newOrderId ? $orderId : $newOrderId;
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order/view', ['order_id' => $newOrderId]);
    }
}
