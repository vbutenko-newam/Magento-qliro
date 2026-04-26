<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Qliro\QliroOne\Model\QliroOrder\Admin\Builder\Handler;

use Qliro\QliroOne\Api\Admin\Builder\OrderItemHandlerInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory;
use Qliro\QliroOne\Model\Formatter\PriceFormatter;

/**
 * Shipping Fee Handler class for order items builder
 */
class ShippingFeeHandler implements OrderItemHandlerInterface
{
    const MERCHANT_REFERENCE_CODE_FIELD = 'qliro_shipping_merchant_ref';

    /**
     * Class constructor
     *
     * @param QliroOrderItemInterfaceFactory $qliroOrderItemFactory
     * @param PriceFormatter $priceFormatter
     */
    public function __construct(
        private readonly QliroOrderItemInterfaceFactory $qliroOrderItemFactory,
        private readonly PriceFormatter $priceFormatter
    ) {
    }

    /**
     * Handle specific type of order items and add them to the QliroOne order items list
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $orderItems
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function handle(array $orderItems, \Magento\Sales\Api\Data\OrderInterface $order): array
    {
        // @todo Handle invoiced and refunded shipping
        if (!$order->getFirstCaptureFlag()) {
            return $orderItems;
        }

        $paymentAdditionalInfo = $order->getPayment()->getAdditionalInformation();
        $merchantReference = $paymentAdditionalInfo[self::MERCHANT_REFERENCE_CODE_FIELD] ?? false;

        $inclTax = (float)$order->getShippingInclTax() - $order->getShippingDiscountAmount();
        $exclTax = $inclTax - $order->getShippingTaxAmount();

        $formattedInclAmount = $this->priceFormatter->format($inclTax);
        $formattedExclAmount = $this->priceFormatter->format($exclTax);

        if ($merchantReference) {
            /** @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $qliroOrderItem */
            $qliroOrderItem = $this->qliroOrderItemFactory->create();

            $qliroOrderItem->setMerchantReference($merchantReference);
            $qliroOrderItem->setDescription($merchantReference);
            $qliroOrderItem->setType(QliroOrderItemInterface::TYPE_SHIPPING);
            $qliroOrderItem->setQuantity(1);
            $qliroOrderItem->setPricePerItemIncVat($formattedInclAmount);
            $qliroOrderItem->setPricePerItemExVat($formattedExclAmount);
            $qliroOrderItem->setMetadata(['qliro' => 'checkout']);

            $orderItems[] = $qliroOrderItem;
        }

        return $orderItems;
    }
}
