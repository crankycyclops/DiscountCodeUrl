# Discount Code URL Module for Magento 2

This module allows discount codes to be applied to a browser session automatically via a query string. For example, with the default settings, the following will make sure the MYDISCOUNTCODE coupon is applied during checkout:

https://url.to.my.store?discount=MYDISCOUNTCODE

If the URL Path feature is turned on, then discount codes can also be applied via a URL like the following:

https://url.to.my.store/path/to/page/discount/MYDISCOUNTCODE

This URL will apply the discount code MYDISCOUNTCODE to the session and route internally to https://url.to.my.store/path/to/page without any redirection. This feature is experimental and relies on a bit of hackery, so if you turn it on, make sure to test your site thoroughly for incompatibilities.

The query string parameter and URL path can be configured via the URL Parameter admin setting. By default, this has a value of "discount", but if you change it to, say, "coupon", then the following URLs will apply the code to the session instead:

https://url.to.my.store?coupon=MYDISCOUNTCODE

https://url.to.my.store/path/to/page/coupon/MYDISCOUNTCODE (if the URL Path feature is turned on)

You can set the lifetime of the discount code cookie in the admin via the Cookie Lifetime setting. By default this is 0, which means the cookie will survive and the browser will remember the coupon until the window or tab is closed. A value greater than 0 will indicate the number of seconds that the coupon should be remembered (for example, 3600 would mean that the coupon will be remembered for an hour.)

## Getting Started

### Prerequisites

I've only tested this on Magento 2.3 (I'm developing this primarily for my own needs but am making it open source to share with anyone who might find it useful), but it should work on 2.2 as well, and quite possibly on 2.1. If it doesn't, post an issue, and if I have time I'll take a look--although, since 2.2 is already EOL as of 2.2.10, you should probably be moving to 2.3 anyway...right? O:-)

I use language features that require at least PHP 7.1, but since PHP 7.1 is already EOL, that (ideally) shouldn't be a problem for anyone.

### Installing

I don't have this setup via composer yet, so if you want to install it, you'll have to do something akin to the following steps:

```
mkdir -p /path/to/store/app/code/Crankycyclops
cd /path/to/store/app/code/Crankycyclops
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
