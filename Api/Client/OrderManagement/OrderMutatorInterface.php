<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Qliro\QliroOne\Api\Client\OrderManagement;

use Qliro\QliroOne\Api\Data\AdminCancelOrderRequestInterface;
use Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterface;
use Qliro\QliroOne\Api\Data\AdminTransactionResponseInterface;
use Qliro\QliroOne\Api\Data\AdminUpdateMerchantReferenceRequestInterface;

/**
 * ISP sub-interface: mutating operations on a QliroOne order.
 *
 * @api
 */
interface OrderMutatorInterface
{
    /**
     * Send a "Mark items as shipped" request
     *
     * @param AdminMarkItemsAsShippedRequestInterface $request
     * @param int|null $storeId
     * @return AdminTransactionResponseInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function markItemsAsShipped(AdminMarkItemsAsShippedRequestInterface $request, int $storeId = null): AdminTransactionResponseInterface;

    /**
     * Cancel admin QliroOne order
     *
     * @param AdminCancelOrderRequestInterface $request
     * @param int|null $storeId
     * @return AdminTransactionResponseInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function cancelOrder(AdminCancelOrderRequestInterface $request, int $storeId = null): AdminTransactionResponseInterface;

    /**
     * Update QliroOne order merchant reference
     *
     * @param AdminUpdateMerchantReferenceRequestInterface $request
     * @param int|null $storeId
     * @return AdminTransactionResponseInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function updateMerchantReference(AdminUpdateMerchantReferenceRequestInterface $request, int $storeId = null): AdminTransactionResponseInterface;
}
