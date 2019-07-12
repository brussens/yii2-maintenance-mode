<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) since 2015 Dmitry Brusensky
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance\states;

use brussens\maintenance\StateInterface;
use yii\base\BaseObject;

/**
 * Class SimpleState
 * @package brussens\maintenance\states
 */
class SimpleState extends BaseObject implements StateInterface
{
    /**
     * @var bool
     */
    public $enabled = false;

    /**
     * @inheritdoc
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}