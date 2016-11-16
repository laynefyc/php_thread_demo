<?php
function demo(array $phoneList){
    $cnt = count($phoneList);  //测试数组大小
    $slice = 30;  //需要调用的进程数量
    $master = array_chunk($phoneList,floor($cnt/$slice));
    $childList = [];

    while($slice >= 0)
    {
        $pid = pcntl_fork();
        if($pid > 0){
            $childList[$pid] = 1;
            //$pid>0表示当前还在执行父进程的代码
            //这里最好啥都不做，每次执行pcntl_fork都会执行这里的代码。
            //这里的代码执行完之后 会将$pid设置为0，然后jump到pcntl_fork代码之后，重新做判断；
        }elseif($pid == 0){
            //这里写我们的逻辑
            foreach($master[$slice] as $val)
            {
                //这里发生短信
                printf("Slice id:%s,phone:%s \r\n",$slice,$val);
            }
            //子进程执行完之后务必需要关闭;
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

demo(range(0,100000));