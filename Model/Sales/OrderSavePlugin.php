<?php

namespace Mcashp\Affiliate\Model\Sales;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Mcashp\Affiliate\Helper\AffiliateHelper;

/**
 * Class OrderSavePlugin
 *
 * @package Mcashp\Affiliate\Model\Sales
 */
class OrderSavePlugin
{
    /**
     * @var \Mcashp\Affiliate\Helper\AffiliateHelper
     */
    protected $_affiliateHelper;


    /**
     * OrderSavePlugin constructor.
     *
     * @param \Mcashp\Affiliate\Helper\AffiliateHelper $affiliateHelper
     */
    public function __construct(AffiliateHelper $affiliateHelper)
    {
        $this->_affiliateHelper = $affiliateHelper;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $repository
     * @param \Magento\Sales\Model\Order                  $order
     */
    public function beforeSave(OrderRepositoryInterface $repository, Order $order)
    {
        $this->saveTracking($order);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function saveTracking(Order $order)
    {
        if ($order->getEntityId() || $order->getMcashpTracking()) {
            return;
        }

        if ( ! $this->_affiliateHelper->getConfigActive()) {
            return;
        }

        $commission = $this->_affiliateHelper->getConfigCommission();
        if ($commission < AffiliateHelper::CONFIG_COMMISSION_MIN) {
            $commission = AffiliateHelper::CONFIG_COMMISSION_MIN;
        } elseif ($commission > AffiliateHelper::CONFIG_COMMISSION_MAX) {
            $commission = AffiliateHelper::CONFIG_COMMISSION_MAX;
        }

        $tracking = null;
        if ($quote = $this->_affiliateHelper->getQuote($order->getQuoteId() ?: false)) {
            $tracking = $quote->getMcashpTracking($tracking);
        }

        if ( ! $tracking) {
            $tracking = $this->_affiliateHelper->getTracking();
        }

        $order->setMcashpTracking($tracking);
        $order->setMcashpCommission($commission);

        $this->_affiliateHelper->mcashpEvent('lead', $tracking, 0);
    }
}
