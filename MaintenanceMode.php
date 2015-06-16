<?php
/**
 * Maintenance mode component for Yii framework 2.x.x version.
 *
 * Class MaintenanceMode
 *
 * @package brussens\maintenance
 * @version 0.2.0
 * @author BrusSENS (Brusenskiy Dmitry) <brussens@nativeweb.ru>
 * @link https://github.com/brussens/yii2-maintenance-mode
 */

namespace brussens\maintenance;

use yii\helpers\FileHelper;
use yii\base\InvalidConfigException;
use yii\base\Component;
use Yii;

class MaintenanceMode extends Component
{
    /**
     *
     * Mode status
     *
     * @var bool
     */
    public $enabled = true;

    /**
     * Route to action
     *
     * @var string
     */
    public $route = 'maintenance/index';

    /**
     * Show message
     *
     * @var null
     */
    public $message;

    /**
     * Allowed user names
     *
     * @var array
     */
    public $users;

    /**
     * Allowed roles
     * @var array
     */
    public $roles;

    /**
     * Allowed IP addresses
     *
     * @var array
     */
    public $ips;

    /**
     * Allowed urls
     *
     * @var array
     */
    public $urls;

    /**
     * Path to layout file
     *
     * @var string
     */
    public $layoutPath = '@vendor/brussens/yii2-maintenance-mode/views/layouts/main';

    /**
     * Path to view file
     *
     * @var string
     */
    public $viewPath = '@vendor/brussens/yii2-maintenance-mode/views/maintenance/index';

    /**
     * Path to command file
     *
     * @var string
     */
    public $commandPath = '@runtime/maintenance';

    /**
     * Username attribute name
     *
     * @var string
     */
    public $usernameAttribute = 'username';

    /**
     * Default status code to send on maintenance
     *
     * 503 = Service Unavailable
     * @var int
     */
    public $statusCode = 503;

    /**
     * Disable items.
     * @var
     */
    protected $_disable;

    /**
     * init method
     */
    public function init()
    {
        if('yii\console\Application'===get_class(Yii::$app)) {
            Yii::$app->controllerMap['maintenance'] = 'brussens\maintenance\commands\MaintenanceController';
        } else {
            if($this->isEnabled())
                $this->filtering();
        }
    }

    /**
     * Set prolonged status enabled.
     *
     * It is equal $this->enable=true, but without change config.
     * @see brussens\maintenance\commands\MaintenanceController.
     *
     * @return int
     * @throws \yii\base\Exception
     */
    public function enableProlonged()
    {
        $path = $this->getProlongedPath();
        $dir = dirname($path);
        if (!is_dir($dir))
            FileHelper::createDirectory($dir);

        return file_put_contents($path, '');
    }

    /**
     * Set prolonged status disabled.
     *
     *  It is equal $this->enable=false, but without change config.
     * @see brussens\maintenance\commands\MaintenanceController.
     *
     * @return bool
     */
    public function disableProlonged()
    {
        $path = $this->getProlongedPath();
        if(file_exists($path))
            return unlink($path);

        return true;
    }

    /**
     * Check component status.
     *
     * @return bool
     */
    protected function isEnabled()
    {
        return $this->enabled || file_exists($this->getProlongedPath());
    }

    /**
     * Get path for command
     *
     * @return string
     */
    protected function getProlongedPath()
    {
        $path = Yii::getAlias($this->commandPath).'/.enable';
        return FileHelper::normalizePath($path);
    }

    /**
     * Filtering process request from web
     *
     * @throws InvalidConfigException
     */
    protected function filtering()
    {
        if($this->statusCode) {
            if(is_integer($this->statusCode)) {
                Yii::$app->getResponse()->setStatusCode($this->statusCode);
            }
            else {
                throw new InvalidConfigException('Parameter "statusCode" should be an integer.');
            }
        }

        if($this->users) {
            if(is_array($this->users)) {
                $this->_disable = in_array(Yii::$app->user->identity->{$this->usernameAttribute}, $this->users);
            }
            else {
                throw new InvalidConfigException('Parameter "users" should be an array.');
            }
        }

        if($this->roles) {
            if(is_array($this->roles)) {
                foreach ($this->roles as $role) {
                    $this->_disable = $this->_disable || Yii::$app->user->can($role);
                }
            }
            else {
                throw new InvalidConfigException('Parameter "roles" should be an array.');
            }
        }

        if($this->urls) {
            if(is_array($this->urls)) {
                $this->_disable = $this->_disable || in_array(Yii::$app->request->getPathInfo(), $this->urls);
            }
            else {
                throw new InvalidConfigException('Parameter "urls" should be an array.');
            }
        }

        if($this->ips) {
            if(is_array($this->ips)) {
                $this->_disable = $this->_disable || in_array(Yii::$app->request->userIP, $this->ips);
            }
            else {
                throw new InvalidConfigException('Parameter "ips" should be an array.');
            }
        }

        if (!$this->_disable) {
            if ($this->route === 'maintenance/index') {
                Yii::$app->controllerMap['maintenance'] = 'brussens\maintenance\controllers\MaintenanceController';
            }

            Yii::$app->catchAll = [$this->route];
        }
    }
}