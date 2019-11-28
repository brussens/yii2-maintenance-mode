<?php
/**
 * @link      https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) since 2015 Dmitry Brusensky
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance\states;

use Yii;
use brussens\maintenance\StateInterface;
use yii\base\BaseObject;

/**
 * Class FileState
 *
 * @package brussens\maintenance\states
 */
class FileState extends BaseObject implements StateInterface
{
    /**
     * @var string the filename that will determine if the maintenance mode is enabled
     */
    public $fileName = 'YII_MAINTENANCE_MODE_ENABLED';
    /**
     * @var string the directory in that the file stated in $fileName above is residing
     */
    public $directory = '@runtime';

    /**
     * Turn on mode.
     *
     * @since 0.2.5
     */
    public function enable()
    {
        file_put_contents($this->getStatusFilePath(), ' ');
    }

    /**
     * Turn off mode.
     *
     * @since 0.2.5
     */
    public function disable()
    {
        $path = $this->getStatusFilePath();

        if (file_exists($path)) {
            if (! unlink($path)) {
                throw new \Exception(
                    "Attention: the maintenance mode could not be disabled because $path could not be removed."
                );
            };
        }
    }

    /**
     * @return bool will return true if the file exists
     */
    public function isEnabled()
    {
        return file_exists($this->getStatusFilePath());
    }

    /**
     * Return status file path.
     *
     * @return bool|string
     * @since 0.2.5
     */
    protected function getStatusFilePath()
    {
        return Yii::getAlias($this->directory . '/' . $this->fileName);
    }
}
