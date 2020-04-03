<?php


namespace Renesis\MondayLogger\Facade;


use Illuminate\Support\Facades\Facade;

class MondayLogger extends Facade
{
    /**
     * @return string
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    protected static function getFacadeAccessor()
    {
        return 'monday.logger';
    }
}