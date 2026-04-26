<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Qliro\QliroOne\Model\Method\QliroOne;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order;
use Qliro\QliroOne\Api\Admin\OrderServiceInterface;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Command\ResultInterface;
use Qliro\QliroOne\Model\Config;

/**
 * Class Capture for QliroOne payment method
 */
readonly class Capture implements CommandInterface
{
    /**
     * Class constructor
     *
     * @param OrderServiceInterface $qliroManagement
     * @param Config $qliroConfig
     */
    public function __construct(
        private OrderServiceInterface $qliroManagement,
        private Config                $qliroConfig
    ) {
    }

    /**
     * Capture command
     *
     * @param array $commandSubject
     *
     * @return ResultInterface|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(array $commandSubject): ?ResultInterface
    {
        /** @var InfoInterface $payment */
        $payment = $commandSubject['payment']->getPayment();
        $amount = $commandSubject['amount'];

        try {
            /** @var Order $order */
            $order = $payment->getOrder();
            if ($this->qliroConfig->shouldCaptureOnInvoice($order ? $order->getStoreId() : null)) {
                $this->qliroManagement->captureByInvoice($payment, $amount);
            }
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __('Unable to capture payment for this order.'),
                $exception
            );
        }

        return null;
    }
}
