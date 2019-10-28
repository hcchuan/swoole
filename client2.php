<?php
//--------在linux服务器上用shell命令执行  php /home/wwwroot/swoole/client2.php
//-----------单进程------------
/**
 * swoorl异步处理
 */
$serv = new swoole_server("127.0.0.1", 9030);
//配置task进程的数量，即配置task_worker_num这个配置项。比如我们开启一个task进程
$serv->set([
    'task_worker_num' => 1,
]);
$serv->on('Connect', function ($serv, $fd) {
    echo "new client connected." . PHP_EOL;
});
$serv->on('Receive', function ($serv, $fd, $fromId, $data) {
    echo "worker received data: {$data}" . PHP_EOL;

    // 投递一个任务到task进程中
    $serv->task($data);

    // 通知客户端server收到数据了
    $serv->send($fd, 'This is a message from server.');

    // 为了校验task是否是异步的，这里和task进程内都输出内容，看看谁先输出
    echo "worker continue run."  . PHP_EOL;
});
/**
 * $serv swoole_server
 * $taskId 投递的任务id,因为task进程是由worker进程发起，所以多worker多task下，该值可能会相同
 * $fromId 来自那个worker进程的id
 * $data 要投递的任务数据
 */
$serv->on('Task', function ($serv, $taskId, $fromId, $data) {
    echo "task start. --- from worker id: {$fromId}." . PHP_EOL;
    for ($i=0; $i < 5; $i++) {
        sleep(1);
        echo "task runing. --- {$i}" . PHP_EOL;
    }
    echo "task end." . PHP_EOL;
});

$serv->on('Finish', function ($serv, $taskId, $data) {
    echo "finish received data '{$data}'" . PHP_EOL;
});
$serv->start();