<?php

/**
 * @category  Crankycyclops
 * @package   Crankycyclops_DiscountCodeUrl
 * @author    James Colannino
 * @copyright Copyright (c) 2019 James Colannino
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace Crankycyclops\DiscountCodeUrl\Observer\Discountcodeurl;

class ControllerFrontSendResponseBefore implements \Magento\Framework\Event\ObserverInterface {

	/**
	 * @var \Crankycyclops\DiscountCodeUrl\Helper\Config
	 */
	private $config;

	/**
	 * @var \Crankycyclops\DiscountCodeUrl\Helper\Cookie
	 */
	private $cookieHelper;

	/**
	 * @var \Magento\Framework\Registry $registry
	 */
	private $registry;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	private $messageManager;

	/************************************************************************/

	/**
	 * Constructor
	 *
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Config $config
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 */
	public function __construct(
		\Crankycyclops\DiscountCodeUrl\Helper\Config $config,
		\Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Message\ManagerInterface $messageManager
	) {
		$this->config = $config;
		$this->cookieHelper = $cookieHelper;
		$this->registry = $registry;
		$this->messageManager = $messageManager;
	}

	/************************************************************************/

	/**
	 * If a valid coupon code was set in the URL, we save a cookie with that
	 * value here so we can remember it when it's time to checkout. Originally,
	 * I was trying to set this value in
	 * Plugin\FrontControllerInterface::beforeDispatch(), but after having weird
	 * issues and tracing through the code in Magento core, I discovered in
	 * \Magento\Framework\App\Http::launch() that I should listen for the
	 * controller_front_send_response_before event and then set cookies there,
	 * because the cookie headers can't be set until after the request has been
	 * dispatched and the response has been fully rendered. I spent many hours
	 * pulling my hair out figuring this out...
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 *
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $observer): void {

		if ($this->config->isEnabled()) {

			$coupon = $this->registry->registry('crankycyclops_discounturl_coupon');
			$message = $this->registry->registry('crankycyclops_discounturl_message');

			if ($coupon) {
				$this->cookieHelper->setCookie($coupon);
			}

			if ($message) {
				if ($message['error']) {
					$this->messageManager->addError($message['message']);
				} else {
					$this->messageManager->addSuccess($message['message']);
				}
			}
		}
	}
}

