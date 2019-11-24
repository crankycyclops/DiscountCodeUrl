# Discount Code URL Module for Magento 2

This module allows discount codes to be applied to a browser session automatically via a GET parameter in the URL. For example:

https://url.to.my.store?coupon=MYDISCOUNTCODE

## Getting Started

### Prerequisites

I've only tested this on Magento 2.3 (I'm developing this primarily for my own needs but am making it open source to share with anyone who might find it useful), but it should work in 2.2 as well. If it doesn't, post an issue, and if I have time I'll take a look.

I use language features that require at least PHP 7.1, but since 7.1 is already EOL, you should be using 7.2 or above anyway ;)

### Installing

I don't have this setup via composer yet, so if you want to install it, you'll have to do something akin to the following steps:

```
mkdir -p /path/to/store/app/code/Crankycyclops
cd https://github.com/crankycyclops/DiscountCodeUrl.git
git clone https://github.com/crankycyclops/DiscountCodeUrl.git
/path/to/store/bin/magento module:enable Crankycyclops_DiscountCodeUrl
/path/to/store/bin/magento setup:upgrade
```

It's also a good idea to clear your cache.

Config options can be found in Stores -> Configuration -> Customers -> Promotions -> Discount URL Settings

## Authors

James Colannino - [crankycyclops](https://github.com/crankycyclops)

## License

This project is licensed under the GPL v3. See [LICENSE](LICENSE) file for details.
