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

The module can be installed via composer:

```
composer require crankycyclops/m2-module-discount-code-url
/path/to/store/bin/magento module:enable Crankycyclops_DiscountCodeUrl
/path/to/store/bin/magento setup:upgrade
```

It's also a good idea to clear your cache.

Config options can be found in Stores -> Configuration -> Customers -> Promotions -> Discount URL Settings

## Contributors

- James Colannino (author) - [crankycyclops](https://github.com/crankycyclops)
- Vladimir Bratukhin - [vo1](https://github.com/vo1)
- Hidayet Ok - [hidonet](https://github.com/hidonet)
- Terrapin - [TerrapinSoftware](https://github.com/TerrapinSoftware)

## How to Contribute

Want to fix a bug or implement a feature? If so, first and foremost, **THANK YOU!** It's incredible that there are people out there who find my code useful enough to contribute and I'm deeply grateful for your willingness to make this module better. As a rule, I'm very receptive to issues and PRs. I only ask that you observe the following guidelines:

1. **Always submit pull requests against the develop branch.** I only merge code into master when I'm getting ready to release.

2. **Be conservative and avoid making changes to existing code unless you're fixing something that's broken.** You might find a more elegant way to implement existing behavior, but doing so has the potential to introduce new bugs, and until the day comes when I no longer have a full-time job to worry about, I won't have time to perform a lot of additional regression testing. I rely on this module for my own production environment and will always value stability over elegance.

3. **Respect the existing code style.** I know I don't adhere to the PSR-2 guidelines that are preferred by Magento and many others. This will make people groan, but I have my own K&R-like style that I find more comfortable, and while I've chosen to open source this module to benefit others, I still want to enjoy working with my own code.

4. **Avoid mixing unrelated changes in a single PR.** For example, let's say you fixed a bug related to how a discount code is applied to the cart during checkout. Along the way, you realized you could also make the code more efficient by refactoring another unrelated class. The changes above should be split into two separate PRs so that I can examine and merge each one independently.

5. **Test your code before submitting a PR.** I'm going to do my own testing before approving and merging your code, but you should still do your best to ensure it works as expected and doesn't introduce new bugs.

6. **Avoid frivolous changes.** Frivolous changes include but are not limited to: needlessly altering the whitespace of surrounding code; renaming classes, functions, and variables without a good reason (this has the potential to introduce bugs); and altering the style of existing code just because you have different preferences.

7. **For new functionality, consider creating an issue first with the "feature request" label so we can talk about it.** If you want to implement a new feature and skip straight to the PR, that's fine, but I might not want to merge it, which would be sad if you put a lot of work into it.

8. **Be nice.** If you're rude, argumentative, or combative, I'm not going to deal with you. Open source is a collaborative experience and should be enjoyable for everyone.

## License

This project is licensed under the GPL v3. See [LICENSE](LICENSE) file for details.
