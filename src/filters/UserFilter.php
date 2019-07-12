<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) since 2015 Dmitry Brusensky
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance\filters;

use brussens\maintenance\Filter;
use yii\web\IdentityInterface;
use yii\web\User;

/**
 * Class UserChecker
 * @package brussens\maintenance\filters
 */
class UserFilter extends Filter
{
    /**
     * @var string
     */
    public $checkedAttribute;
    /**
     * @var array
     */
    public $users;
    /**
     * @var User|null
     */
    protected $identity;

    /**
     * UserChecker constructor.
     * @param User $user
     * @param array $config
     */
    public function __construct(User $user, array $config = [])
    {
        $this->identity = $user->identity;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_string($this->users)) {
            $this->users = [$this->users];
        }
        parent::init();
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        if (($this->identity instanceof IdentityInterface) && is_array($this->users) && !empty($this->users)) {
            return (bool) in_array($this->identity->{$this->checkedAttribute}, $this->users);
        }
        return false;
    }
}