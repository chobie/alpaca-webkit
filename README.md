# Alpaca-Webkit

A Alpaca driver that uses Webkit via capybara-webkit's webkit_server

# Examples

currently it expects webkit_server on "/Library/Ruby/Gems/1.8/gems/capybara-webkit-0.11.0/bin/webkit_server".

````php
<?php
require "src/Alpaca/Driver/Webkit.php";
require "src/Alpaca/Driver/Webkit/Browser.php";

$webkit = new Alpaca\Driver\Webkit(null);
$webkit->visit("http://github.com/");
var_dump($webkit->body());
````

# License

MIT License