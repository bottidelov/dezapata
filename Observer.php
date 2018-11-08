<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 17.Observer
 * 「観察者」の意味
 *  観察対象のオブジェクトに変更があった際、通知を各オブジェクトに行う
 *  変化をの通知機能をobserverクラスに集約させることでシンプルにすることができる
 */

//監視対象になるクラス
class Cart
{
    private $items;
    private $listeners;

    public function __construct()
    {
        $this->items = array();
        $this->listeners = array();
    }

    public function addItem($item_cd)
    {
        $this->items[$item_cd] = (isset($this->items[$item_cd]) ? ++$this->items[$item_cd] : 1);
        $this->notify();
    }

    public function removeItem($item_cd)
    {
        $this->items[$item_cd] = (isset($this->items[$item_cd]) ? --$this->items[$item_cd] : 0);
        if ($this->items[$item_cd] <= 0)
        {
            unset($this->items[$item_cd]);
        }
        $this->notify();
    }

    public function getItems()
    {
        return $this->items;
    }

    public function hasItem($item_cd)
    {
        return array_key_exists($item_cd, $this->items);
    }

    /**
     * Observerクラスを登録するメソッド
     */
    public function addListener(CartListener $listener)
    {
        $this->listeners[get_class($listener)] = $listener;
    }

    /**
     * Observerクラスを削除するメソッド
     */
    public function removeListener(CartListner $listener)
    {
        unset($this->listeners[get_class($listener)]);
    }

    /**
     * Observerへ通知するメソッド
     */
    public function notify()
    {
        foreach ($this->listeners as $listener)
        {
            $listener->update($this);
        }
    }
}

//以下Observerクラス
interface CartListener
{
    public function update(Cart $cart);
}

class PresentListener implements CartListener
{
    private static $PRESENT_TARGET_ITEM = '30:クッキーセット';
    private static $PRESENT_ITEM = '99:プレゼント';

    public function __construct()
    {
    }

    public function update(Cart $cart)
    {
        //ターゲットアイテムがカートに存在する際、プレゼントアイテムを自動追加する処理
        if ($cart->hasItem(self::$PRESENT_TARGET_ITEM) &&
            !$cart->hasItem(self::$PRESENT_ITEM))
        {
            $cart->addItem(self::$PRESENT_ITEM);
        }

        if (!$cart->hasItem(self::$PRESENT_TARGET_ITEM) &&
            $cart->hasItem(self::$PRESENT_ITEM))
        {
            $cart->removeItem(self::$PRESENT_ITEM);
        }
    }
}

class LoggingListener implements CartListener
{
    public function __construct()
    {
    }

    //カートの中身を一覧に出力
    public function update(Cart $cart)
    {
        echo '<pre>';
        var_dump($cart->getItems());
        echo '</pre>';
    }
}


//以下クライアント
function createCart()
{
    $cart = new Cart();
    $cart->addListener(new PresentListener());
    $cart->addListener(new LoggingListener());

    return $cart;
}

session_start();

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : null;
if (is_null($cart))
{
    $cart = createCart();
}

$item = (isset($_POST['item']) ? $_POST['item'] : '');
$mode = (isset($_POST['mode']) ? $_POST['mode'] : '');
switch ($mode)
{
    case 'add':
        echo '<p style="color: #aa0000">追加しました</p>';
        $cart->addItem($item);
        break;
    case 'remove':
        echo '<p style="color: #008800">削除しました</p>';
        $cart->removeItem($item);
        break;
    case 'clear':
        echo '<p style="color: #008800">クリアしました</p>';
        $cart = createCart();
        break;
}

$_SESSION['cart'] = $cart;

echo '<h1>商品一覧</h1>';
echo '<ul>';
foreach ($cart->getItems() as $item_name => $quantity)
{
    echo '<li>' . $item_name . ' ' . $quantity . '個</li>';
}

<form action="" method="post">
<select name="item">
<option value="10:Tシャツ">Tシャツ</option>
<option value="20:ぬいぐるみ">ぬいぐるみ</option>
<option value="30:クッキーセット">クッキーセット</option>
</select>
<input type="submit" name="mode" value="add">
<input type="submit" name="mode" value="remove">
<input type="submit" name="mode" value="clear">
</form>
