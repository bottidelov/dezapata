<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 18.protorype
 * 「原型」の意味
 *  原型となるインスタンスをコピーして新しいインスタンスを生成するためのパターン
 *  cloneキーワード(オブジェクトをコピーする)とは異なるコピー挙動を行う
 * （cloneは参照元に依存する＝浅いコピーに対してこちらは内部参照もコピーする＝深いコピー）
 */

//prototypeクラス
 abstract class ItemPrototype
{
    private $item_code;
    private $item_name;
    private $price;
    private $detail;

    public function __construct($code, $name, $price)
    {
        $this->item_code = $code;
        $this->item_name = $name;
        $this->price = $price;
    }

    public function getCode()
    {
        return $this->item_code;
    }

    public function getName()
    {
        return $this->item_name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setDetail(stdClass $detail)
    {
        $this->detail = $detail;
    }

    public function getDetail()
    {
        return $this->detail;
    }

    public function dumpData()
    {
        echo '<dl>';
        echo '<dt>' . $this->getName() . '</dt>';
        echo '<dd>商品番号：' . $this->getCode() . '</dd>';
        echo '<dd>\\' . number_format($this->getPrice()) . '-</dd>';
        echo '<dd>' . $this->detail->comment . '</dd>';
        echo '</dl>';
    }

    //cloneキーワードを使って新しいインスタンスを作成する
    //浅いコピー
    public function newInstance()
    {
        $new_instance = clone $this;
        return $new_instance;
    }

    /**
     * protectedメソッドにする事で、外部から直接cloneされない
     * ようにしている
     */
    protected abstract function __clone();
}

//具体的実装
class DeepCopyItem extends ItemPrototype
{
    //深いコピーを行うための実装、内部で保持しているオブジェクトもコピー
    protected function __clone()
    {
        $this->setDetail(clone $this->getDetail());
    }
}

class ShallowCopyItem extends ItemPrototype
{
    //浅いコピーを行うので、空の実装を行う
    protected function __clone()
    {
    }
}

//Clientクラスに相当する、このクラスからはConcretePrototypeクラスは見えていない
class ItemManager
{
    private $items;

    public function __construct()
    {
        $this->items = array();
    }

    public function registItem(ItemPrototype $item)
    {
        $this->items[$item->getCode()] = $item;
    }

    //Prototypeクラスのメソッドを使って、新しいインスタンスを作成
    public function create($item_code)
    {
        if (!array_key_exists($item_code, $this->items))
        {
            throw new Exception('item_code [' . $item_code . '] not exists !');
        }
        $cloned_item = $this->items[$item_code]->newInstance();

        return $cloned_item;
    }
}


//以下クライアント
function testCopy(ItemManager $manager, $item_code)
{
    /**
     * 商品のインスタンスを2つ作成
     */
    $item1 = $manager->create($item_code);
    $item2 = $manager->create($item_code);

    /**
     * 1つだけコメントを削除
     */
    $item2->getDetail()->comment = 'コメントを書き換えました';

    /**
     * 商品情報を表示
     * 深いコピーをした場合、$item2への変更は$item1に影響しない
     */
    echo '■オリジナル';
    $item1->dumpData();
    echo '■コピー';
    $item2->dumpData();
    echo '<hr>';
}

$manager = new ItemManager();

/**
 * 商品データを登録
 */
$item = new DeepCopyItem('ABC0001', '限定Ｔシャツ', 3800);
$detail = new stdClass();
$detail->comment = '商品Aのコメントです';
$item->setDetail($detail);
$manager->registItem($item);

$item = new ShallowCopyItem('ABC0002', 'ぬいぐるみ', 1500);
$detail = new stdClass();
$detail->comment = '商品Bのコメントです';
$item->setDetail($detail);
$manager->registItem($item);

testCopy($manager, 'ABC0001');
testCopy($manager, 'ABC0002');
