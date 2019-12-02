<?php

/**
 * @category  Crankycyclops
 * @package   Crankycyclops_DiscountCodeUrl
 * @author    James Colannino
 * @copyright Copyright (c) 2019 James Colannino
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace Crankycyclops\DiscountCodeUrl\Plugin\Framework\App;

class FrontControllerInterface {

	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	private $request;

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

	/**
	 * @var \Magento\SalesRule\Model\Coupon
	 */
	private $couponModel;

	/**
	 * @var \Magento\SalesRule\Model\Rule
	 */
	private $ruleModel;

	/**
	 * @var \Magento\Framework\Registry $registry
	 */
	private $registry;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	private $checkoutSession;

	/************************************************************************/

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Config $config
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper
	 * @param \Crankycyclops\DiscountCodeUrl\Helper\Cart $cartHelper
	 * @param \Magento\SalesRule\Model\Coupon $couponModel
	 * @param \Magento\SalesRule\Model\Rule $ruleModel
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Crankycyclops\DiscountCodeUrl\Helper\Config $config,
		\Crankycyclops\DiscountCodeUrl\Helper\Cookie $cookieHelper,
		\Crankycyclops\DiscountCodeUrl\Helper\Cart $cartHelper,
		\Magento\SalesRule\Model\Coupon $couponModel,
		\Magento\SalesRule\Model\Rule $ruleModel,
		\Magento\Framework\Registry $registry,
		\Magento\Checkout\Model\Session $checkoutSession
	) {
		$this->request = $request;
		$this->config = $config;
		$this->cookieHelper = $cookieHelper;
		$this->cartHelper = $cartHelper;
		$this->couponModel = $couponModel;
		$this->ruleModel = $ruleModel;
		$this->registry = $registry;
		$this->checkoutSession = $checkoutSession;
	}

	/************************************************************************/

	/**
	 * If coupon code is provided by the URL, remember it for the duration of
	 * the session.
	 *
	 * @param \Magento\Framework\App\FrontControllerInterface $subject (not used)
	 *
	 * @return void
	 */
	public function beforeDispatch(\Magento\Framework\App\FrontControllerInterface $subject): void {

		if ($this->config->isEnabled()) {

			$queryParameter = $this->config->getUrlParameter();

			// Discount code passed through the URL via query string
			$coupon = $this->request->getParam($queryParameter);

			// If the coupon code didn't come in via a query string, check to
			// see if it was tacked onto the end of the URL. This will only
			// function if your implementation of RequestInterface has
			// implemented the setPathInfo method (in most cases, this
			// should be true, but the interface doesn't require it to be
			// implemented, so better safe than sorry!)
			if (
				!$coupon &&
				$this->config->isUrlPathEnabled() &&
				method_exists($this->request, 'setPathInfo')
			) {

				$requestPath = $this->request->getPathInfo();

				// If a coupon code was included in the URL, fix the URL
				// after extracting it so that we can continue to route normally
				if (preg_match("#/$queryParameter/([^/]+?)/*$#", $requestPath, $matches)) {

					$coupon = $matches[1];
					$realPath = str_replace($matches[0], '', $requestPath);

					if (!$realPath) {
						$realPath = '/';
					}

					$this->request->setPathInfo($realPath);
				}
			}

			if ($coupon) {

				$invalidMessage = "Discount code <strong>$coupon</strong> is invalid";
				$expiredMessage = "Unfortunately, the <strong>$coupon</strong> discount code is expired";
				$consumedMessage = "Unfortunately, the <strong>$coupon</strong> discount code has been fully consumed";

				$this->couponModel->loadByCode($coupon);

				if($this->couponModel->getId()) {

					$this->ruleModel->load($this->couponModel->getRuleId());

					if ($this->ruleModel->getId()) {

						$today = strtotime(date("Y-m-d"));
						$startDay = $this->ruleModel->getFromDate();
						$expirationDay = $this->ruleModel->getToDate();

						$numUses = $this->couponModel->getTimesUsed();
						$maxUses = $this->couponModel->getUsageLimit();

						$usesPerCustomer = $this->couponModel->getUsagePerCustomer();

						// Discount code is expired
						if ($expirationDay && strtotime($expirationDay) < $today) {
							$this->registry->register('crankycyclops_discounturl_message', [
								'message' => __($expiredMessage),
								'error' => true
							]);
						}

						// Discount hasn't started yet
						else if ($startDay && strtotime($startDay) > $today) {
							$this->registry->register('crankycyclops_discounturl_message', [
								'message' => __($invalidMessage),
								'error' => true
							]);
						}

						// Coupon has already been fully consumed
						else if ($maxUses && $numUses >= $maxUses) {
							$this->registry->register('crankycyclops_discounturl_message', [
								'message' => __($consumedMessage),
								'error' => true
							]);
						}

						else {

							$successMessage = "Discount code <strong>$coupon</strong> will be applied to your order during checkout";

							if ($usesPerCustomer && $usesPerCustomer > 0) {

								if ($usesPerCustomer > 1) {
									$successMessage .= " unless you've already fully consumed it (code is only valid for up to $usesPerCustomer orders";
								} else {
									$successMessage .= " unless you've already used it (code is only valid for one order";
								}

								$successMessage .= " per customer)";
							}

							// As documented in
							// \Magento\Framework\App\Http::launch()
							// around line 150, I can't actually set a
							// cookie until after the request is
							//  dispatched and the result is rendered.
							// Thus, I save this coupon code in the
							// registry and actually set the cookie in an
							// observer that listens for
							// controller_front_send_response_before. You
							// don't know how many hours I pulled my hair
							// out figuring this out...
							$this->registry->register('crankycyclops_discounturl_coupon', $coupon);
							$this->registry->register('crankycyclops_discounturl_message', [
								'message' => __($successMessage),
								'error' => false
							]);
						}
					}

					else {
						$this->registry->register('crankycyclops_discounturl_message', [
							'message' => __($invalidMessage),
							'error' => true
						]);
					}
				}

				else {
					$this->registry->register('crankycyclops_discounturl_message', [
						'message' => __($invalidMessage),
						'error' => true
					]);
				}
			}
		}
	}

	/************************************************************************/

	/**
	 * If a quote already exists, we need to apply the discount code to it
	 * automatically (if possible) and before the response is rendered. This
	 * covers us in the case that a user applies a discount code to the URL
	 * after having a cart that's already full (which means the save cart
	 * observer won't execute and therefore won't update the quote's price.) I
	 * can't do this in beforeDispatch, because based on my own testing, it
	 * seems that the session classes don't get populated until after
	 * FrontController::dispatch() finishes.
	 *
	 * @param \Magento\Framework\App\FrontControllerInterface $subject (not used)
	 * @param ResponseInterface|ResultInterface Return value of FrontController::dispatch()
	 *
	 * @return ResponseInterface|ResultInterface
	 */
	public function afterDispatch(\Magento\Framework\App\FrontControllerInterface $subject, $result) {

		if ($this->config->isEnabled()) {

			// If a quote already exists, apply the
			// discount automatically (if possible)
			$coupon = $this->registry->registry('crankycyclops_discounturl_coupon');

			if ($coupon && $this->checkoutSession->hasQuote()) {
				$this->cartHelper->applyCoupon(
					$this->checkoutSession->getQuote(),
					$coupon
				);
			}
		}

		return $result;
	}
}

