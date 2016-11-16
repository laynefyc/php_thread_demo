<?php
/**
 * Created by PhpStorm.
 * User: sibenx
 * Date: 16/11/16
 * Time: 上午2:40
 */

function demo(array $phoneList){

    $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
    $cnt = count($phoneList);  //测试数组大小
    $slice = 3;  //需要调用的进程数量
    $childList = [];
    $i = 0;
    while($slice >= 0)
    {
        $pid = pcntl_fork();
        if($pid > 0){
            $childList[$pid] = 1;
            if($slice == 0)
            {
                while(true)
                {
                    if(is_resource($sockets[1]))
                    {
                        $i++;
                        if($i >= $cnt){
                            fwrite($sockets[1], "c\n");
                            fclose($sockets[1]);
                            exit();
                        }else
                        {
                            fwrite($sockets[1], "{$i}\n");
                        }
                    }
                }
            }
        }elseif($pid == 0){
            while(true)
            {
                if(is_resource($sockets[0]))
                {
                    $tmp = fgets($sockets[0]);
                    if(trim($tmp) == 'c')
                    {
                        echo "child-die\r\n";
                        fclose($sockets[0]);
                        exit();
                    }else
                    {
                        //这里发短信
                        printf("Slice id:%s,phone:%s \r\n",$slice,$phoneList[trim($tmp)]);
                    }
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


demo(range(0,3));