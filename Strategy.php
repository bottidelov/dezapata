<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 22.Strategy
 * 「戦術」の意味
 * アルゴリズムをクラスとして定義し、切り替えられるようにする構造
 * 異なる処理を選択するための条件文がなくなり簡潔に書くことができる
 */

//stratrgyクラス
 abstract class ReadItemDataStrategy
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function getData()
    {
        if (!is_readable($this->getFilename()))
        {
            throw new Exception('file [' . $this->getFilename() . '] is not readable !');
        }

        return $this->readData($this->getFilename());
    }

    public function getFilename()
    {
        return $this->filename;
    }

    //委譲
    protected abstract function readData($filename);
}

//固定長データを読み込む
class ReadFixedLengthDataStrategy extends ReadItemDataStrategy
{
    //データを取得しオブジェクトで返す
    protected function readData($filename)
    {
        $fp = fopen($filename, 'r');

        $dummy = fgets($fp, 4096);

        $return_value = array();
        while ($buffer = fgets($fp, 4096))
        {
            $item_name = trim(substr($buffer, 0, 20));
            $item_code = trim(substr($buffer, 20, 10));
            $price = (int)substr($buffer, 30, 8);
            $release_date = substr($buffer, 38);

            $obj = new stdClass();
            $obj->item_name = $item_name;
            $obj->item_code = $item_code;
            $obj->price = $price;

            $obj->release_date = mktime(0, 0, 0,
                                        substr($release_date, 4, 2),
                                        substr($release_date, 6, 2),
                                        substr($release_date, 0, 4));

            $return_value[] = $obj;
        }

        fclose($fp);

        return $return_value;
    }
}

//タブ区切りデータを読み込む
class ReadTabSeparatedDataStrategy extends ReadItemDataStrategy
{

    protected function readData($filename)
    {
        $fp = fopen($filename, 'r');

        $dummy = fgets($fp, 4096);

        $return_value = array();
        while ($buffer = fgets($fp, 4096))
        {
            list($item_code, $item_name, $price, $release_date) = split("\t", $buffer);

            $obj = new stdClass();
            $obj->item_name = $item_name;
            $obj->item_code = $item_code;
            $obj->price = $price;

            list($year, $mon, $day) = split('/', $release_date);
            $obj->release_date = mktime(0, 0, 0,
                                        $mon,
                                        $day,
                                        $year);

            $return_value[] = $obj;
        }

        fclose($fp);

        return $return_value;
    }
}

//contextクラス
class ItemDataContext
{
    private $strategy;

    //ReadItemDataStrategyおよびReadItemDataStrategyオブジェクトが渡される事を期待している
    public function __construct(ReadItemDataStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getItemData()
    {
        return $this->strategy->getData();
    }

}


//以下クライアント
function dumpData($data)
{
    echo '<dl>';
    foreach ($data as $object) {
        echo '<dt>' . $object->item_name . '</dt>';
        echo '<dd>商品番号：' . $object->item_code . '</dd>';
        echo '<dd>\\' . number_format($object->price) . '-</dd>';
        echo '<dd>' . date('Y/m/d', $object->release_date) . '発売</dd>';
    }
    echo '</dl>';
}

//固定長データを読みこむ
$strategy1 = new ReadFixedLengthDataStrategy('fixed_length_data.txt');
$context1 = new ItemDataContext($strategy1);
dumpData($context1->getItemData());

echo '<hr>';

//タブ区切りデータを読み込む
$strategy2 = new ReadTabSeparatedDataStrategy('tab_separated_data.txt');
$context2 = new ItemDataContext($strategy2);
dumpData($context2->getItemData());
