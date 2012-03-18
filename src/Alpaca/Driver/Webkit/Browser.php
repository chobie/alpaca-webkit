<?php
namespace Alpaca\Driver\Webkit;

class Browser
{
    /* @var resource server socket */
    protected $server;

    /* @var int */
    protected $port;

    /* @var resource proc_opened process*/
    protected $process;


    /**
     * return current server port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    public function __construct($options = array())
    {
        $this->startServer();
        $this->connect();
    }

    public function currentUrl()
    {
        return $this->command("CurrentUrl");
    }

    public function render($path, $width = 1024, $height = 680)
    {
        $this->command("Render",array($path,$width,$height));
    }

    /**
     * visit specified url
     *
     * @param $url
     * @return int result status
     */
    public function visit($url)
    {
        return $this->command("Visit", array($url));
    }

    /**
     * dunno
     *
     * @return array
     */
    public function consoleMessage()
    {
        $result = array();
        foreach (explode("\n",$this->command("ConsoleMessages")) as $message) {
            $part = explode("|", $message, 3);
            $result[] = array("source" => $part[0], "line_number" => (int)$part[1], "message" => $part[2]);
        }
        return $result;
    }

    /**
     * returns current response header.
     *
     * @return array
     */
    public function responseHeader()
    {
        $result = array();
        foreach(explode("\n", $this->command("Headers")) as $line){
            list($key, $value) = explode(": ", $line);
            $result[$key] = $value;
        }

        return $result;
    }

    public function setHeader($key, $value)
    {
        $this->command("Header",array($key, $value));
    }


    /**
     * returns http status
     *
     * @return int
     */
    public function statusCode()
    {
        return (int)$this->command("Status");
    }

    /**
     * i'm not sure what's the difference between body.
     *
     * @return string
     */
    public function source()
    {
        return $this->command("Source");
    }

    /**
     * Reset browser session
     *
     * @return void
     */
    public function reset()
    {
        $this->command("Reset");
    }

    /**
     * i dunno
     *
     * @param $query
     * @return array
     */
    public function find($query)
    {
        $ret = $this->command("Find",array($query));
        if (empty($ret)) {
            return array();
        }

        return explode(",", $ret);
    }

    /**
     * obtain current frame buffer as string.
     *
     * @return string
     */
    public function body()
    {
        return $this->command("Body");
    }

    /**
     * evaluate specified js and returns it result as json object
     *
     * @param array $js
     */
    public function evaluateScript($js)
    {
        $json = $this->command("Evaluate", array($js));
        return json_decode("[{$json}]", true);
    }


    /**
     * execute javascript
     *
     * @param $js
     */
    public function executeScript($js)
    {
        return $this->command("Execute", array($js));
    }

    /**
     * connect to spawned webkit_serer.
     *
     * @throws \RuntimeException
     */
    protected function connect()
    {
        $server = stream_socket_client("tcp://localhost:{$this->port}",$errno, $errstr,5);
        if (is_resource($server)) {
            $this->server = $server;
        } else {
            throw new \RuntimeException("could not connect to webkit_server");
        }
    }

    /**
     * send command to webkit_server
     *
     * @param $command
     * @param array $args
     * @return mixed the result
     */
    public function command($command, $args = array())
    {
        //"#" . $command . PHP_EOL;

        fwrite($this->server, $command . "\n");
        fwrite($this->server, count($args) . "\n");

        foreach($args as $arg) {
            fwrite($this->server, strlen($arg) . "\n");
            fwrite($this->server, $arg);
        }
        $this->check();
        return $this->readResponse();
    }

    protected function check()
    {
        $error = trim(fgets($this->server));
        if ($error != "ok") {
            throw new \Exception($this->readResponse($this->server));
        }
    }

    /**
     * @return string
     */
    protected function readResponse()
    {
        $data = "";
        $nread = trim(fgets($this->server));

        if ($nread == 0) {
            return $data;
        }

        $read = 0;
        while ($read < $nread) {
            $tmp   = fread($this->server,$nread);
            $read += strlen($tmp);
            $data .= $tmp;
        }
        return $data;
    }


    public function startServer()
    {
        $pipes = array();
        $server_path = "/Library/Ruby/Gems/1.8/gems/capybara-webkit-0.11.0/bin/webkit_server";
        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
        );

        $process = proc_open($server_path, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $data = fgets($pipes[1]);
            $this->port = $this->discoverServerPort($data);
            $this->process = $process;
        } else {
            throw new \RuntimeException("coudn't lunch webkit_server");
        }

        register_shutdown_function(array($this,"registerShutdownHook"));
    }

    /**
     * @param $line
     * @return mixed
     * @throws \RuntimeException
     */
    protected function discoverServerPort($line)
    {
        if (preg_match('/listening on port: (\d+)/',$line,$matches)) {
            return $matches[1];
        } else {
            throw \RuntimeException("couldn't find server port");
        }
    }

    /**
     * clear cookies
     *
     */
    public function clearCookies()
    {
        $this->command("ClearCookies");
    }

    /**
     * dunno
     *
     * @param null $frame_id_or_index
     * @return mixed
     */
    public function frameFocus($frame_id_or_index = null)
    {
        if (is_string($frame_id_or_index)) {
            return $this->command("FrameFocus", array("", $frame_id_or_index));
        } else if ($frame_id_or_index) {
            return $this->command("FrameFocus", array($frame_id_or_index));
        } else {
            return $this->command("FrameFocus");
        }
    }

    public function setProxy($options = array())
    {
        $options = array_merge(array("host"=>"localhost","port"=>0,"user"=>"","pass"=>""), $options);
        $this->command("SetProxy", array($options['host'],$options['port'],$options['user'],$options['pass']));
    }

    public function clearProxy()
    {
        $this->command("SetProxy");
    }

    /**
     * set cookies
     *
     * @param string $cookie
     */
    public function setCookies($cookie)
    {
        $this->command("setCookies", array($cookie));
    }

    /**
     * get cookies.
     *
     * @todo parse cookie string
     *
     * @return array
     */
    public function getCookies()
    {
        $result = array();
        foreach(explode("\n",$this->command("GetCookies")) as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $result[] = $line;
            }
        }
        return $result;
    }

    public function __destruct()
    {
        $this->killServer();
    }

    protected function killServer()
    {
        if (is_resource($this->process)) {
            proc_terminate($this->process);
        }
    }

    public function registerShutdownHook()
    {
        $this->killServer();
    }
}