<?php

namespace Mcashp\Affiliate\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mcashp\Affiliate\Helper\AffiliateHelper;

/**
 * Class OrderInvoicePayObserver
 *
 * @package Mcashp\Affiliate\Observer\Sales
 */
class OrderInvoicePayObserver implements ObserverInterface
{
    /**
     * @var \Mcashp\Affiliate\Helper\AffiliateHelper
     */
    protected $_affiliateHelper;


    /**
     * OrderInvoicePayObserver constructor.
     *
     * @param \Mcashp\Affiliate\Helper\AffiliateHelper $affiliateHelper
     */
    public function __construct(AffiliateHelper $affiliateHelper)
    {
        $this->_affiliateHelper = $affiliateHelper;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ( ! $this->_affiliateHelper->getConfigActive()) {
            return;
        }

        $invoice = $observer->getEvent()->getInvoice();
        if ( ! $order = $invoice->getOrder()) {
            return;
        }

        $tracking = $order->getMcashpTracking();
        $commission = $order->getMcashpCommission();

        if ( ! $tracking || ! $commission) {
            return;
        }

        $total = $invoice->getBaseSubtotalInclTax() / 100 * $commission;
        $currency = $invoice->getBaseCurrencyCode();

        $invoice->setMcashpTracking($tracking);
        $invoice->setMcashpCommission($total);

        $res = $this->_affiliateHelper->mcashpEvent('sale', $tracking, $total);
    }
}
