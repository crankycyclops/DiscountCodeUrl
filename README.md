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

## Known Issues

There's a really annoying bug that I'm trying to track down. I believe it's something happening in Magento core, but I can't prove that yet. Basically what happens is, the first time a discount code URL is used, the cookie header isn't sent, and so the cookie doesn't get set and the discount code doesn't get remembered. On the second request, however, and every request after, the cookie header is sent as it should be.

This is a server side issue, not something on the client side. If, for example, you request http://store.url/path/to/page/discount/TESTCODE in one browser, the cookie header won't be sent. Then, if you request that exact same URL in another browser, the cookie will be set, even though it's the first time the page has been requested in that other browser.

It's a really weird issue, and right now, I'm kind of at a loss. For now, to work around this issue, you can "prime" the module by requesting coupon code URLs you know you're going to share at least once. Doing so will ensure that the next time someone browses to that URL, the module works as expected. If you clear the cache, you'll have to "prime" the URL again.

I'm working on it...

## Authors

James Colannino - [crankycyclops](https://github.com/crankycyclops)

## License

This project is licensed under the GPL v3. See [LICENSE](LICENSE) file for details.
