<?php


namespace Renesis\MondayLogger\Logger;


use Illuminate\Support\Facades\Log;
use Renesis\MondayLogger\Monday\MondayConfiguration;

class ExceptionLogging
{
    protected $authentication;

    protected $authUser;

    protected $level;

    /**
     * @var MondayConfiguration
     */
    private $mondayConfiguration;


    public function __construct()
    {
        $this->authStatus();
        $this->mondayConfiguration = new MondayConfiguration();
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

//        dd($this->mondayConfiguration->test());

        // TODO: Implement if reporting is enabled in configurations
        $error = $this->prepareExceptionMessage($exception);

        $this->mondayConfiguration->createItem($error);
    }

    public function prepareExceptionMessage(\Exception $exception)
    {
        $message = $exception->getMessage();

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

}