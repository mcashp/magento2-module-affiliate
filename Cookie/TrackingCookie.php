<?php

namespace Mcashp\Affiliate\Cookie;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Mcashp\Affiliate\Helper\AffiliateHelper;

/**
 * Class TrackingCookie
 *
 * @package Mcashp\Affiliate\Cookie
 */
class TrackingCookie
{
    const COOKIE_NAME         = AffiliateHelper::CONFIG_COOKIE_NAME;

    const COOKIE_LIFETIME_MP  = AffiliateHelper::CONFIG_COOKIE_LIFETIME_MP;


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;


    /**
     * TrackingCookie constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface     $scopeConfig
     * @param \Magento\Framework\Stdlib\CookieManagerInterface       $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        ScopeConfigInterface   $scopeConfig,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory  $cookieMetadataFactory
    ) {
        $this->_scopeConfig           = $scopeConfig;
        $this->_cookieManager         = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Get form key cookie
     *
     * @return string
     */
    public function get()
    {
        return $this->_cookieManager->getCookie(self::COOKIE_NAME);
    }

    /**
     * @param string $tracking
     *
     * @return void
     */
    public function set($tracking)
    {
        $lifetime = (int) $this->_scopeConfig->getValue(AffiliateHelper::CONFIG_COOKIE_LIFETIME);
        $lifetimeMin = AffiliateHelper::CONFIG_COOKIE_LIFETIME_MIN;
        $lifetimeMax = AffiliateHelper::CONFIG_COOKIE_LIFETIME_MAX;
        if ( ! is_numeric($lifetime) || ! $lifetime < $lifetimeMin) {
            $lifetime = $lifetimeMin;
        } elseif ($lifetime > $lifetimeMax) {
            $lifetime = $lifetimeMax;
        }
        $lifetime = (int) $lifetime * self::COOKIE_LIFETIME_MP;

        $metadata = $this
            ->_cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($lifetime)
            ->setPath('/')
        ;

        $this->_cookieManager->setPublicCookie(self::COOKIE_NAME, $tracking, $metadata);
    }

    /**
     * @return void
     */
    public function delete()
    {
        $this->_cookieManager->deleteCookie(
            self::COOKIE_NAME,
            $this->_cookieMetadataFactory->createCookieMetadata()
        );
    }
}
