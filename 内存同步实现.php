<?php

/*
 * 本程序于2018-12-21 10:43:21
 * 由陈浩波编写完成
 * 任何人使用时请保留该声明
 * 这个设想算是完成了,下面的代码只要有apcu就可以运行.
 * 而且还有一个可笑的副作用:儿子比他爹先知道他爹有多少钱[泪目]
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
        apcu_store($apcu_key, $value);
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
//            $this->sync('test');
            echo "我是儿子, 我爹有", $this->test, "元钱!\n";
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
$i = 2;

$pid = pcntl_fork();
//父进程和子进程都会执行下面代码
if ($pid == -1) {
    //错误处理：创建子进程失败时返回-1.
    die('could not fork');
} else if ($pid) {
    //父进程会得到子进程号，所以这里是父进程执行的逻辑
    while ($i--) {
        $test->test = mt_rand(1, 10000);
        echo "我是你爹, 我有", $test->test, "元钱!\n";
        usleep(150);
    }
    posix_kill($pid, 5);
    pcntl_wait($status); //等待子进程中断，防止子进程成为僵尸进程。
} else {
    //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
    while (true) {
        usleep(50);
    }
//    $test1->asynctest();
//        echo "我是儿子, 我爹有", $test->test, "元钱!\n";
}
// */

echo "打完收工\n";
