<?php

/**
 * @category  Crankycyclops
 * @package   Crankycyclops_DiscountCodeUrl
 * @author    James Colannino
 * @copyright Copyright (c) 2019 James Colannino
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace Crankycyclops\DiscountCodeUrl\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper {

	/**
	 * The URL parameter we should look for to set the current coupon code.
	 *
	 * @var DEFAULT_URL_PARAMETER URL parameter containing the coupon code
	 */
	public const DEFAULT_URL_PARAMETER = 'discount';

	/**
	 * When a code is supplied via the URL, a cookie is set that allows us to
	 * remember it during a session.
	 *
	 * @var COUPON_COOKIE_NAME Name of cookie that stores discount code
	 */
	public const COUPON_COOKIE_NAME = 'discount_coupon_url_code';

	/**
	 * This is how long a browser session should remember the last coupon code
	 * that was supplied via the URL in seconds. A default value of 0 means
	 * the cookie will last as long as the session (i.e. until the browser tab
	 * or window is closed.)
	 *
	 * @var DEFAULT_COOKIE_LIFETIME Default cookie lifetime in seconds
	 */
	public const DEFAULT_COOKIE_LIFETIME = 0;

	/**
	 * @var ENABLED_CONFIG_PATH Whether or not the module is enabled
	 */
	public const ENABLED_CONFIG_PATH = 'promo/discounturl/enabled';

	/**
	 * @var URL_PARAMETER_CONFIG_PATH GET parameter that should set the coupon code
	 */
	public const URL_PARAMETER_CONFIG_PATH = 'promo/discounturl/url_param';

	/**
	 * @var COOKIE_LIFETIME_CONFIG_PATH How long the cookie should last
	 */
	public const COOKIE_LIFETIME_CONFIG_PATH = 'promo/discounturl/cookie_lifetime';

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	public $scopeConfig;

	/************************************************************************/

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Module\ModuleListInterface $moduleList
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Module\ModuleListInterface $moduleList
	) {
		$this->scopeConfig = $context->getScopeConfig();
		parent::__construct($context);
	}

	/************************************************************************/

	/**
	 * Returns whether or not the module is enabled.
	 *
	 * @param string|int $scope
	 *
	 * @return bool
	 */
	public function isEnabled($scope = 'default'): bool {

		$value = $this->scopeConfig->getValue(
			self::ENABLED_CONFIG_PATH,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
			$scope
		);

		return is_null($value) || '0' == $value ? false : true;
	}

	/************************************************************************/

	/**
	 * Returns whether or not the module is enabled.
	 *
	 * @param string|int $scope
	 *
	 * @return string
	 */
	public function getUrlParameter($scope = 'default'): string {

		$value = $this->scopeConfig->getValue(
			self::URL_PARAMETER_CONFIG_PATH,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
			$scope
		);

		return is_null($value) || '' === $value ? self::DEFAULT_URL_PARAMETER : $value;
	}

	/************************************************************************/

	/**
	 * Returns whether or not the module is enabled.
	 *
	 * @param string|int $scope
	 *
	 * @return int
	 */
	public function getCookieLifetime($scope = 'default'): int {

		$value = $this->scopeConfig->getValue(
			self::COOKIE_LIFETIME_CONFIG_PATH,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
			$scope
		);

		return is_null($value) || '' === $value ? self::DEFAULT_COOKIE_LIFETIME : $value;
	}

	/************************************************************************/

	public function getCookieName(): string {

		return self::COUPON_COOKIE_NAME;
	}
}

