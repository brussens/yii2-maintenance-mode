#Yii2 Maintenance mode component

##Install
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
'bootstrap' => ['log', 'maintenanceMode'],
  ...
  'components' => [
  
    'maintenanceMode'=>[
    
      'class' => '\brussens\maintenance\MaintenanceMode',
      
    ],
    ...
  ],
```
##Options
```php
'maintenanceMode'=>[

  // Component class namespace
  'class' => '\brussens\maintenance\MaintenanceMode',
  
  // Mode status
  'enabled'=>true,
  
  // Route to action
  'route'=>'maintenance/index',
  
  // Show message
  'message'=>'Sorry, perform technical works.',
  
  // Allowed user names
  'users'=>[
    'BrusSENS',
  ],
  
  // Allowed roles
  'roles'=>[
    'administrator',
  ],
  
  // Allowed IP addresses
  'ips'=>[
    '127.0.0.1',
  ],
  
  // Allowed URLs
  'urls'=>[
    'site/login'
  ],
  
  // Layout path
    'layoutPath'=>'@web/maintenance/layout',
    
  // View path
  'viewPath'=>'@web/maintenance/view',
  
  // User name attribute name
  'usernameAttribute'=>'login',
  
  // HTTP Status Code
  'statusCode'=>503,
  
],
```
