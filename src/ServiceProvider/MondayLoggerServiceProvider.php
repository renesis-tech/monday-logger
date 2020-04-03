<?php


namespace Renesis\MondayLogger\ServiceProvider;


use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Renesis\MondayLogger\Logger\ExceptionLogging;

class MondayLoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('monday.logger',function ($app){

            if (!getenv('MONDAY_API_KEY') and ! $app['config']->get('services.renesis-monday')){
                //TODO: Implement all required configurations check
                throw new \Exception('Monday Configuration are not complete');
            }

            return $this->mondayLogger($app);
        });
    }

    public function mondayLogger($app)
    {
        return new ExceptionLogging();
    }
}