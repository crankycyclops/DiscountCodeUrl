<?php

/**
 * @category  Crankycyclops
 * @package   Crankycyclops_DiscountCodeUrl
 * @author    James Colannino
 * @copyright Copyright (c) 2019 James Colannino
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace Crankycyclops\DiscountCodeUrl\Observer\Discountcodeurl;

class CheckoutCartSaveAfter implements \Magento\Framework\Event\ObserverInterface {

	/**
	 * @var \Crankycyclops\DiscountCodeUrl\Helper\Config
	 */
	private $config;

	/**
	 * @var \Crankycyclops\DiscountCodeUrl\Helper\Cookie
	 */
	private $cookieHelper;

	/**
	 * @var \Crankycyclops\DiscountCodeUrl\Helper\Cart
	 */
	private $cartHelper;

	/************************************************************************/

	/**
	 * Constructor
	 *
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Config $config
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Cart $cartHelper
	 */
	public function __construct(
		\Crankycyclops\DiscountCodeUrl\Helper\Config $config,
		\Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper,
		\Crankycyclops\DiscountCodeUrl\Helper\Cart $cartHelper
	) {
		$this->config = $config;
		$this->cookieHelper = $cookieHelper;
		$this->cartHelper = $cartHelper;
	}

	/************************************************************************/

	/**
	 * If a coupon code was set in the URL at any point during the session,
	 * apply it as soon as the cart is created and re-apply it every time it's
	 * updated to keep the total price current.
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 *
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $observer): void {

		if ($this->config->isEnabled()) {

			$coupon = $this->cookieHelper->getCookie();

			if ($coupon) {

				$cart = $observer->getData('cart');

				if ($cart) {
					$this->cartHelper->applyCoupon($cart->getQuote(), $coupon);
				}
			}
		}
	}
}

