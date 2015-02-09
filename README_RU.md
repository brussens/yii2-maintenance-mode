#Yii2 компонент режима обслуживания

##Установка
Запускаем
```
php composer.phar require --prefer-dist brussens/yii2-maintenance-mode "*"
```

или добавляем

```
"brussens/yii2-maintenance-mode": "*"
```

в секцию "require" вашего `composer.json` файла.

Добавляем в ваш конфигурационный файл:
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
##Опции
```php
'maintenanceMode'=>[

  // Пространство имён класса
  'class' => '\brussens\maintenance\MaintenanceMode',
  
  // Статус режима
  'enabled' => true,
  
  // Маршрут к экшену
  'route' => 'maintenance/index',
  
  // Сообщение
  'message'=>'Sorry, perform technical works.',
  
  // Разрешённые имена пользователей
  'users'=>[
    'BrusSENS',
  ],
  
  // Разрешённые роли
  'roles'=>[
    'administrator',
  ],
  
  // Разрешённые IP адреса
  'ips'=>[
    '127.0.0.1',
  ],
  
  // Разрешённые URL'ы
  'urls'=>[
    'site/login'
  ],
  
  // Путь до лайаута
    'layoutPath'=>'@web/maintenance/layout',
    
  // Путь до представления
  'viewPath'=>'@web/maintenance/view',
  
  // Название атрибута имени пользователя
  'usernameAttribute'=>'login',
  
],
```

##Профит
