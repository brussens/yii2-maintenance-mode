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
     * Username attribute name
     *
     * @var string
     */
    public $usernameAttribute = 'username';

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

        if ($this->enabled) {

            if($this->users) {
                if(is_array($this->users)) {
                    if(isset(Yii::$app->user->identity->username)){
                    $this->_disable = in_array(Yii::$app->user->identity->username, $this->users);
                    }
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
}
