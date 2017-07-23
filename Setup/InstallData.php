<?php

namespace Mcashp\Affiliate\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Mcashp\Affiliate\Helper\AffiliateHelper;

/**
 * Class InstallData
 *
 * @package Mcashp\Affiliate\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $_customerSetupFactory;

    /**
     * @var \Magento\Quote\Setup\QuoteSetupFactory
     */
    private $_quoteSetupFactory;

    /**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    private $_salesSetupFactory;


    /**
     * InstallData constructor.
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Quote\Setup\QuoteSetupFactory       $quoteSetupFactory
     * @param \Magento\Sales\Setup\SalesSetupFactory       $salesSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        QuoteSetupFactory    $quoteSetupFactory,
        SalesSetupFactory    $salesSetupFactory
    ) {
        $this->_customerSetupFactory = $customerSetupFactory;
        $this->_quoteSetupFactory    = $quoteSetupFactory;
        $this->_salesSetupFactory    = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $trackingAttribute = AffiliateHelper::ATTRIBUTE_TRACKING;
        $trackingOptions = ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false];

        $commissionAttribute = AffiliateHelper::ATTRIBUTE_COMMISSION;
        $commissionOptions = ['type' => Table::TYPE_DECIMAL, 'visible' => true, 'required' => false];

        $customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute('customer', $trackingAttribute, [
            'type' => 'varchar',
            'label' => 'MCASHP tracking',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => false,
            'position' => 333,
            'system' => false,
            'backend' => '',
            'readonly' => true, // FIXME readonly
        ]);

        /** @var \Magento\Quote\Setup\QuoteSetup $quoteSetup */
        $quoteSetup = $this->_quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute('quote', $trackingAttribute, $trackingOptions);

        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->_salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute('order', $trackingAttribute, $trackingOptions);
        $salesSetup->addAttribute('invoice', $trackingAttribute, $trackingOptions);

        $salesSetup->addAttribute('order', $commissionAttribute, $commissionOptions);
        $salesSetup->addAttribute('invoice', $commissionAttribute, $commissionOptions);
    }
}
