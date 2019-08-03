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
 * Class URIChecker
 * @package brussens\maintenance\filters
 */
class URIFilter extends Filter
{
    /**
     * @var array
     */
    public $uri;
    /**
     * @var Request
     */
    protected $request;

    /**
     * URIChecker constructor.
     * @param Request $request
     * @param array $config
     */
    public function __construct(Request $request, array $config = [])
    {
        $this->request = $request;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_string($this->uri)) {
            $this->uri = [$this->uri];
        }
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function isAllowed()
    {
        if (is_array($this->uri) && !empty($this->uri)) {
           return (bool) in_array($this->request->getPathInfo(), $this->uri);
        }
        return false;
    }
}
