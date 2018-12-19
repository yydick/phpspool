<?php

/* 
 * 一直以来，我都在为PHP进程间，变量共享的问题伤透了脑筋，直到今天灵光一现。
 * 当这个程序真正运行起来，并达到了我的预期的时候，我真的是感慨万千。
 * 一些在我心中酝酿了很多年的想法，就这样实现了第一步，没有依赖C/C++，也没有依赖其他的扩展。
 * 或许，PHP真的可以写一个服务，类似redis，类似mysql，只要有足够的基础，和大胆的想象。
 * 我觉得，这种既是观察者，又是被观察者的模式，可以起一个专有名词，比如：双观察模式？
 * 第三者模式？上帝模式？大家以为呢？
 */

class getset implements SplObserver, SplSubject{
    private $test = 'ss';
    private $observers;
    public  $mykey;


    public function __construct() {
        $this->observers = new SplObjectStorage();
        $this->mykey = uniqid();
    }
    
    public function __get($name) {
        return $this->test;
    }
    public function __set($name, $value) {
        $this->$name = $value;
        $this->notify();
        return $name;
    }
    public function update(SplSubject $subject) {
        $this->test = $subject->test;
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
$test1->attach($test);

$test->test = 'adx';

echo "test: ", $test->test, "\n";
echo "test1: ", $test1->test, "\n";
