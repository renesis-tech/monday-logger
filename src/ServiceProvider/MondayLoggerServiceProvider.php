<?php


namespace Renesis\MondayLogger\ServiceProvider;


use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Renesis\MondayLogger\Logger\ExceptionLogging;

class MondayLoggerServiceProvider extends ServiceProvider
{

    /**
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function boot()
    {
        $this->publishConfigurations();
    }

    /**
     * Copy configuration file to config directory
     *
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function publishConfigurations()
    {
        $this->publishes([
            __DIR__.'/../Configurations/monday-logger.php' => config_path('monday-logger.php'),
        ]);
    }

    /**
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function register()
    {
        $this->registerMondayLogger();
    }

    /**
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function registerMondayLogger()
    {
        $this->app->singleton('monday.logger',function ($app){

            $config = $app['config']->get('monday-logger');

            if ($config == null){
                config(['monday-logger' => include __DIR__.'/../Configurations/monday-logger.php']);
                $config = $app['config']->get('monday-logger');
            }

            if (!getenv('MONDAY_API_KEY') and !$config){
                throw new \Exception('Monday Configuration are not complete');
            }

            if (!isset($config['board_id']) || !isset($config['group_id'])){
                throw new \Exception('params: board_id and group_id required in monday-logger configurations');
            }

            return new ExceptionLogging($app);
        });

    }
}