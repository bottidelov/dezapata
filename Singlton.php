<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 1.シングルトン
 * インスタンス生成を一度のみに保証する
 * static変数にインスタンスを保持し、この値の有無によりインスタンスを返すか否か判定
 */
class Singleton
{
    private static $singleton;

    private function __construct()
    {
        echo "singletonパターン";
    }

    public function getSingleton()
    {
        if(!isset($singleton))
        {
            $this->singleton = new Singleton();
        }
        return $singleton;
    }
}
