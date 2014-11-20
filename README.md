swooleIM
========

基于swoole的网页聊天室

=========

在linux的终端下运行 php server/WebSocket.php文件
运行前修改WebSocket.php文件中的IP和端口 $server = new TcpServer('*', 9501 );
和resource /js/chat.js中的ws = new WebSocket( "ws://127.0.0.1:9501" );