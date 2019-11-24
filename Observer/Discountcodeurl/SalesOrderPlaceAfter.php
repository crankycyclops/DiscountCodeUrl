<?php

/**
 * @category  Crankycyclops
 * @package   Crankycyclops_DiscountCodeUrl
 * @author    James Colannino
 * @copyright Copyright (c) 2019 James Colannino
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace Crankycyclops\DiscountCodeUrl\Observer\Discountcodeurl;

class SalesOrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface {

	/**
	 * @var \Crankycyclops\DiscountCodeUrl\Helper\Config
	 */
	private $config;

	/**
	 * @var \Crankycyclops\DiscountCodeUrl\Helper\Cookie
	 */
	private $cookieHelper;

	/************************************************************************/

	/**
	 * Constructor
	 *
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper
	 */
	public function __construct(
		\Crankycyclops\DiscountCodeUrl\Helper\Config $config,
		\Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper
	) {
		$this->config = $config;
		$this->cookieHelper = $cookieHelper;
	}

	/************************************************************************/

	/**
	 * If a coupon code was set in the URL at any point during the session
	 * an an order was successfully placed, we should remove it to avoid
	 * having it get automatically applied a secon time (user will have to
	 * either enter the code manually again or browse once more to the
	 * coupon-specific URL.)
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 *
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $observer): void {

		// Once we've placed an order, we should delete the coupon cookie so
		// that the user will have to add one again if they wish to place
		// another order
		if ($this->config->isEnabled()) {
			$this->cookieHelper->deleteCookie();
		}
	}
}

