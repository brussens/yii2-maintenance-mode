# Yii2 Maintenance mode component
[![Latest Stable Version](https://poser.pugx.org/brussens/yii2-maintenance-mode/v/stable)](https://packagist.org/packages/brussens/yii2-maintenance-mode)
[![Total Downloads](https://poser.pugx.org/brussens/yii2-maintenance-mode/downloads)](https://packagist.org/packages/brussens/yii2-maintenance-mode)
[![License](https://poser.pugx.org/brussens/yii2-maintenance-mode/license)](https://packagist.org/packages/brussens/yii2-maintenance-mode)
## Install
Either run
```
php composer.phar require --prefer-dist brussens/yii2-maintenance-mode "*"
```

or add

```
"brussens/yii2-maintenance-mode": "*"
```

to the require section of your `composer.json` file.

Add to your config file:
```php
'bootstrap' => [
    'brussens\maintenance\Maintenance'
],
...
'container' => [
    'singletons' => [
        'brussens\maintenance\Maintenance' => [
            'class' => 'brussens\maintenance\Maintenance',
            // Route to action
            'route' => 'maintenance/index',
            // Filters. Read Filters for more info.
            'filters' = [
                [
                    'class' => 'brussens\maintenance\filters\URIFilter',
                    'uri' => [
                        'debug/default/toolbar',
                        'debug/default/view',
                        'site/login'
                    ]
                ]
            ],
            // HTTP Status Code
            'statusCode' => 503,
            //Retry-After header
            'retryAfter' => 120 //or Wed, 21 Oct 2015 07:28:00 GMT for example
        ],
        'brussens\maintenance\StateInterface' => [
            'class' => 'brussens\maintenance\states\FileState',
            'fileName' => 'myfile.ext'
            'directory' => '@mypath'
        ]
    ]
]
```

## Filters
You can use filters for allow excepts:

```php
'container' => [
    'singletons' => [
        'brussens\maintenance\Maintenance' => [
            'class' => 'brussens\maintenance\Maintenance',
            // Route to action
            'route' => 'maintenance/index',
            // Filters. Read Filters for more info.
            'filters' = [
                //Allowed URIs filter. Your can allow debug panel URI.
                [
                    'class' => 'brussens\maintenance\filters\URIFilter',
                    'uri' => [
                        'debug/default/toolbar',
                        'debug/default/view',
                        'site/login'
                    ]
                ],
                // Allowed roles filter
                [
                    'class' => 'brussens\maintenance\filters\RoleFilter',
                    'roles' => [
                        'administrator'
                    ]
                ],
                // Allowed IP addresses filter
                [
                    'class' => 'brussens\maintenance\filters\IpFilter',
                    'ips' => [
                        '127.0.0.1'
                    ]
                ],
                //Allowed user names
                [
                    'class' => 'brussens\maintenance\filters\UserFilter',
                    'checkedAttribute' => 'username',
                    'users' => [
                        'BrusSENS',
                    ],
                ]
            ],
        ]
    ]
]
```
You can create custom filter:
```php
class MyCustomFilter extends Filter
{
    public $time;
    
    /**
     * @return bool
     */
    public function isAllowed()
    {
        return (bool) $this->time > 3600;
    }
}
```

## Set maintenance mode by console or dashboard

Add to your console or common config file:
```php
'container' => [
    'singletons' => [
        'brussens\maintenance\StateInterface' => [
            'class' => 'brussens\maintenance\states\FileState',
            'fileName' => 'myfile.ext',
            'directory' => '@mypath'
        ]
    ]
]
```
Create controller:
```php
class MaintenanceController extends Controller
{
    /**
     * @var StateInterface
     */
    protected $state;
    
    public function __construct(string $id, Module $module, StateInterface $state, array $config = [])
    {
        $this->state = $state;
        parent::__construct($id, $module, $config);
    }
    
    public function actionEnable()
    {
        $this->state->enable();
    }
    public function actionDisable()
    {
        $this->state->disable();
    }
}
```

Now you can set mod by command:
```
php yii maintenance/enable
```
```
php yii maintenance/disable
```
