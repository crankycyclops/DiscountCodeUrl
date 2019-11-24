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
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	private $quoteRepository;

	/**
	 * @var \Crankycyclops\DiscountCodeUrl\Helper\Cookie
	 */
	private $cookieHelper;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	private $messageManager;

	/************************************************************************/

	/**
	 * Constructor
	 *
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper
	 */
	public function __construct(
		\Crankycyclops\DiscountCodeUrl\Helper\Config $config,
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		\Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper,
		\Magento\Framework\Message\ManagerInterface $messageManager
	) {
		$this->config = $config;
		$this->quoteRepository = $quoteRepository;
		$this->cookieHelper = $cookieHelper;
		$this->messageManager = $messageManager;
	}

	/************************************************************************/

	/**
	 * If a coupon code was set in the URL at any point during the session,
	 * apply it as soon as the cart is created.
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

					try {
						$cart->getQuote()->setCouponCode($coupon);
						$this->quoteRepository->save($cart->getQuote()->collectTotals());
					}

					catch (LocalizedException $e) {
						$this->messageManager->addError(
							__("Discount code <strong>$coupon</strong> couldn't be applied: " .
								$e->getMessage())
						);
					}

					catch (\Exception $e) {
						$this->messageManager->addError(
							__("Discount code <strong>$coupon</strong> couldn't be applied or is invalid")
						);
					}

					if ($cart->getQuote()->getCouponCode() != $coupon) {
						$this->messageManager->addError(
							__("Discount code <strong>$coupon</strong> is invalid. Verify that it's correct and try again.")
						);
					}
				}
			}
		}
	}
}

