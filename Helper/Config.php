<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Magenerds
 * @package    Magenerds_OptimizeMedia
 * @author     Mahmood Dhia <m.dhia@techdivision.com>
 * @copyright  Copyright (c) 2019 TechDivision GmbH (https://www.techdivision.com)
 * @link       https://www.techdivision.com/
 */

namespace Magenerds\OptimizeMedia\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * Configuration path for "general/enabled"
     *
     * @var string
     */
    const MODULE_ENABLED = 'optimizemedia/general/enabled';

    /**
     * Configuration path for "image_optimization/check_mode"
     *
     * @var string
     */
    const CHECK_MODE = 'optimizemedia/image_optimization/check_mode';

    /**
     * Configuration path for "image_optimization/optimize_wysiwyg_images"
     *
     * @var string
     */
    const OPTIMIZE_WYSIWYG_IMAGES = 'optimizemedia/image_optimization/optimize_wysiwyg_images';

    /**
     * Configuration path for "logging/enabled"
     *
     * @var string
     */
    const LOGGING_ENABLED = 'optimizemedia/logging/enabled';

    /**
     * return true if module is enable
     *
     * @param string $scope
     * @return bool
     */
    public function isModuleEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return boolval($this->scopeConfig->getValue(self::MODULE_ENABLED, $scope));
    }

    /**
     * return check mode
     *
     * @param string $scope
     * @return int
     */
    public function getCheckMode($scope = ScopeInterface::SCOPE_STORE)
    {
        return intval($this->scopeConfig->getValue(self::CHECK_MODE, $scope));
    }

    /**
     * return true if optimize wysiwyg images is enable
     *
     * @param string $scope
     * @return bool
     */
    public function isOptimizeWysiwygImagesEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return boolval($this->scopeConfig->getValue(self::OPTIMIZE_WYSIWYG_IMAGES, $scope));
    }

    /**
     * return true if logging is enable
     *
     * @param string $scope
     * @return bool
     */
    public function isLoggingEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return boolval($this->scopeConfig->getValue(self::LOGGING_ENABLED, $scope));
    }

    /**
     * Gets a value from configuration
     *
     * @param string $path
     * @param string [$scope]
     * @return mixed
     */
    protected function getValue($path, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scope);
    }
}
