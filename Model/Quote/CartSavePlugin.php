<?php

namespace Mcashp\Affiliate\Model\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mcashp\Affiliate\Helper\AffiliateHelper;

/**
 * Class CartSavePlugin
 *
 * @package Mcashp\Affiliate\Model\Quote
 */
class CartSavePlugin
{
    /**
     * @var \Mcashp\Affiliate\Helper\AffiliateHelper
     */
    protected $_affiliateHelper;


    /**
     * CartSavePlugin constructor.
     *
     * @param \Mcashp\Affiliate\Helper\AffiliateHelper $affiliateHelper
     */
    public function __construct(AffiliateHelper $affiliateHelper)
    {
        $this->_affiliateHelper = $affiliateHelper;
    }

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $repository
     * @param \Magento\Quote\Model\Quote                 $quote
     */
    public function beforeSave(CartRepositoryInterface $repository, Quote $quote)
    {
        $this->saveTracking($quote);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function saveTracking(Quote $quote)
    {
        if ($quote->getEntityId()) {
            return;
        }

        if ( ! $this->_affiliateHelper->getConfigActive()) {
            return;
        }

        if ( ! $tracking = $this->_affiliateHelper->getTracking()) {
            return;
        }

        $quote->setMcashpTracking($tracking);
    }
}
