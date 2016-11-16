<?php
/**
 * Created by PhpStorm.
 * User: sibenx
 * Date: 16/11/14
 * Time: 上午3:05
 */

function demo(array $phoneList){
    global $shareMemory;
    global $signal;
    $cnt = count($phoneList);  //测试数组大小
    $slice = 3;  //需要调用的进程数量
    $childList = [];

    while($slice >= 0)
    {
        $pid = pcntl_fork();
        if($pid > 0){
            $childList[$pid] = 1;
            //父进程什么都不用做

        }elseif($pid == 0){

            while(true)
            {
                // 标记信号量,这里被我承包了
                sem_acquire($signal);
                //检测共享内存是否存在
                if (shm_has_var($shareMemory,SHARE_KEY)){
                    //从共享内存中拿数据
                    $val = shm_get_var($shareMemory,SHARE_KEY);
                    if($val>=$cnt)
                    {
                        sem_release($signal);
                        break;
                    }else
                    {
                        printf("Slice id:%s,phone:%s \r\n",$slice,$phoneList[$val]);
                        $val ++;
                        //再将数据写入共享内存
                        shm_put_var($shareMemory,SHARE_KEY,$val);
                    }
                }else{
                    // 无值会,先初始化
                    shm_put_var($shareMemory,SHARE_KEY,0);
                }
                // 用完释放
                sem_release($signal);
            }
            exit();
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

const SHARE_KEY = 1;

// 创建一块共享内存
$shm_id = ftok(__FILE__,'a');
$shareMemory = shm_attach($shm_id);

// 创建一个信号量
$sem_id = ftok(__FILE__,'b');
$signal = sem_get($sem_id);

demo(range(0,900));

// 释放共享内存与信号量
shm_remove($shareMemory);
sem_remove($signal);