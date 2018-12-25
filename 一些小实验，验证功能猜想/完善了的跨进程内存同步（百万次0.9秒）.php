<?php

/*
 * 本程序于2018-12-26 01:43:21
 * 由陈浩波编写完成
 * 任何人使用时请保留该声明
 * 这个测试算是基本完善了，可以做些复杂一点儿的开发了。
 * 下一个测试将给予这个模式
 * 关于性能，在关闭了交互输出后，百万次运行的速度为
 * real    0m0.883s
 * user    0m0.861s
 * sys     0m0.021s
 * 没有跟C、JAVA比较，不过自我感觉基本够用了
 */

class getset implements SplObserver, SplSubject {

    private $test;
    private $observers;
    public $mykey;

    public function __construct() {
        $this->observers = new SplObjectStorage();
        $this->mykey = uniqid();
    }

    public function __get($name) {
        if (!isset($this->$name)) {
            throw new Exception;
        }
        $this->sync($name);
        return $this->$name;
    }

    public function __set($name, $value) {
        $apcu_key = __FILE__ . __CLASS__ . $name;
        $this->$name = $value;
        if (!is_resource($value)) {
            apcu_store($apcu_key, $value);
        }
        $this->notify();
        return $name;
    }

    private function sync($name) {
        $apcu_key = __FILE__ . __CLASS__ . $name;
        if (apcu_exists($apcu_key)) {
            $this->$name = apcu_fetch($apcu_key);
        }
    }

    public function update(SplSubject $subject) {
        $this->test = $subject->test;
        //echo "我是儿子, 我爹有", $this->test, "元钱!\n";
    }

    public function attach(\SplObserver $observer): void {
        $this->observers->attach($observer);
    }

    public function detach(\SplObserver $observer): void {
        $this->observers->detach($object);
    }

    public function notify(): void {
        foreach ($this->observers as $observer) {
            if ($observer->mykey != $this->mykey) {
                $observer->update($this);
            }
        }
    }

}

$test = new getset();
$test1 = new getset();
$test->attach($test1);
$i = 1000000;

$pid = pcntl_fork();
//父进程和子进程都会执行下面代码
if ($pid == -1) {
    //错误处理：创建子进程失败时返回-1.
    die('could not fork');
} else if ($pid) {
    //父进程会得到子进程号，所以这里是父进程执行的逻辑
    echo "fpid = $pid\n";
    while ($i--) {
        $test->test = mt_rand(1, 10000);
        //echo "我是你爹, 我有", $test->test, "元钱!\n";
    }

    if (isset($test->fp) && $test->fp) {
        fclose($test->fp);
    }
    posix_kill($pid, SIGHUP);
    usleep(5);
    $return = posix_kill($pid, SIGTERM);
    echo "return: $return 等他结束\n";
    pcntl_wait($status);
//    pcntl_wait($status, WNOHANG);
    echo "打完收工\n";
} else {
    //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
    while (true) {
        $sigin = pcntl_sigtimedwait([SIGHUP], $info, 1, 0);
        pcntl_signal_dispatch();
        if ($sigin) {
            echo "我退出!\n";
            break;
        }
    }
}
// */

