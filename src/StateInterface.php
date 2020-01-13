<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) since 2015 Dmitry Brusensky
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance;

/**
 * Interface MaintenanceStateInterface
 * @package brussens\maintenance
 */
interface StateInterface
{
    /**
     * Enable mode method
     */
    public function enable(string $message = null): bool;

    /**
     * Disable mode method
     */
    public function disable();

    /**
     * @return bool
     */
    public function isEnabled();
}