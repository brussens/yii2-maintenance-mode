<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) 2017 Brusensky Dmitry
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Component;
use yii\helpers\FileHelper;

/**
 * Maintenance mode component for Yii framework 2.x.x version.
 *
 * @see \yii\base\component
 * @package brussens\maintenance
 * @author Brusensky Dmitry <brussens@nativeweb.ru>
 * @since 0.2.0
 */
class MaintenanceMode extends Component
{
    /**
     * Value of "OK" status code.
     */
    const STATUS_CODE_OK = 200;

    /**
     * Mode status.
     * @since 0.2.0
     * @var bool
     */
    public $enabled = false;
    /**
     * Route to action.
     * @since 0.2.0
     * @var string
     */
    public $route = 'maintenance/index';
    /**
     * Show title.
     * @since 0.2.4
     * @var string
     */
    public $title = 'We’ll be back soon!';
    /**
     * Show message.
     * @since 0.2.0
     * @var string
     */
    public $message = 'Sorry, perform technical works.';
    /**
     * Allowed user name(s).
     * @since 0.2.0
     * @var array|string
     */
    public $users;
    /**
     * Allowed roles.
     * @since 0.2.0
     * @var array
     */
    public $roles;
    /**
     * Allowed IP addresses.
     * @since 0.2.0
     * @var string|array
     */
    public $ips;
    /**
     * Allowed urls.
     * @since 0.2.0
     * @var array
     */
    public $urls;
    /**
     * Path to layout file.
     * @since 0.2.0
     * @var string
     */
    public $layoutPath = '@vendor/brussens/yii2-maintenance-mode/views/layouts/main';
    /**
     * Path to view file
     * @since 0.2.0
     * @var string
     */
    public $viewPath = '@vendor/brussens/yii2-maintenance-mode/views/maintenance/index';
    /**
     * Path to command file
     * @since 0.2.2
     * @var string
     */
    public $commandPath = '@vendor/../maintenance';
    /**
     * Username attribute name
     * @since 0.2.0
     * @var string
     */
    public $usernameAttribute = 'username';
    /**
     * Default status code to send on maintenance
     * 503 = Service Unavailable
     * @since 0.2.1
     * @var integer
     */
    public $statusCode = 503;
    /**
     * Retry-After header
     * @since 0.2.4
     * @var bool|string
     */
    public $retryAfter = false;
    /**
     * Console controller class name.
     * @since 0.3.0
     * @var string
     */
    public $consoleController = 'brussens\maintenance\commands\MaintenanceController';

    /**
     * Disable items.
     * @since 0.2.2
     * @var boolean
     */
    protected $disable;

    /**
     * Initial component method.
     * @since 0.2.0
     */
    public function init()
    {
        Yii::setAlias('@maintenance', $this->commandPath);
        if (!file_exists(Yii::getAlias('@maintenance'))) {
            FileHelper::createDirectory(Yii::getAlias('@maintenance'));
        }
        if (Yii::$app instanceof \yii\console\Application) {
            Yii::$app->controllerMap['maintenance'] = $this->consoleController;
        } else {
            if ($this->getIsEnabled()) {
                $this->filtering();
            }
        }
    }

    /**
     * Checks if mode is on.
     * @since 0.2.2
     * @param bool $onlyConsole
     * @return bool
     */
    public function getIsEnabled($onlyConsole = false)
    {
        $exists = file_exists($this->getStatusFilePath());
        return $onlyConsole ? $exists : $this->enabled || $exists;
    }

    /**
     * Return status file path.
     * @since 0.2.5
     * @return bool|string
     */
    protected function getStatusFilePath()
    {
        return Yii::getAlias('@maintenance/.enable');
    }

    /**
     * Turn off mode.
     * @since 0.2.5
     * @return bool
     */
    public function disable()
    {
        $path = $this->getStatusFilePath();
        if ($path && file_exists($path)) {
            return (bool) unlink($path);
        }
        return false;
    }

    /**
     * Turn on mode.
     * @since 0.2.5
     * @return bool
     */
    public function enable()
    {
        $path = $this->getStatusFilePath();
        return (bool) file_put_contents($path, ' ');
    }

    /**
     * Check IP (mask supported).
     * @since 0.2.6
     * @param $filter
     * @return bool
     */
    protected function checkIp($filter)
    {
        $ip = Yii::$app->getRequest()->getUserIP();
        return $filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos));
    }

    /**
     * Filtering by configuration.
     * @since 0.2.2
     * @throws InvalidConfigException
     */
    protected function filtering()
    {
        $app = Yii::$app;
        if ($this->statusCode) {
            if (is_integer($this->statusCode)) {
                if ($app->getRequest()->getIsAjax()) {
                    $app->getResponse()->setStatusCode(self::STATUS_CODE_OK);
                } else {
                    $app->getResponse()->setStatusCode($this->statusCode);
                    if ($this->retryAfter){
                        $app->getResponse()->getHeaders()->set('Retry-After', $this->retryAfter);
                    }
                }
            } else {
                throw new InvalidConfigException('Parameter "statusCode" should be an integer.');
            }
        }
        // Check users
        if ($this->users) {
            if (is_array($this->users)) {
                $this->disable = $app->getUser()->getIdentity()
                    ? in_array($app->getUser()->getIdentity()->{$this->usernameAttribute}, $this->users)
                    : false;
            } elseif (is_string($this->users)) {
                $this->disable = $app->getUser()->getIdentity()->{$this->usernameAttribute} === $this->users;
            } else {
                throw new InvalidConfigException('Parameter "users" should be an array or string.');
            }
        }
        // Check roles
        if ($this->roles) {
            if (is_array($this->roles)) {
                foreach ($this->roles as $role) {
                    $this->disable = $this->disable || $app->getUser()->can($role);
                }
            } else {
                throw new InvalidConfigException('Parameter "roles" should be an array.');
            }
        }
        // Check URL's
        if ($this->urls) {
            if (is_array($this->urls)) {
                $this->disable = $this->disable || in_array($app->getRequest()->getPathInfo(), $this->urls);
            } else {
                throw new InvalidConfigException('Parameter "urls" should be an array.');
            }
        }
        // Check IP's
        if ($this->ips) {
            if (is_array($this->ips)) {
                foreach ($this->ips as $filter) {
                    $this->disable = $this->disable || $this->checkIp($filter);
                }
            } elseif (is_string($this->ips)){
                $this->disable = $this->disable || $this->checkIp($this->ips);
            } else {
                throw new InvalidConfigException('Parameter "ips" should be an array.');
            }
        }
        if (!$this->disable) {
            if ($this->route === 'maintenance/index') {
                $app->controllerMap['maintenance'] = 'brussens\maintenance\controllers\MaintenanceController';
            }
            $app->catchAll = [$this->route];
        } else {
            $app->getResponse()->setStatusCode(self::STATUS_CODE_OK);
        }
    }
}