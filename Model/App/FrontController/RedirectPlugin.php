<?php

namespace Mcashp\Affiliate\Model\App\FrontController;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Mcashp\Affiliate\Helper\AffiliateHelper;

/**
 * Class RedirectPlugin
 *
 * @package Mcashp\Affiliate\Model\App\FrontController
 */
class RedirectPlugin
{
    /**
     * @var \Mcashp\Affiliate\Helper\AffiliateHelper
     */
    protected $_affiliateHelper;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $_resultFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;


    /**
     * RedirectPlugin constructor.
     *
     * @param \Mcashp\Affiliate\Helper\AffiliateHelper    $affiliateHelper
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\UrlInterface             $url
     */
    public function __construct(AffiliateHelper $affiliateHelper, ResultFactory   $resultFactory, UrlInterface $url)
    {
        $this->_affiliateHelper = $affiliateHelper;
        $this->_resultFactory   = $resultFactory;
        $this->_url             = $url;
    }

    /**
     * @param \Magento\Framework\App\FrontControllerInterface $subject
     * @param callable                                        $proceed
     * @param \Magento\Framework\App\RequestInterface         $request
     *
     * @return \Magento\Framework\Interception\InterceptorInterface
     */
    public function aroundDispatch(FrontControllerInterface $subject, callable $proceed, RequestInterface $request)
    {
        if ( ! $this->_affiliateHelper->checkTrackingRequest()) {
            return $proceed($request);
        }

        $queryParameters = $_GET;
        unset($queryParameters[AffiliateHelper::CONFIG_COOKIE_NAME]);

        $url = strtok($this->_url->getCurrentUrl(), '?');
        $query = http_build_query($queryParameters);

        $url = $url . ($query ? '?' . $query : '');

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this
            ->_resultFactory
            ->create(ResultFactory::TYPE_REDIRECT)
        ;

        $resultRedirect->setUrl($url);
        $resultRedirect->setHeader('Cache-Control', 'no-cache, no-store');

        return $resultRedirect;
    }
}
