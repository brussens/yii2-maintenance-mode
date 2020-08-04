<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) since 2015 Dmitry Brusensky
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance\filters;

use brussens\maintenance\Filter;
use yii\web\Request;

/**
 * Class RouteFilter
 * @package brussens\maintenance\filters
 * @since 1.2.0
 */
class RouteFilter extends Filter
{
    /**
     * @var array
     */
    public $routes;
    /**
     * @var string
     */
    protected $currentRoute;

    /**
     * RouteFilter constructor.
     * @param Request $request
     * @param array $config
     */
    public function __construct(Request $request, array $config = [])
    {
        $this->currentRoute = $request->resolve()[0];
        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (is_string($this->routes)) {
            $this->routes = [$this->routes];
        }
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        if (is_array($this->routes) && !empty($this->routes)) {
            return (bool) in_array($this->currentRoute, $this->routes);
        }
        return false;
    }
}