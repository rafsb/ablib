<?php

class Sockets extends Activity
{
    public $Host;
    public $Port;

    private $Sock;
    private $Pipe;
    private $Sock;
    private $Pipe;
    private $Listener;
    private $Accept;
    private $IncomingMesg;

    public function connect(String $host="0.0.0.0", int $port=10001)
    {
        $this->Host = $host;
        $this->Port = $port;
        $this->Sock = socket_create(AF_INET, SOCK_STREAM, 0) or die(Core::response(-1, "Socket::open -> Couldn`t create socket"));
        $this->Pipe = socket_bind($this->Sock, $this->Host, $this->Port) or die(Core::response(-2, "Socket::open -> Couldn`t bind socket");
        $this->Listener = socket_listen($this->Sock, 3) or die(Core::response(-3, "Socket::open -> Couldn`t listen socket");
        $this->Accept = socket_accept($this->Sock) or die(Core::response(-4, "Socket::open -> Couldn`t accept socket");
        $this->IncomingMesg = socket_read($this->Accept, 2048) or die(Core::response(-5, "Socket::open -> Couldn`t read socket");
    }

    public function close()
    {
        socket_close($this->Accept, $this->Sock);
    }

    public function write($obj)
    {
        if(is_object($obj) || is_array($obj)) $obj = Convert::json($obj);
        socket_write($this->Accept, $obj) or die(Core::response(-1, "Socket::write -> Couldn`t write into socket");;
    }

    public static function handler(Closure $fn, array $connection=[])
    {
        $host = isset($connection["host"]) ? $connection["host"] : null;
        $port = isset($connection["port"]) ? $connection["port"] : null;
        $sock = new Sockets($host, $port);
        $sock->write($fn($this->IncomingMesg, [
            "argv" => Request::in()
            , "referer" => $_SERVER["HTTP_REFERER"]
            , "socket" => [
                "port" => $sock->Port
                , "host" => $sock->Host
            ]
        ]));
        $sock->close();
    }

    public static function test()
    {
        self::handler(function($one,$two){ 
            echo "<pre>";
            print_r($one);
            print_r($two);
         })
    }

    public function __construct(String $host=null, int $port=null)
    {
        if($host&&$port) $this->connect($host, $port)
    }
}