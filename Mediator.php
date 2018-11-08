<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 16.Mediator
 * 「仲介者」の意味
 * 各クラスごとの複雑なやりとりを一つのクラスに集約する構造
 * 仲介クラスで処理の命令を一元管理することができる
 */

 class User
{
    private $chatroom;
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setChatroom(Chatroom $value)
    {
         $this->chatroom = $value;
    }
    public function getChatroom()
    {
        return $this->chatroom;
    }
    public function sendMessage($to, $message)
    {
        $this->chatroom->sendMessage($this->name, $to, $message);
    }
    public function receiveMessage($from, $message)
    {
        printf('<font color="008800">%sさんから%sさんへ</font>： %s<hr>', $from, $this->getName(), $message);
    }
}

//Mediatorクラス
class Chatroom
{
    private $users = array();
    public function login(User $user)
    {
        $user->setChatroom($this);
        if (!array_key_exists($user->getName(), $this->users))
        {
            $this->users[$user->getName()] = $user;
            printf('<font color="#0000dd">%sさんが入室しました</font><hr>', $user->getName());
        }
    }
    public function sendMessage($from, $to, $message)
    {
        if (array_key_exists($to, $this->users))
        {
            $this->users[$to]->receiveMessage($from, $message);
        } else {
            printf('<font color="#dd0000">%sさんは入室していないようです</font><hr>', $to);
        }
    }
}

//以下クライアント
$chatroom = new Chatroom();

$sasaki = new User('佐々木');
$suzuki = new User('鈴木');
$yoshida = new User('吉田');
$kawamura = new User('川村');
$tajima = new User('田島');

$chatroom->login($sasaki);
$chatroom->login($suzuki);
$chatroom->login($yoshida);
$chatroom->login($kawamura);
$chatroom->login($tajima);

$sasaki->sendMessage('鈴木', '来週の予定は？') ;
$suzuki->sendMessage('川村', '秘密です') ;
$yoshida->sendMessage('萩原', '元気ですか？') ;
$tajima->sendMessage('佐々木', 'お邪魔してます') ;
$kawamura->sendMessage('吉田', '私事で恐縮ですが…') ;
