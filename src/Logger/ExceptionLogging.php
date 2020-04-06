<?php


namespace Renesis\MondayLogger\Logger;


use Illuminate\Support\Facades\Log;
use Renesis\MondayLogger\Monday\MondayConfiguration;

class ExceptionLogging
{
    protected $authentication;

    protected $authUser;

    protected $level;

    protected $configurations = [];

    protected $enableReporting = false;
    /**
     * @var MondayConfiguration
     */
    private $mondayConfiguration;


    public function __construct($app)
    {
        $this->authStatus();
        $this->configurations =  $app['config']->get('monday-logger');
        $this->reportingStatus($app);
        $this->mondayConfiguration = new MondayConfiguration();

    }

    public function reportingStatus($app)
    {
        if (isset($this->configurations['enabled'])) {
            $this->enableReporting = $this->configurations['enabled'] ?? false;
        }else {
            $environment = $app->bound('env') ? $app->environment() : 'local';
            if ($environment == 'production') {
                $this->enableReporting = true;
            }
        }
    }

    public function authStatus()
    {
        //TODO: Implement configuration check if auth status is enabled in configurations
        if (auth()->check()){
            $this->authentication = true;
            $this->authUser = auth()->user();
        }

        return $this;
    }

    public function report(\Exception $exception)
    {
        $this->level = 'Exception';

        if (!$this->enableReporting){
            return;
        }

        // TODO: Implement if reporting is enabled in configurations
        $error = $this->prepareExceptionMessage($exception);

        $this->mondayConfiguration->reportToMonday($error);
    }

    public function prepareExceptionMessage(\Exception $exception)
    {
        $message = '['.$this->level.'] '.$exception->getMessage();

        $file = $exception->getFile();

        $line = $exception->getLine();

        $trace = $exception->getTraceAsString();

        return [
            'message' => "$message in File: $file on Line Number: $line",
            'trace' => $trace,
            'level' => $this->level,
            'user' => $this->authUser,
            'authentication' => $this->authentication
        ];
    }

    public function info(\Exception $exception)
    {
        $this->level = 'INFO';

        $error = $this->prepareExceptionMessage($exception);

        $this->mondayConfiguration->reportToMonday($error);
    }

}