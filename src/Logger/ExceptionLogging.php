<?php


namespace Renesis\MondayLogger\Logger;

use Renesis\MondayLogger\Monday\MondayConfiguration;
class ExceptionLogging
{
    /**
     * If acting user is authenticated or not
     *
     * @var $authentication
     */
    protected $authentication;

    /**
     * Get Auth user
     *
     * @var $authUser
     */
    protected $authUser;

    /**
     * Define log level Exception | Info
     *
     * @var $level
     */
    protected $level;

    /**
     * Monday Logger configurations array
     *
     * @var $configurations
     */
    protected $configurations = [];

    /**
     * Reporting is enabled in configuration if true then send logs to monday.com
     *
     * @var $enableReporting
     */
    protected $enableReporting = false;

    /**
     * @var MondayConfiguration
     */
    private $mondayConfiguration;


    /**
     * ExceptionLogging constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->authStatus();
        $this->configurations =  $app['config']->get('monday-logger');
        $this->reportingStatus($app);
        $this->mondayConfiguration = new MondayConfiguration();

    }

    /**
     * Update reporting status variable
     *
     * @param $app
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
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

    /**
     * Check if user is authenticated then update auth user object
     *
     * @return $this
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function authStatus()
    {
        //TODO: Implement configuration check if auth status is enabled in configurations
        if (auth()->check()){
            $this->authentication = true;
            $this->authUser = auth()->user();
        }

        return $this;
    }

    /**
     * Report Exception to monday.com
     *
     * @param \Exception $exception
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
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

    /**
     * Prepare exception data and error message to report
     *
     * @param \Exception $exception
     * @return array
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
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

    /**
     * Report Info level exception to monday.com
     *
     * @param \Exception $exception
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function info(\Exception $exception)
    {
        $this->level = 'INFO';

        $error = $this->prepareExceptionMessage($exception);

        $this->mondayConfiguration->reportToMonday($error);
    }

}