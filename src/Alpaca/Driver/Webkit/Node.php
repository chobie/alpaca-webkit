<?php
namespace Alpaca\Driver\Webkit;

class Node
{
    /* @var \Alpaca\Driver\Webkit */
    protected $driver;

    protected $native;

    public function __construct(\Alpaca\Driver\Webkit $driver, $native)
    {
        $this->driver = $driver;
        $this->native = $native;
    }

    public function set($value)
    {
        return $this->invoke("set",$value);
    }

    public function find($xpath)
    {
        return $this->invoke("findWithin",$xpath);
    }

    public function text()
    {
        return $this->invoke("text");
    }

    public function tagName()
    {
        return $this->invoke("tagName");
    }

    public function click()
    {
        return $this->invoke("click");
    }

    protected function invoke()
    {
        $args = func_get_args();
        $command = array_shift($args);

        $arguments = array($command, $this->native);
        foreach($args as $arg) {
            $arguments[] = $arg;
        }

        return $this->driver->getBrowser()->command("Node", $arguments);
    }
}