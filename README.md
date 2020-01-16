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
            'route' => '//maintenance/index',

            // Filters. Read Filters for more info.
            'filters' => [
                [
                    'class' => 'brussens\maintenance\filters\URIFilter',
                    'uri' => [
                        'debug/default/toolbar',
                        'debug/default/view',
                        'site/login',
                    ]
                ]
            ],

            // HTTP Status Code
            'statusCode' => 503,

            //Retry-After header
            'retryAfter' => 120 // or Wed, 21 Oct 2015 07:28:00 GMT for example
        ],
        'brussens\maintenance\StateInterface' => [
            'class' => 'brussens\maintenance\states\FileState',

            // optional: use different filename for controlling maintenance state:
            // 'fileName' => 'myfile.ext',

            // optional: use different directory for controlling maintenance state:
            // 'directory' => '@mypath',
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
            // Route to action. There is a example maintenance controller
            // provided with the package. If you dont want to use it, you 
            // can define your custom action one here:
            'route' => 'maintenance/index',
            // Filters. Read Filters for more info.
            'filters' => [
                //Allowed URIs filter. Your can allow debug panel URI.
                [
                    'class' => 'brussens\maintenance\filters\URIFilter',
                    'uri' => [
                        'debug/default/toolbar',
                        'debug/default/view',
                        'site/login',
                    ]
                ],
                // Allowed roles filter
                [
                    'class' => 'brussens\maintenance\filters\RoleFilter',
                    'roles' => [
                        'administrator',
                    ]
                ],
                // Allowed IP addresses filter
                [
                    'class' => 'brussens\maintenance\filters\IpFilter',
                    'ips' => [
                        '127.0.0.1',
                    ]
                ],
                // Allowed user names
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
You can create a custom filter:
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
            // optional: use different filename for controlling maintenance state:
            // 'fileName' => 'myfile.ext',

            // optional: use different directory for controlling maintenance state:
            // 'directory' => '@mypath',
        ]
    ]
],
'controllerMap' => [
      'maintenance' => [
          'class' => 'brussens\maintenance\commands\MaintenanceController',
      ],
],

```

Now you can set mode by command:
```
php yii maintenance/enable --message="The system is in a planned maintenance and is available again in estimated three hours"
```
```
php yii maintenance/disable
```

A small MaintenanceController and maintenance view is provided with the package.
You can place this components in your application by running in your application root:

```
$ cp vendor/brussens/yii2-maintenance-mode/src/controllers/MaintenanceController.php controllers/
$ co -r vendor/brussens/yii2-maintenance-mode/src/views/maintenance/ views/
```

