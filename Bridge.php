<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 8.Bridge
 * インターフェースを「機能」・個別処理を「実装」に大別、それぞれ継承により拡張する
 * 縦関係はtemplateMethodと同一だが、横関係にすることでより追加・修正処理しやすくしている
 * 横関係、「機能」→「実装」での繋がりからbridgeと呼ばれる
 */

//「実装」のインターフェース
 interface DataSource
{
    public function open();
    public function read();
    public function close();
}

//「実装」クラス
class FileDataSource implements DataSource
{
    //ソース名
    private $source_name;

    //ハンドラ
    private $handler;

    //コンストラクタ @return String
    function __construct($source_name) {
        $this->source_name = $source_name;
    }

    //データソースを開く　@throws Exception
    function open()
    {
        if (!is_readable($this->source_name))
        {
            throw new Exception('データソースが見つかりません');
        }
        $this->handler = fopen($this->source_name, 'r');
        if (!$this->handler)
        {
            throw new Exception('データソースのオープンに失敗しました');
        }
    }

    //読み込み
    function read()
    {
        $buffer = array();
        while (!feof($this->handler)) {
            $buffer[] = fgets($this->handler);
        }
        return join($buffer);
    }

    //データソースを閉じる
    function close()
    {
        if (!is_null($this->handler)) {
            fclose($this->handler);
        }
    }
}

//「機能」クラス、引数に「実装」クラスを代入し使用
// メソッド名はわかりやすいよう意図的に「実装」と同じにしている
class Listing
{
    private $data_source;

    //コンストラクタ
    function __construct($data_source)
    {
        $this->data_source = $data_source;
    }

    //データソースを開く
    function open()
    {
        $this->data_source->open();
    }

    //データソース取得
    function read()
    {
        return $this->data_source->read();
    }

    //データソースを閉じる
    function close()
    {
        $this->data_source->close();
    }
}

//「機能」クラスの拡張
class ExtendedListing extends Listing
{
    //コンストラクタ
    function __construct($data_source)
    {
        parent::__construct($data_source);
    }

    //エンコード出力
    function readWithEncode()
    {
        return htmlspecialchars($this->read(), ENT_QUOTES, mb_internal_encoding());
    }
}

//以下クライアント　data.txtは一行のテキストのみ
$list1 = new Listing(new FileDataSource('data.txt'));
$list2 = new ExtendedListing(new FileDataSource('data.txt'));

try {
    $list1->open();
    $list2->open();
}
catch (Exception $e)
{
    die($e->getMessage());
}

//データ表示
$data = $list1->read();
echo $data;

$data = $list2->readWithEncode();
echo $data;

$list1->close();
$list2->close();
