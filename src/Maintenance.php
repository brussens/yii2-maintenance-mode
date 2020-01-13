<?php
/**
 * @link      https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) since 2015 Dmitry Brusensky
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance;

use yii\base\InvalidConfigException;
use yii\web\Application;
use yii\base\BaseObject;
use yii\base\BootstrapInterface;

/**
 * Class Maintenance
 *
 * @package brussens\maintenance
 */
class Maintenance extends BaseObject implements BootstrapInterface
{
    /**
     * Value of "OK" status code.
     */
    const STATUS_CODE_OK = 200;

    /**
     * Route to maintenance action.
     *
     * @var string
     */
    public $route;

    /**
     * @var array
     */
    public $filters;

    /**
     * Default status code to send on maintenance
     * 503 = Service Unavailable
     *
     * @var integer
     */

    public $statusCode = 503;
    /**
     * Retry-After header
     *
     * @var bool|string
     */

    public $retryAfter = false;
    /**
     * @var StateInterface
     */

    protected $state;

    /**
     * Maintenance constructor.
     *
     * @param StateInterface $state
     * @param array          $config
     */
    public function __construct(StateInterface $state, array $config = [])
    {
        $this->state = $state;
        parent::__construct($config);
    }

    /**
     * @param Application $app
     *
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        $response = $app->response;
        if ($app->request->isAjax) {
            $response->statusCode = self::STATUS_CODE_OK;
        } else {
            $response->statusCode = $this->statusCode;
            if ($this->retryAfter) {
                $response->headers->set('Retry-After', $this->retryAfter);
            }
        }

        if ($this->state->isEnabled() && ! $this->filtersExcepted()) {
            $app->catchAll = [$this->route];
        } else {
            $response->statusCode = self::STATUS_CODE_OK;
        }
    }

    /**
     * @return bool
     * @throws InvalidConfigException
     */
    protected function filtersExcepted()
    {
        if (! is_array($this->filters) || empty($this->filters)) {
            return false;
        }

        foreach ($this->filters as $config) {
            $filter = \Yii::createObject($config);
            if (! ($filter instanceof Filter)) {
                throw new InvalidConfigException(
                    'Class "' . get_class($filter) . '" must instance of "' . Filter::className() . '".'
                );
            }

            if ($filter->isAllowed()) {
                return true;
            }
        }

        return false;
    }
}