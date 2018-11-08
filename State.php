<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 20.State
 * 「状態」の意味
 * 各クラスを状態ごとに定義し、「状態」と「状態による振る舞い」を1つのクラスにまとめる
 * 状態ごとの処理がクラス単位にまとめて実装されるので、
 * if文やswitch文を使うことがなくなり、非常に簡潔なコードとなる。
 */

 class User
{
    private $name;
    private $state;
    private $count = 0;

    public function __construct($name)
    {
        $this->name = $name;

        // 初期値の取得、未承認のインスタンスを取得
        $this->state = UnauthorizedState::getInstance();
        $this->resetCount();
    }

    //状態を切り替える
    public function switchState()
    {
        //メンバに格納されたインスタンスのクラス名を取得、stateを更新
        echo "状態遷移:" . get_class($this->state) . "→";
        $this->state = $this->state->nextState();
        echo get_class($this->state) . "<br>";
        $this->resetCount();
    }

    public function isAuthenticated()
    {
        return $this->state->isAuthenticated();
    }

    public function getMenu()
    {
        return $this->state->getMenu();
    }

    public function getUserName()
    {
        return $this->name;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function incrementCount()
    {
        $this->count++;
    }

    public function resetCount()
    {
        $this->count = 0;
    }
}

//stateに関するメソッド
interface UserState
{
    public function isAuthenticated();
    public function nextState();
    public function getMenu();
}

//認証前の状態で使用するメソッド
class AuthorizedState implements UserState
{

    private static $singleton = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$singleton == null)
        {
            self::$singleton = new AuthorizedState();
        }
        return self::$singleton;
    }

    public function isAuthenticated()
    {
        return true;
    }

    public function nextState()
    {
        // 次の状態（未認証）を返す
        return UnauthorizedState::getInstance();
    }

    public function getMenu()
    {
        $menu = '<a href="?mode=inc">カウントアップ</a> | '
              .    '<a href="?mode=reset">リセット</a> | '
              .    '<a href="?mode=state">ログアウト</a>';
        return $menu;
    }

    //複製の否定
    public final function __clone() {
        throw new RuntimeException ('Clone is not allowed against ' . get_class($this));
    }
}

//認証後に使用するメソッド
class UnauthorizedState implements UserState
{
    private static $singleton = null;

    private function __construct()
    {
    }

    //外部から使用するために静的参照を設定
    public static function getInstance()
    {
        if (self::$singleton === null)
        {
            self::$singleton = new UnauthorizedState();
        }
        return self::$singleton;
    }

    public function isAuthenticated()
    {
        return false;
    }

    public function nextState()
    {
        // 次の状態（認証済み）を返し、
        return AuthorizedState::getInstance();
    }

    public function getMenu()
    {
        $menu = '<a href="?mode=state">ログイン</a>';
        return $menu;
    }

    //複製の禁止
    public final function __clone()
    {
        throw new RuntimeException ('Clone is not allowed against ' . get_class($this));
    }
}


//以下クライアント
session_start();

$context = isset($_SESSION['context']) ? $_SESSION['context'] : null;
if (is_null($context))
{
    $context = new User('hogehoge');
}

$mode = (isset($_GET['mode']) ? $_GET['mode'] : '');
switch ($mode)
{
    case 'state':
        echo '<p style="color: #aa0000">状態を遷移します</p>';
        $context->switchState();
        break;
    case 'inc':
        echo '<p style="color: #008800">カウントアップします</p>';
        $context->incrementCount();
        break;
    case 'reset':
        echo '<p style="color: #008800">カウントをリセットします</p>';
        $context->resetCount();
        break;
}

//コンテキストをセッションに保存
$_SESSION['context'] = $context;

echo 'ようこそ、' . $context->getUserName() . 'さん<br>';
echo '現在、ログインして' . ($context->isAuthenticated() ? 'います' : 'いません') . '<br>';
echo '現在のカウント：' . $context->getCount() . '<br>';
echo $context->getMenu() . '<br>';
