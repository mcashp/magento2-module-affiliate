MCASHP Magento2 Affiliate module 
================================

Let webmaster and influencer advertise your products for a given commission in %.
The payouts are automatically handled by the [MCASHP affiliate and marketing platform](https://www.mcashp.com/register/850).
It's possible to run your own instance of MCASHP as whitelabel.
For more information and support contact us by mail (hilfe@mcashp.com) or via user support form at [https://www.mcashp.com/](https://www.mcashp.com/register/850).

## Requirements
- [MCASHP webmaster account](https://www.mcashp.com/register/850)
- Magento 2 ([module for Magento1 here](https://github.com/mcashp/magento-module-affiliate))

## Installation

#### Require module

    composer require "mcashp/magento2-module-affiliate"

#### Setup module & recompile DI

    php bin/magento setup:upgrade
    php bin/magento setup:di:compile

## Configuration

#### Mandatory parameters

You find your API key on your account page at [www.mcashp.com](https://www.mcashp.com/register/850).

	Stores -> Configuration -> MCASHP
		General   -> API key
		Affiliate -> enable
