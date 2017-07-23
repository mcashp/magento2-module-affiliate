<?php

namespace Mcashp\Affiliate\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mcashp\Affiliate\Helper\AffiliateHelper;

/**
 * Class LoginTrackingObserver
 *
 * @package Mcashp\Affiliate\Observer\Customer
 */
class LoginTrackingObserver implements ObserverInterface
{
    /**
     * @var \Mcashp\Affiliate\Helper\AffiliateHelper
     */
    protected $_affiliateHelper;


    /**
     * LoginTrackingObserver constructor.
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

        if ( ! $tracking = $this->_affiliateHelper->getTrackingCookie()) {
            return;
        }

        $this->_affiliateHelper->setTracking($tracking, $observer->getCustomer()->getId());
    }
}
