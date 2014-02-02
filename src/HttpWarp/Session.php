<?php
/**
 * Created by JetBrains PhpStorm.
 * User: orbisnull
 * Date: 31.08.13
 * Time: 22:13
 * To change this template use File | Settings | File Templates.
 */

namespace HttpWarp;


class Session {

    protected $closures = [];

    protected $started = false;

    //protected $actions = ['start', 'destroy', 'status', 'set', 'get', 'rm'];

    function __construct(array $closures = null)
    {
        if (!is_null($closures)) {
            $this->setClosures($closures);
        }
    }

    /**
     * @param \Closure[] $closures
     */
    protected function setClosures(array $closures)
    {
        foreach ($closures as $action=>$closure)
        {
            if (is_callable($closure)) {
                $this->closures[$action] = $closure;
            }
        }
    }

    /**
     * @param $action
     * @return bool|\Closure
     */
    protected function getClosure($action)
    {
        if (isset($this->closures[$action]) && is_callable($this->closures[$action])) {
            return $this->closures[$action];
        } else {
            return false;
        }
    }

    public function start()
    {
        $closure = $this->getClosure('start');
        if ($closure) {
            return call_user_func($closure);
        }
        return session_start();
    }

    public function status()
    {
        $closure = $this->getClosure('status');
        if ($closure) {
            return call_user_func($closure);
        }
        return session_status();
    }

    public function prepare()
    {
        if (empty($this->closures)) {
            if (!$this->started) {
                return $this->start();
            }
        }
    }

    public function destroy()
    {
        $closure = $this->getClosure('destroy');
        if ($closure) {
            return call_user_func($closure);
        }
        session_destroy();
    }

    public function set($name, $value)
    {
        $closure = $this->getClosure('set');
        if ($closure) {
            return call_user_func_array($closure, [$name, $value]);
        }
        $this->prepare();
        $_SESSION[$name] = $value;
        return true;
    }

    public function has($name)
    {
        $closure = $this->getClosure('has');
        if ($closure) {
            return call_user_func($closure, $name);
        }
        $this->prepare();
        return isset($_SESSION[$name]);
    }

    public function get($name, $default = null)
    {
        $closure = $this->getClosure('get');
        if ($closure) {
            return call_user_func_array($closure, [$name, $default]);
        }
        $this->prepare();
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    public function rm($name)
    {
        $closure = $this->getClosure('rm');
        if ($closure) {
            return call_user_func($closure, $name);
        }
        $this->prepare();
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
        return true;
    }


}