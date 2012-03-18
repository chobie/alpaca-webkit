<?php
namespace Alpaca\Driver;

class Webkit
{
    protected $app;

    /* @var Webkit\Browser */
    protected $browser;

    /**
     * create webkit browser instance
     *
     * @param $app
     * @param array $options
     */
    public function __construct($app = null, $options = array())
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


    /**
     * return current url
     *
     * @return string
     */
    public function currentUrl()
    {
        return $this->browser->currentUrl();
    }

    /**
     * visit specified url.
     *
     * @param $path
     * @return void
     */
    public function visit($path)
    {
        $this->browser->visit($path);
    }

    /**
     * find node with xpath
     *
     * @param $query
     * @return array
     */
    public function find($query)
    {
        $result = array();
        foreach($this->browser->find($query) as $native) {
            $result[] = new Webkit\Node($this, $native);
        }

        return $result;
    }

    /**
     * find the element and set value
     *
     * @param $name input tag name or id
     * @param $value
     * @return bool
     */
    public function fillIn($name, $value)
    {
        $ops = array(
            "//input[@name='{$name}']",
            "//input[@id='{$name}']",
            "//textarea[@name='{$name}']",
            "//textarea[@id='{$name}']",
        );

        foreach ($ops as $op) {
            $result = $this->find($op);
            if (count($result)) {
                $result[0]->set($value);
                return true;
            } else {
                continue;
            }
        }
        return false;
    }


    /**
     * click link with specified text node
     *
     * @param $title
     * @return bool
     */
    public function clickLink($title)
    {
        $result = $this->find("//a[text()=\"{$title}\"]");
        if (count($result)) {
            $result[0]->click();
            return true;
        } else {
            return false;
        }
    }

    /**
     * click button with specified value
     *
     * @param $value
     * @return bool
     */
    public function clickButton($value)
    {
        $result = $this->find("//input[@value=\"{$value}\"]");
        if (count($result)) {
            $result[0]->click();
            return true;
        } else {
            return false;
        }
    }


    /**
     * return current source code
     *
     * @return string
     */
    public function source()
    {
        return $this->browser->source();
    }

    /**
     * return current body
     *
     * @return string
     */
    public function body()
    {
        return $this->browser->body();
    }

    /**
     * set header
     *
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value)
    {
        $this->browser->setHeader($key,$value);
    }

    /**
     * evaluate specified javascript on current frame.
     *
     * this method will return json array
     *
     * @param $script
     * @return mixed
     */
    public function evaluateScript($script)
    {
        return $this->browser->evaluateScript($script);
    }

    /**
     * execute specified javascript on current frame
     *
     * @param $script
     * @return mixed
     */
    public function executeScript($script)
    {
        $value = $this->browser->executeScript($script);
        if (!empty($value)) {
            return $value;
        }
    }


    /**
     * probably this method will obtain console.log() message.
     *
     * @return array
     */
    public function consoleMessage()
    {
        return $this->browser->consoleMessage();
    }

    public function errorMessage()
    {
        //return $this->browser->errorMessage();
    }

    /**
     * obtain current response headers.
     *
     * @return mixed
     */
    public function responseHeaders()
    {
        return $this->responseHeaders();
    }

    /**
     * obtain current http status
     *
     * @return int
     */
    public function statusCode()
    {
        return $this->browser->statusCode();
    }

    /**
     * find and execute php function with found scope
     *
     * @todo implement correctly
     *
     * @param $frame_id_or_index
     * @param $func
     */
    public function withinFrame($frame_id_or_index, $func)
    {
        $this->browser->frameFocus($frame_id_or_index);
        try {
            $func();
        } catch (\Exception $e) {
            throw $e;
        }

        $this->browser->frameFocus();
    }

    /**
     * reset webkit browser
     *
     * @return void
     */
    function reset()
    {
        $this->browser->reset();
    }

    /**
     * render current frame image
     *
     * @param $path
     * @param array $options width and height parameters are required.
     */
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

    /**
     * @return int port number
     */
    public function getServerPort()
    {
        return $this->browser->getPort();
    }

    /**
     * @return array cookies
     */
    public function getCookies()
    {
        return $this->browser->getCookies();
    }

    /**
     * @return Webkit\Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }
}