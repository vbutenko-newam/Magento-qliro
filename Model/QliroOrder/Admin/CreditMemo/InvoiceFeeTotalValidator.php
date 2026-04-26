<?php
declare(strict_types=1);

namespace Qliro\QliroOne\Model\QliroOrder\Admin\CreditMemo;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Qliro\QliroOne\Api\Admin\CreditMemo\InvoiceFeeTotalValidatorInterface;

class InvoiceFeeTotalValidator implements InvoiceFeeTotalValidatorInterface
{
    /**
     * @var CreditmemoInterface|null
     */
    protected ?CreditmemoInterface $creditMemo = null;

    /**
     * @var float|null
     */
    private ?float $totalFee = null;

    /**
     * @inheritDoc
     */
    public function validate(bool $feeIsAddedAsTotal = true, bool $useQtyRefundedOnly = false): bool
    {
        if (!$this->getCreditMemo()) {
            return false;
        }

        if ($this->getOrderFeesTotal() == 0) {
            return false;
        }

        if ($useQtyRefundedOnly) {
            return bccomp(
                $this->getCreditMemo()->getInvoice()->getBaseTotalRefunded(),
                $this->getCreditMemo()->getInvoice()->getGrandTotal()
            ) != -1;
        }

        $invoiceGrandTotal = $this->getCreditMemo()->getInvoice()->getGrandTotal() - $this->getOrderFeesTotal();

        $totalRefunded = floatval($this->getCreditMemo()->getInvoice()->getBaseTotalRefunded());
        $totalCreditMemo = floatval($this->getCreditMemo()->getGrandTotal());
        $fee = $this->getOrderFeesTotal();
        $orderTotalRefunded = $feeIsAddedAsTotal ? $totalRefunded + $totalCreditMemo - $fee : $totalRefunded + $totalCreditMemo;

        if (bccomp($orderTotalRefunded, $invoiceGrandTotal) != -1) {
            return true;
        }

        return false;
    }

    /**
     * Calculates and retrieves the total fees associated with an order.
     *
     * If the fees have already been calculated and cached in the $totalFee property,
     * the method returns this value directly. Otherwise, it calculates the total
     * by summing up the prices (including VAT) from the payment's additional information,
     * caches the result, and then returns it.
     *
     * @return float The total fees for the order, including VAT.
     */
    private function getOrderFeesTotal(): float
    {
        if (!$this->totalFee) {
            $feeTotal = floatval(0);
            $qlirooneFees = $this->getCreditMemo()->getOrder()->getPayment()->getAdditionalInformation('qliroone_fees');
            if (!is_array($qlirooneFees) || !count($qlirooneFees)) {
                $this->totalFee = $feeTotal;
                return $this->totalFee;
            }

            foreach ($qlirooneFees as $qlirooneFee) {
                $this->totalFee = $this->totalFee + floatval($qlirooneFee['PricePerItemIncVat']);
            }

        }

        return $this->totalFee;
    }

    /**
     * @inheritDoc
     */
    public function setCreditMemo(CreditmemoInterface $creditMemo): static
    {
        $this->creditMemo = $creditMemo;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreditMemo(): ?CreditmemoInterface
    {
        return $this->creditMemo;
    }
}
