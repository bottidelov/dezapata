<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 14.FlyWeight
 * 「軽量」
 *  ひとつのインスタンスを使い回すための構造
 *  インスタンス生成を少なくできるためメモリの節約ができるが、変化しないクラス（=）を共有する必要がある
 */

 class Item
{

    private $code;
    private $name;
    private $price;

    public function __construct($code, $name, $price)
    {
        $this->code = $code;
        $this->name = $name;
        $this->price = $price;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }
}

//FlyweightFactoryに相当、またSingletonパターンにもなっている
class ItemFactory
{
    private $pool;
    private static $instance = null;

    private function __construct($filename)
    {
        $this->buildPool($filename);
    }

    //Factoryのインスタンスを返す
    public static function getInstance($filename)
    {
        if (is_null(self::$instance))
        {
            self::$instance = new ItemFactory($filename);
        }
        return self::$instance;
    }

    //ConcreteFlyweightを返す
    public function getItem($code)
    {
        if (array_key_exists($code, $this->pool)) {
            return $this->pool[$code];
        } else {
            return null;
        }
    }

    //データを読み込み、プールを初期化する
    private function buildPool($filename)
    {
        $this->pool = array();

        $fp = fopen($filename, 'r');
        while ($buffer = fgets($fp, 4096)) {
            list($item_code, $item_name, $price) = split("\t", $buffer);
            $this->pool[$item_code] = new Item($item_code, $item_name, $price);
        }
        fclose($fp);
    }

    //このインスタンスの複製を許可しないようにする
    public final function __clone()
    {
        throw new RuntimeException ('Clone is not allowed against ' . get_class($this));
    }
}


//以下クライアント
function dumpData($data) {
    echo '<dl>';
    foreach ($data as $object) {
        echo '<dt>' . htmlspecialchars($object->getName(), ENT_QUOTES, mb_internal_encoding()) . '</dt>';
        echo '<dd>商品番号：' . $object->getCode() . '</dd>';
        echo '<dd>\\' . number_format($object->getPrice()) . '-</dd>';
    }
    echo '</dl>';
}

$factory = ItemFactory::getInstance('data.txt');

/**
 * データを取得する
 */
$items = array();
$items[] = $factory->getItem('ABC0001');
$items[] = $factory->getItem('ABC0002');
$items[] = $factory->getItem('ABC0003');

if ($items[0] === $factory->getItem('ABC0001')) {
    echo '同一のオブジェクトです';
} else {
    echo '同一のオブジェクトではありません';
}

dumpData($items);
