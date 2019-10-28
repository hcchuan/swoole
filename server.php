<?php
//--------在linux服务器上用shell命令执行  php /home/wwwroot/swoole/server.php
$serv = new swoole_server('127.0.0.1', 9027);
//server的创建，只需要绑定要监听的ip和端口，如果ip指定为127.0.0.1，则表示客户端只能位于本机才能连接，其他计算机无法连接。
//端口这里指定为9501，可以通过netstat查看下该端口是否被占用。如果该端口被占用，可更改为其他端口，如9502，9503等。
$serv->set([    //我开2个worker进程处理我们的业务
    'worker_num' => 2,
]);
// 有新的客户端连接时，worker进程内会触发该回调
$serv->on('Connect', function ($serv, $fd) {
    echo "新客户端连接成功" . PHP_EOL;
});
//参数$serv是我们一开始创建的swoole_server对象，
//参数$fd是唯一标识，用于区分不同的客户端，同时该参数是1-1600万之间可以复用的整数。
// server接收到客户端的数据后，worker进程内触发该回调
$serv->on('Receive', function ($serv, $fd, $fromId, $data) {
    // 收到数据后发送给客户端
    $serv->send($fd, '服务器：我已收到数据----'. $data);
});
// 客户端断开连接或者server主动关闭连接时 worker进程内调用
$serv->on('Close', function ($serv, $fd) {
    echo "客户端已关闭" . PHP_EOL;
});
// 启动server
$serv->start();
