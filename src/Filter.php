<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) since 2015 Dmitry Brusensky
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance;

use yii\base\BaseObject;

/**'
 * Class Checker
 * @package brussens\maintenance
 */
abstract class Filter extends BaseObject
{
    /**
     * @return bool
     */
    abstract public function isAllowed();
}