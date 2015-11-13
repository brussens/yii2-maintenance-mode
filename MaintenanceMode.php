<?php
/**
 * Maintenance mode component for Yii framework 2.x.x version.
 *
 * Class MaintenanceMode
 *
 * @package brussens\maintenance
 * @version 0.2.0
 * @author BrusSENS (Brusenskiy Dmitry) <brussens@nativeweb.ru>
 * @author co11ter (Poltoratsky Alexander)
 * @link https://github.com/brussens/yii2-maintenance-mode
 */

namespace brussens\maintenance;

use yii\base\InvalidConfigException;
use yii\base\Component;
use yii\helpers\FileHelper;
use Yii;

class MaintenanceMode extends Component
{
    const STATUS_CODE_OK = 200;
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
     * Allowed user name(s)
     * @var
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
    protected $disable;

    /**
     * init method
     */
    public function init()
    {
        if(Yii::$app instanceof yii\console\Application) {
            Yii::$app->controllerMap['maintenance'] = 'brussens\maintenance\commands\MaintenanceController';
        } else {
            if($this->getIsEnabled()) {
                $this->filtering();
            }
        }
    }

    /**
     * @return bool
     */
    protected function getIsEnabled()
    {
        return $this->enabled || file_exists($this->getProlongedPath());
    }

    /**
     * @return mixed
     */
    protected function getProlongedPath()
    {
        $path = Yii::getAlias($this->commandPath).'/.enable';
        return FileHelper::normalizePath($path);
    }

    /**
     * @return bool
     */
    public function disableProlonged()
    {
        $path = $this->getProlongedPath();
        if(file_exists($path)) {
            return unlink($path);
        }
        return true;
    }

    /**
     * @return int
     */
    public function enableProlonged()
    {
        $path = $this->getProlongedPath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir);
        }
        return file_put_contents($path, '');
    }

    /**
     * @throws InvalidConfigException
     */
    protected function filtering()
    {
        if($this->statusCode) {
            if(is_integer($this->statusCode)) {
                if(Yii::$app->getRequest()->getIsAjax()) {
                    Yii::$app->getResponse()->setStatusCode(self::STATUS_CODE_OK);
                }
                else {
                    Yii::$app->getResponse()->setStatusCode($this->statusCode);
                }
            }
            else {
                throw new InvalidConfigException('Parameter "statusCode" should be an integer.');
            }
        }

        /**
         * Check users
         */
        if($this->users) {
            if(is_array($this->users)) {
                $this->disable = Yii::$app->user->identity ? in_array(Yii::$app->user->identity->{$this->usernameAttribute}, $this->users): false;
            }
            elseif(is_string($this->users)) {
                $this->disable = Yii::$app->user->identity->{$this->usernameAttribute} === $this->users;
            }
            else {
                throw new InvalidConfigException('Parameter "users" should be an array or string.');
            }
        }

        /**
         * Check roles
         */
        if($this->roles) {
            if(is_array($this->roles)) {
                foreach ($this->roles as $role) {
                    $this->disable = $this->disable || Yii::$app->user->can($role);
                }
            }
            else {
                throw new InvalidConfigException('Parameter "roles" should be an array.');
            }
        }

        /**
         * Check URL's
         */
        if($this->urls) {
            if(is_array($this->urls)) {
                $this->disable = $this->disable || in_array(Yii::$app->request->getPathInfo(), $this->urls);
            }
            else {
                throw new InvalidConfigException('Parameter "urls" should be an array.');
            }
        }

        /**
         * Checked IP's
         */
        if($this->ips) {
            if(is_array($this->ips)) {
                $this->disable = $this->disable || in_array(Yii::$app->request->userIP, $this->ips);
            }
            else {
                throw new InvalidConfigException('Parameter "ips" should be an array.');
            }
        }

        if (!$this->disable) {
            if ($this->route === 'maintenance/index') {
                Yii::$app->controllerMap['maintenance'] = 'brussens\maintenance\controllers\MaintenanceController';
            }

            Yii::$app->catchAll = [$this->route];

        }
        else {
            Yii::$app->getResponse()->setStatusCode(self::STATUS_CODE_OK);
        }
    }
}
