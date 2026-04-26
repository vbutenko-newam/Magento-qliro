<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Qliro\QliroOne\Model\QliroOrder\Admin;

use Qliro\QliroOne\Api\Data\AdminReturnWithItemsRequestInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;
use Qliro\QliroOne\Model\Payload\PayloadConverter;

/**
 * Return With Items Request class
 */
class ReturnWithItemsRequest implements AdminReturnWithItemsRequestInterface
{
    /**
     * @var string
     */
    private string $merchantApiKey;

    /**
     * @var int
     */
    private int $paymentReference;

    /**
     * @var string
     */
    private string $requestId;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @var QliroOrderItemInterface[]
     */
    private array $orderItems;

    /**
     * @var QliroOrderItemInterface[]
     */
    private array $fees;

    /**
     * @var QliroOrderItemInterface[]
     */
    private array $discounts;

    /**
     * @var int
     */
    private int $orderId;

    /**
     * @var int
     */
    private int $paymentTransactionId;

    /**
     * @var array
     */
    private array $returns = [];

    /**
     * Class constructor
     *
     * @param PayloadConverter $payloadConverter
     */
    public function __construct(
        private readonly PayloadConverter $payloadConverter
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getMerchantApiKey(): string
    {
        return (string)$this->merchantApiKey;
    }

    /**
     * @inheritDoc
     */
    public function setMerchantApiKey($value): static
    {
        $this->merchantApiKey = (string)$value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentReference(): int
    {
        return (int)$this->paymentReference;
    }

    /**
     * @inheritDoc
     */
    public function setPaymentReference($value): static
    {
        $this->paymentReference = (int)$value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequestId(): string
    {
        return (string)$this->requestId;
    }

    /**
     * @inheritDoc
     */
    public function setRequestId($value): static
    {
        $this->requestId = (string)$value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCurrency(): string
    {
        return (string)$this->currency;
    }

    /**
     * @inheritDoc
     */
    public function setCurrency($value): static
    {
        $this->currency = (string)$value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrderItems(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function setOrderItems($orderItems): static
    {
        if (!count($orderItems)) {
            return $this;
        }

        // Convert positive discount numbers to negative
        foreach ($orderItems as $key => $orderItem) {
            if ($orderItem->getType() === QliroOrderItemInterface::TYPE_DISCOUNT) {
                $orderItem->setPricePerItemExVat(-abs($orderItem->getPricePerItemExVat()));
                $orderItem->setPricePerItemIncVat(-abs($orderItem->getPricePerItemIncVat()));
            }

        }

        $this->orderItems = $orderItems;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFees(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function setFees($value): static
    {
        $this->fees = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOrderId(int $value): static
    {
        $this->orderId = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @inheritDoc
     */
    public function setReturns(array $value): static
    {
        $this->returns = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getReturns(): array
    {
        if ($this->paymentTransactionId) {
            $this->returns['PaymentTransactionId'] = $this->paymentTransactionId;
        }
        if (is_countable($this->orderItems)) {
            $orderItems = [];
            foreach ($this->orderItems as $orderItem) {
                $innerItem = $this->payloadConverter->toArray($orderItem);
                if (!count($innerItem)){
                    continue;
                }

                $orderItems[] = $innerItem;
            }

            if (count($orderItems)) {
                $this->returns['OrderItems'] = $orderItems;
            }
        }

        if (is_countable($this->fees)) {
            $fees = [];
            foreach ($this->fees as $fee) {
                $innerItem = $this->payloadConverter->toArray($fee);
                if (!count($innerItem)){
                    continue;
                }

                $fees[] = $innerItem;
            }

            if (count($fees)) {
                $this->returns['Fees'] = $fees;
            }
        }

        if (is_countable($this->discounts)) {
            $discounts = [];
            foreach ($this->discounts as $discount) {
                $innerItem = $this->payloadConverter->toArray($discount);
                if (!count($innerItem)){
                    continue;
                }

                $discounts[] = $innerItem;
            }

            if (count($discounts)) {
                $this->returns['Discounts'] = $discounts;
            }
        }

        return $this->returns;
    }

    /**
     * @inheritDoc
     */
    public function setPaymentTransactionId(int $value): static
    {
        $this->paymentTransactionId = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentTransactionId(): int
    {
        return $this->paymentTransactionId;
    }

    /**
     * @inheritDoc
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    /**
     * @inheritDoc
     */
    public function setDiscounts($value): static
    {
        $this->discounts = $value;

        return $this;
    }
}
