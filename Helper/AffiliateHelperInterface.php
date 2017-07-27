<?php

namespace Mcashp\Affiliate\Helper;

/**
 * Interface AffiliateHelperInterface
 *
 * @package Mcashp\Affiliate\Helper
 */
interface AffiliateHelperInterface
{
    /**
     * Attributes
     */
    const ATTRIBUTE_TRACKING       = 'mcashp_tracking';
    const ATTRIBUTE_TRACKING_REGEX = '/^[0-9a-zA-Z_-]{1,35}$/';

    const ATTRIBUTE_COMMISSION = 'mcashp_commission';

    /**
     * Configuration
     */
    const CONFIG_ACTIVE = 'mcashpaffiliate/general/active';

    const CONFIG_TEST = 'mcashpaffiliate/general/test';

    const CONFIG_COMMISSION     = 'mcashpaffiliate/general/commission';
    const CONFIG_COMMISSION_MIN = 3;
    const CONFIG_COMMISSION_MAX = 100;

    const CONFIG_COOKIE_NAME         = 'mcps';
    const CONFIG_COOKIE_LIFETIME     = 'mcashpaffiliate/general/cookie_lifetime';
    const CONFIG_COOKIE_LIFETIME_MIN = 4;
    const CONFIG_COOKIE_LIFETIME_MAX = 6240;
    const CONFIG_COOKIE_LIFETIME_MP  = 604800;

    /**
     * @return bool
     */
    public function getConfigActive();

    /**
     * @return bool
     */
    public function getConfigTest();

    /**
     * @return mixed
     */
    public function getConfigCommission();

    /**
     * @return mixed
     */
    public function getConfigCookieLifetime();

    /**
     * @return string|null
     */
    public function getTrackingRequest();

    /**
     * @return string
     */
    public function getTrackingCookie();

    /**
     * @return bool
     */
    public function checkTrackingRequest();

    /**
     * @param string   $tracking
     * @param int|null $customerId
     * @param int|null $quoteId
     */
    public function setTracking($tracking, $customerId = null, $quoteId = null);

    /**
     * @param string|null $customerId
     *
     * @return string|null
     */
    public function getTracking($customerId = null);

    /**
     * @param string|null $customerId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer($customerId = null);

    /**
     * @param string|null $customerId
     *
     * @return string|null
     */
    public function getCustomerTracking($customerId = null);

    /**
     * @param int    $customerId
     * @param string $tracking
     *
     * @return bool
     */
    public function setCustomerTracking($customerId, $tracking);

    /**
     * @param string|null $quoteId
     *
     * @return \Magento\Quote\Api\Data\CartInterface|null
     */
    public function getQuote($quoteId = null);

    /**
     * @param int    $quoteId
     * @param string $tracking
     *
     * @return bool
     */
    public function setQuoteTracking($quoteId, $tracking);

    /**
     * @param string|null $orderId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     */
    public function getOrder($orderId = null);

    /**
     * @param string $action
     * @param string $tracking
     * @param string $payoutAmount
     * @param string $payoutAmountFormat
     * @param string $type
     *
     * @return mixed|string
     */
    public function mcashpEvent($action, $tracking, $payoutAmount, $payoutAmountFormat = 'float', $type = 'rev');
}
