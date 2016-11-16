<?php

function demo(array $phoneList){
    global $msgQueue;
    $cnt = count($phoneList);  //测试数组大小
    $slice = 3;  //需要调用的进程数量
    $childList = [];
    //主进程先发送一条消息，告诉子进程可以发送第一条短信了
    msg_send($msgQueue,MSG_TYPE,0);

    while($slice >= 0)
    {
        $pid = pcntl_fork();
        if($pid > 0){
            $childList[$pid] = 1;
            //父进程什么都不用做

        }elseif($pid == 0){
            //子进程不停的请求，直到所有短信发送完成
            while(msg_receive($msgQueue,MSG_TYPE,$msgType,1024,$message))
            {
                if($cnt>intval($message))
                {
                    printf("Slice id:%s,phone:%s \r\n",$slice,$phoneList[$message]);
                    $message = $message + 1;
                    msg_send($msgQueue,MSG_TYPE,$message);
                }else
                {
                    //通知其他进程一切都结束了
                    msg_send($msgQueue,MSG_TYPE,$cnt);
                    exit();
                }
            }
        }else
        {
            //程序发生错误也需要关闭程序
            exit();
        }
        $slice--;
    }

    // 等待所有子进程结束后回收资源
    while(!empty($childList)){
        $childPid = pcntl_wait($status);
        if ($childPid > 0){
            unset($childList[$childPid]);
        }
    }
}

const MSG_TYPE = 1;
//创建消息队列
$id = ftok(__FILE__,'m');
$msgQueue = msg_get_queue($id);

demo(range(0,900));