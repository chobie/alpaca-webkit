<?php
namespace Alpaca\Driver\Webkit;

class Node
{
    /* @var \Alpaca\Driver\Webkit */
    protected $driver;

    /* @var int internal index id */
    protected $native;

    /**
     * create Node object
     *
     * @param \Alpaca\Driver\Webkit $driver
     * @param $native
     */
    public function __construct(\Alpaca\Driver\Webkit $driver, $native)
    {
        $this->driver = $driver;
        $this->native = $native;
    }

    /**
     * set form value
     *
     * @param $value
     * @return mixed
     */
    public function set($value)
    {
        return $this->invoke("set",$value);
    }


    /**
     * dunno
     *
     * @param $xpath
     * @return mixed
     */
    public function find($xpath)
    {
        return $this->invoke("findWithin",$xpath);
    }

    /**
     * obtain current text node
     *
     * @return string
     */
    public function text()
    {
        return $this->invoke("text");
    }

    /**
     * obtain current tag name
     *
     * @return string
     */
    public function tagName()
    {
        return $this->invoke("tagName");
    }

    /**
     * click current node
     *
     * @return mixed
     */
    public function click()
    {
        return $this->invoke("click");
    }

    /**
     * invoke js function on capybara webkit server.
     *
     * @param string $function_name
     * @param array $parameters
     * @return mixed
     */
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