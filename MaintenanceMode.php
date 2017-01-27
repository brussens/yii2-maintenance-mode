<?php
/**
 * Maintenance mode component for Yii framework 2.x.x version.
 * Class MaintenanceMode
 * @package brussens\maintenance
 * @version 0.2.2
 * @author BrusSENS (Brusenskiy Dmitry) <brussens@nativeweb.ru>
 * @author co11ter (Poltoratsky Alexander)
 * @author ibra1994
 * @link https://github.com/brussens/yii2-maintenance-mode
 */
namespace brussens\maintenance;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Component;
use yii\helpers\FileHelper;
class MaintenanceMode extends Component
{
    const STATUS_CODE_OK = 200;
    /**
     * Mode status
     * @var bool
     */
    public $enabled = true;
    /**
     * Route to action
     * @var string
     */
    public $route = 'maintenance/index';
    /**
     * Show title
     * @var null
     */
    public $title = 'We&rsquo;ll be back soon!';
    /**
     * Show message
     * @var null
     */
    public $message = 'Sorry, perform technical works.';
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
     * @var array
     */
    public $ips;
    /**
     * Allowed urls
     * @var array
     */
    public $urls;
    /**
     * Path to layout file
     * @var string
     */
    public $layoutPath = '@vendor/brussens/yii2-maintenance-mode/views/layouts/main';
    /**
     * Path to view file
     * @var string
     */
    public $viewPath = '@vendor/brussens/yii2-maintenance-mode/views/maintenance/index';
    /**
     * Path to command file
     * @var string
     */
    public $commandPath = '@vendor/../maintenance';
    /**
     * Username attribute name
     * @var string
     */
    public $usernameAttribute = 'username';
    /**
     * Default status code to send on maintenance
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
     * Retry-After header
     * @var bool|string
     */
    public $retryAfter = false;
    /**
     * init method
     */
    public function init()
    {
        Yii::setAlias('@maintenance', $this->commandPath);
        if(!file_exists(Yii::getAlias('@maintenance'))) {
            FileHelper::createDirectory(Yii::getAlias('@maintenance'));
        }
        if(Yii::$app instanceof \yii\console\Application) {
            Yii::$app->controllerMap['maintenance'] = 'brussens\maintenance\commands\MaintenanceController';
        } else {
            if($this->getIsEnabled()) {
                $this->filtering();
            }
        }
    }
    /**
     * @param bool $onlyConsole
     * @return bool
     */
    public function getIsEnabled($onlyConsole = false)
    {
        $exists = file_exists($this->getStatusFilePath());
        return $onlyConsole ? $exists : $this->enabled || $exists;
    }
    /**
     * @return bool|string
     */
    protected function getStatusFilePath()
    {
        return Yii::getAlias('@maintenance/.enable');
    }
    /**
     * @return bool
     */
    public function disable()
    {
        $path = $this->getStatusFilePath();
        if($path && file_exists($path)) {
            return (bool) unlink($path);
        }
        return false;
    }
    /**
     * @return bool
     */
    public function enable()
    {
        $path = $this->getStatusFilePath();
        return (bool) file_put_contents($path, ' ');
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
                    if($this->retryAfter){
                        Yii::$app->response->headers->set('Retry-After', $this->retryAfter);
                    }
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
