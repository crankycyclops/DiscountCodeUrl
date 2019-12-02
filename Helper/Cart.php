<?php

/**
 * @category  Crankycyclops
 * @package   Crankycyclops_DiscountCodeUrl
 * @author    James Colannino
 * @copyright Copyright (c) 2019 James Colannino
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace Crankycyclops\DiscountCodeUrl\Helper;

class Cart extends \Magento\Framework\App\Helper\AbstractHelper {

	/**
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	private $quoteRepository;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	private $messageManager;

	/************************************************************************/

	/**
	 * Constructor
	 *
	 * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 */
	public function __construct(
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		\Magento\Framework\Message\ManagerInterface $messageManager
	) {
		$this->quoteRepository = $quoteRepository;
		$this->messageManager = $messageManager;
	}

	/************************************************************************/

	public function applyCoupon(\Magento\Quote\Model\Quote $quote, string $coupon): void {

		try {
			$quote->setCouponCode($coupon);
			$this->quoteRepository->save($quote->collectTotals());
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

		if ($quote->getCouponCode() != $coupon) {
			$this->messageManager->addError(
				__("Discount code <strong>$coupon</strong> is invalid. Verify that it's correct and try again.")
			);
		}
	}
}

