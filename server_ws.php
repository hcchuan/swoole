<?php
//--------在linux服务器上用shell命令执行  php /home/wwwroot/swoole/server_ws.php
header('Access-Control-Allow-Origin:*');//允许所有IP跨域请求
header('Access-Control-Allow-Methods:*');//响应类型
header('Access-Control-Allow-Headers:Origin,X-Requested-With,Content-Type,Accept');// 响应头设置

//注意事项：
//1、关闭centos服务器防火墙 systemctl stop firewalld
//2、如果是阿里云ecs服务器，则要在“阿里云后台----ecs服务器----安全组规则”设置端口9502开放
//3、服务器上要启动服务端（文件路径可自定义） php /home/wwwroot/swoole/server_ws.php

//创建WebSocket服务器对象，监听0.0.0.0:9502端口----ip只能写0.0.0.0，不能写127.0.0.1，端口只能用9502
$ws = new swoole_websocket_server('0.0.0.0',9502);//客户端链接成功
//$ws = new swoole_websocket_server("192.168.55.171", 9502);//客户端链接成功
//$ws = new swoole_websocket_server("127.0.0.1", 9502);//客户端链接失败

//监听WebSocket链接打开事件
$ws->on('open', function ($ws, $request) {
    //var_dump($request->fd, $request->get, $request->server);
    print_r($request);
    $ws->push($request->fd, "hello, welcome");
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    //echo "Message: {$frame->data}\n";
    print_r($frame);
    $ws->push($frame->fd, "server: {$frame->data}");
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
    //删除已断开的客户端
    unset($ws->user_c[$fd-1]);
});
$ws->start();