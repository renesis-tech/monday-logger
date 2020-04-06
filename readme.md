## Laravel Monday Logger

Laravel monday logger is laravel plugin to send exception updates to Monday.com

### Installation

This package is compatible with laravel 5.4+.
Package can be installed using composer 

```$xslt
composer require renesis-tech/monday-logger
```

Register Service Provider in your provider array of ```config/app.php```

```$xslt
\Renesis\MondayLogger\ServiceProvider\MondayLoggerServiceProvider::class,
```
Add Facade to your ```config/app.php```

```$xslt
'MondayLogger' => \Renesis\MondayLogger\Facade\MondayLogger::class,
```

### Monday.com Configurations

Next you have to set monday.con configurations in ```.env``` file. 

1. Set monday.com V2 API key in .env file with key ```MONDAY_API_KEY```

2. Set Monday.com Board ID with key ```MONDAY_BOARD_ID```

3. Set Monday's Board group id with key ```MONDAY_BOARD_ID```

4. Set ```MONDAY_LOGGER_ENABLED``` to enable and disable monday.com logger, default is true.

###### !!!Without setting these configuration, package will not work.!!!

Here is how you can generate monday.com api v2 key [Monday.com Developers](https://monday.com/developers/v2).

Next you can publish configuration file by using this command 

```$xslt
php artisan vendor:publish --provider="Renesis\MondayLogger\ServiceProvider\MondayLoggerServiceProvider"
```

It will create configurations file in ``config`` directory of project
with an array.

```$xslt
return [
    'board_id' => env('MONDAY_BOARD_ID',null),
    'group_id' => env('MONDAY_GROUP_ID',null),
    'enabled' => true,
    'auth_info' => true
];
```

### Usage

To log each exception to monday.com call MondayLogger facade report method in report function of ```app/Exceptions/Handler.php```
```$xslt
public function report(Exception $exception)
    {
        //Enabled Monday Logger Reporting
        MondayLogger::report($exception);
        parent::report($exception);
    }
```

**Important**
It will not log if you are using try catch method, You have to manually call MondayLogger report Method in catch case
e.g.

```$xslt
try{
    //Some Logic Here
}catch (\Exception $e){
    MondayLogger::report($e)
}   
```

