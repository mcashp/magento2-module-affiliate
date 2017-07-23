<?php

namespace Mcashp\Affiliate\Helper;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Mcashp\Affiliate\Cookie\TrackingCookie;

/**
 * Class AffiliateHelper
 *
 * @package Mcashp\Affiliate\Helper
 */
class AffiliateHelper extends AbstractHelper
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

    const CONFIG_COMMISSION     = 'mcashpaffiliate/general/commission';
    const CONFIG_COMMISSION_MIN = 3;
    const CONFIG_COMMISSION_MAX = 100;

    const CONFIG_COOKIE_NAME         = 'mcps';
    const CONFIG_COOKIE_LIFETIME     = 'mcashpaffiliate/general/cookie_lifetime';
    const CONFIG_COOKIE_LIFETIME_MIN = 4;
    const CONFIG_COOKIE_LIFETIME_MAX = 6240;


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Mcashp\Affiliate\Cookie\TrackingCookie
     */
    protected $_trackingCookie;

    /**
     * @var \Magento\Customer\Model\Session $_customerSession
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session $_checkoutSession
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface $_customerRepository
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface $_quoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_salesOrderRepository
     */
    protected $_orderRepository;


    /**
     * AffiliateHelper constructor.
     *
     * @param \Magento\Framework\App\Helper\Context             $context
     * @param \Mcashp\Affiliate\Cookie\TrackingCookie           $trackingCookie
     * @param \Magento\Customer\Model\Session                   $customerSession
     * @param \Magento\Checkout\Model\Session                   $checkoutSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface        $quoteRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface       $orderRepository
     */
    public function __construct(
        Context                     $context,
        TrackingCookie              $trackingCookie,
        CustomerSession             $customerSession,
        CheckoutSession             $checkoutSession,
        CustomerRepositoryInterface $customerRepository,
        CartRepositoryInterface     $quoteRepository,
        OrderRepositoryInterface    $orderRepository
    ) {
        parent::__construct($context);

        $this->_scopeConfig        = $context->getScopeConfig();
        $this->_trackingCookie     = $trackingCookie;
        $this->_customerSession    = $customerSession;
        $this->_checkoutSession    = $checkoutSession;
        $this->_customerRepository = $customerRepository;
        $this->_quoteRepository    = $quoteRepository;
        $this->_orderRepository    = $orderRepository;
    }

    /**
     * @return bool
     */
    public function getConfigActive()
    {
        return (bool) $this->getConfig(self::CONFIG_ACTIVE);
    }

    /**
     * @return mixed
     */
    public function getConfigCommission()
    {
        return $this->getConfig(self::CONFIG_COMMISSION);
    }

    /**
     * @return mixed
     */
    public function getConfigCookieLifetime()
    {
        return $this->getConfig(self::CONFIG_COOKIE_LIFETIME);
    }

    /**
     * @param string $config
     * @param string $scope
     *
     * @return mixed
     */
    protected function getConfig($config, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->_scopeConfig->getValue($config, $scope);
    }

    /**
     * @return string|null
     */
    public function getTrackingRequest()
    {
        $tracking = $this->_request->getParam(TrackingCookie::COOKIE_NAME);
        if (1 !== preg_match(self::ATTRIBUTE_TRACKING_REGEX, $tracking)) {
            return null;
        }

        return $tracking;
    }

    /**
     * @return string
     */
    public function getTrackingCookie()
    {
        return $this->_trackingCookie->get();
    }

    /**
     * @return bool
     */
    public function checkTrackingRequest()
    {
        $tracking = $this->getTrackingRequest();
        if (null === $tracking) {
            return false;
        }

        if ($this->getConfigActive()) {
            $this->setTracking($tracking);
        }

        return true;
    }

    /**
     * @param string   $tracking
     * @param int|null $customerId
     * @param int|null $quoteId
     */
    public function setTracking($tracking, $customerId = null, $quoteId = null)
    {
        if (null === $customerId) {
            $customerId = $this->_customerSession->getCustomer()->getEntityId();
        }

        if (null === $quoteId) {
            $quoteId = $this->_checkoutSession->getQuote()->getEntityId();
        }

        $this->_trackingCookie->set($tracking);
        $this->setCustomerTracking($customerId, $tracking);
        $this->setQuoteTracking($quoteId, $tracking);
    }

    /**
     * @param string|null $customerId
     *
     * @return string|null
     */
    public function getTracking($customerId = null)
    {
        $tracking = $this->getTrackingCookie();
        if ( ! $tracking) {
            $tracking = $this->getCustomerTracking($customerId);
        }

        return $tracking;
    }

    /**
     * @param string|null $customerId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer($customerId = null)
    {
        if (null === $customerId && $this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getCustomer()->getEntityId();
        }

        if ( ! is_numeric($customerId)) {
            return null;
        }

        return $this->_customerRepository->getById($customerId);
    }

    /**
     * @param string|null $customerId
     *
     * @return string|null
     */
    public function getCustomerTracking($customerId = null)
    {
        if ( ! $customer = $this->getCustomer($customerId)) {
            return null;
        }

        return $customer->getCustomAttribute(self::ATTRIBUTE_TRACKING);
    }

    /**
     * @param int    $customerId
     * @param string $tracking
     *
     * @return bool
     */
    public function setCustomerTracking($customerId, $tracking)
    {
        if ( ! $customerId || ! $tracking) {
            return false;
        }

        if ( ! $customer = $this->getCustomer($customerId)) {
            return false;
        }

        $customer->setCustomAttribute(self::ATTRIBUTE_TRACKING, $tracking);
        $this->_customerRepository->save($customer);

        return true;
    }

    /**
     * @param string|null $quoteId
     *
     * @return \Magento\Quote\Api\Data\CartInterface|null
     */
    public function getQuote($quoteId = null)
    {
        if (null === $quoteId) {
            $quoteId = $this->_checkoutSession->getQuote()->getEntityId();
        }

        if ( ! is_numeric($quoteId)) {
            return null;
        }

        return $this->_quoteRepository->get($quoteId);
    }

    /**
     * @param int    $quoteId
     * @param string $tracking
     *
     * @return bool
     */
    public function setQuoteTracking($quoteId, $tracking)
    {
        if ( ! $quoteId || ! $tracking) {
            return false;
        }

        if ( ! $quote = $this->getQuote($quoteId)) {
            return false;
        }

        $quote->setMcashpTracking($tracking);
        $this->_quoteRepository->save($quote);

        return true;
    }

    /**
     * @param string|null $orderId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     */
    public function getOrder($orderId = null)
    {
        if ( ! is_numeric($orderId)) {
            return null;
        }

        return $this->_orderRepository->get($orderId);
    }

    /**
     * @param string $action
     * @param string $tracking
     * @param string $payoutAmount
     * @param string $payoutAmountFormat
     * @param string $type
     *
     * @return mixed|string
     */
    public function mcashpEvent($action, $tracking, $payoutAmount, $payoutAmountFormat = 'float', $type = 'rev')
    {
        $apiKey = $this->getConfig('mcashpcore/webmaster/api');
        if ( ! $apiKey) {
            return false;
        }

        $data = array(
            'k' => $action,
            'type' => $type,
            'tracking' => $tracking,
            'payout_amount' => $payoutAmount,
            'payout_amount_format' => $payoutAmountFormat,
        );

        $url = 'https://www.mcashp.com/api/' . $apiKey . '/stats/new';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);

        return $result;
    }
}
