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
 * IP addresses checker with mask supported.
 * @package brussens\maintenance\filters
 */
class IpFilter extends Filter
{
    /**
     * @var array|string
     */
    public $ips;
    /**
     * @var Request
     */
    protected $request;

    /**
     * IpFilter constructor.
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
        if (is_string($this->ips)) {
            $this->ips = [$this->ips];
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function isAllowed()
    {
        if (is_array($this->ips) && !empty($this->ips)) {
            $ip = $this->request->userIP;
            foreach ($this->ips as $filter) {
                if ($this->checkIp($filter, $ip)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check IP (mask supported).
     * @since 1.0.0
     * @param string $filter
     * @param string $ip
     * @return bool
     */
    protected function checkIp($filter, $ip)
    {
        return $filter === '*' || $filter === $ip
            || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos));
    }
}