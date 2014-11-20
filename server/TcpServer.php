<?php
/**
* Class Server
*/
class TcpServer
{
	static $sw_mode = SWOOLE_PROCESS;
	protected $sw;
	protected $swooleSetting;
	protected $protocol;
	
	function __construct($host, $port, $timeout = 0) 
	{
		$this->sw = swoole_server_create ( $host, $port, self::$sw_mode, SWOOLE_SOCK_TCP );
		$this->host = $host;
		$this->port = $port;
	
		$this->swooleSetting = array (
				'timeout' => 2.5, // select and epoll_wait timeout.
				'poll_thread_num' => 2, // reactor thread num
				'writer_num' => 2, // writer thread num
				'worker_num' => 2, // worker process num
				'backlog' => 128, // listen backlog
				'open_cpu_affinity' => 1,
				'open_tcp_nodelay' => 1 ,
		);
	}
	
	
	function daemonize() 
	{
		$this->swooleSetting ['daemonize'] = 1;
	}
	
	
	function run( $setting = array() ) 
	{
		$set = array_merge ( $this->swooleSetting, $setting );
		swoole_server_set ( $this->sw, $set );
		swoole_server_handler ( $this->sw, 'onWorkerStart', array (
				$this->protocol,
				'onStart' 
		) );
		swoole_server_handler ( $this->sw, 'onConnect', array (
				$this->protocol,
				'onConnect' 
		) );
		swoole_server_handler ( $this->sw, 'onReceive', array (
				$this->protocol,
				'onReceive' 
		) );
		swoole_server_handler ( $this->sw, 'onClose', array (
				$this->protocol,
				'onClose' 
		) );
		swoole_server_handler ( $this->sw, 'onWorkerStop', array (
				$this->protocol,
				'onShutdown' 
		) );
		// swoole_server_handler($this->sw, 'onTimer', array($this->protocol, 'onReceive'));
		swoole_server_start ( $this->sw );
	}
	
	function shutdown() 
	{
		swoole_server_shutdown ( $this->sw );
	}
	
	function close($client_id) 
	{
		swoole_server_close ( $this->sw, $client_id );
	}
	
	function send($client_id, $data) 
	{
		swoole_server_send ( $this->sw, $client_id, $data );
	}
	
	function setProtocol($protocol) 
	{
		$this->protocol = $protocol;
		$this->protocol->server = $this;
	}
}