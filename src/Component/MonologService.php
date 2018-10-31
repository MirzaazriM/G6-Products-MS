<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 9/3/18
 * Time: 10:34 AM
 */

namespace Component;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MonologService
{
    private $monolog;

    /**
     * Construct monolog service
     *
     * MonologService constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->monolog = new Logger('monolog');
        $this->monolog->pushHandler(new StreamHandler('../resources/loggs/monolog.txt', Logger::WARNING));
    }


    /**
     * Return monolog
     *
     * @return Logger
     */
    public function getDependency():Logger {
        return $this->monolog;
    }

}