<?php
namespace Alpaca\Driver;

class Webkit
{
    protected $app;

    /* @var Webkit\Browser */
    protected $browser;

    public function __construct($app, $options = array())
    {
        $this->app = $app;
        if (isset($options['browser'])) {
            $this->browser = $options['browser'];
        } else {
            $this->browser = new \Alpaca\Driver\Webkit\Browser(array(
                "ignore_ssl_errors" => $options['ignore_sssl_errors']
            ));
        }
    }

    public function currentUrl()
    {
        return $this->browser->currentUrl();
    }

    public function visit($path)
    {
        $this->browser->visit($path);
    }

    /**
     * @todo implement correctly
     * @param $query
     */
    public function find($query)
    {
        $this->browser->find($query);
    }

    public function source()
    {
        return $this->browser->source();
    }

    public function body()
    {
        return $this->browser->body();
    }

    public function setHeader($key, $value)
    {
        $this->browser->setHeader($key,$value);
    }

    public function evaluateScript($script)
    {
        return $this->browser->evaluateScript($script);
    }

    public function executeScript($script)
    {
        $value = $this->browser->executeScript($script);
        if (!empty($value)) {
            return $value;
        }
    }

    public function consoleMessage()
    {
        return $this->browser->consoleMessage();
    }

    public function errorMessage()
    {
        //return $this->browser->errorMessage();
    }

    public function responseHeaders()
    {
        return $this->responseHeaders();
    }

    public function statusCode()
    {
        return $this->browser->statusCode();
    }

    public function withinFrame($frame_id_or_index, $func)
    {
        $this->browser->frameFocus($frame_id_or_index);
        try {
            $func();
        } catch (\Exception $e) {

        }
        $this->browser->frameFocus();
    }

    function reset()
    {
        $this->browser->reset();
    }

    public function render($path, $options = array())
    {
        if (!isset($options['width'])) {
            $options['width'] = 1000;
        }
        if (!isset($options['height'])) {
            $options['height'] = 10;
        }

        $this->browser->render($path, $options['width'], $options['height']);
    }

    public function serverPort()
    {

    }

    public function cookies()
    {

    }
}