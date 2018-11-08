<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 5.ファサード
 * フランス語で窓口
 * クライアントからファサードクラスにアクセスし、そこで各子クラスにアクセスし処理する
 * クライアント側からはファサードクラスを呼び出すだけで良いので単純に使える
 * また複数のクラスをファサードで一元管理するので、ひとつのコントローラーが肥大化する事を防げる
 */

//ファサードクラス、クライアントからはこのクラスのメソッドにアクセスするのみ
class Facade
{
    private $test1;
    private $test2;
    private $test3;

    public function __construct()
    {
        $this->test1 = new Test1();
        $this->test2 = new Test2();
        $this->test3 = new Test3();
    }

    //メソッドチェーン
    public function run()
    {
        $this->test1->doSomething();
        $this->test2->doSomething();
        $this->test3->doSomething();
    }
}

//各配下クラス
class Test1
{
    public function doSomething()
    {
        echo 'test1';
    }
}

class Test2
{
    public function doSomething()
    {
        echo 'test2';
    }
}

class Test3
{
    public function doSomething()
    {
        echo 'test3';
    }
}

//クライアント
class Client
{
    private $facade;

    public function __construct()
    {
        $this->facade = new Facade();
    }

    public function run()
    {
        $this->facade->run();
    }
}
