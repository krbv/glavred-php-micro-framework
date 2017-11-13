<?php namespace Glavred\Singleton;

abstract class AAASingletons
{
    private static $instances = array();

    protected function __construct() {
    }

    public static function link()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    private function __clone() {
        throw new \SingeltonException (__CLASS__." : ".__FUNCTION__." you can't clone");
    }
    //Метод __wakeup вызывается при попытке десериализовать экземпляр класса.
    private function __wakeup() {
        throw new \SingeltonException (__CLASS__." : ".__FUNCTION__." you can't __wakeup");
    } 
} 


