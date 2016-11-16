<?php
/**
 * Created by PhpStorm.
 * User: sibenx
 * Date: 16/11/15
 * Time: 上午1:45
 */

function demo(array $phoneList){
    // 定义管道路径,与创建管道
    $pipe_path = '/tmp/test1.pipe';
    if(!file_exists($pipe_path)){
        if(!posix_mkfifo($pipe_path,0664)){
            exit("create pipe error!");
        }
    }

    $cnt = count($phoneList);  //测试数组大小
    $slice = 0;  //需要调用的进程数量
    $childList = [];

    while($slice >= 0)
    {
        $pid = pcntl_fork();
        if($pid > 0){
            $childList[$pid] = 1;
            //向管道写数据
            $file = fopen($pipe_path,'w');
            foreach($phoneList as $val)
            {
                fwrite($file,"{$val}\r");
            }
        }elseif($pid == 0){
            // 父进程,从管道读数据
            $file = fopen($pipe_path,'r');
            while (true){
                echo 123;
                $val = fgets($file);
                echo $val."\r\n";
                if(empty($val)){
                    break;
                }else{
                    printf("Slice id:%s,phone:%s \r\n",$slice,$val);
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

demo(range(0,900));