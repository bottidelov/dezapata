<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 4.ファクトリーメソッド
 * 引数によって実行クラスを選択するクラス・処理が書かれるクラスに分割し実装する
 * 処理ごとに分割することでコードが冗長にならず、また修正時に該当箇所のみ修正すれば良いので楽
 * =取得処理と処理内容をつらつら書く事を防止できる
 */

 //Readerクラスのインスタンス生成および実行処理を分岐させる
 //クライアント側からはこのコードからホストにアクセスすることになる
 class ReaderFactory
 {
     public function create($filename)
     {
         $reader = $this->createReader($filename);
         return $reader;
     }

     //渡されたファイルの拡張子を調べ処理の分岐
     private function createReader($filename)
     {
         $poscsv = stripos($filename, '.csv');
         $posxml = stripos($filename, '.xml');

         if ($poscsv !== false) {
             $r = new CSVFileReader($filename);
             return $r;
         } elseif ($posxml !== false) {
             return new XMLFileReader($filename);
         } else {
             die('This filename is not supported : ' . $filename);
         }
     }
 }

 //読み込み用のメソッドを定義したインターフェース
  interface Reader
  {
      public function read();
      public function display();
  }

//CSVファイルの読み込みを行なうクラス
class CSVFileReader implements Reader
{
    //ファイル名を保持する変数
    //@access private
    private $filename;

    //ハンドラ名(ハンドラ＝自動実行せず待機しリクエストがあった際実行する処理)
    private $handler;

    //コンストラクタ
    public function __construct($filename)
    {
        if (!is_readable($filename)) {
            throw new Exception('file "' . $filename . '" is not readable !');
        }
        $this->filename = $filename;
    }

    //ファイルの読み込み
    public function read()
    {
        $this->handler = fopen ($this->filename, "r");
    }

    //ファイルの表示
    public function display()
    {
        $column = 0;
        $tmp = "";
       while ($data = fgetcsv ($this->handler, 1000, ",")) {
            $num = count ($data);
            for ($c = 0; $c < $num; $c++) {
                if ($c == 0) {
                    if ($column != 0 && $data[$c] != $tmp) {
                        echo "</ul>";
                    }
                    if ($data[$c] != $tmp) {
                        echo "<b>" . $data[$c] . "</b>";
                        echo "<ul>";
                        $tmp = $data[$c];
                    }
                }else {
                    echo "<li>";
                    echo $data[$c];
                    echo "</li>";
                }
            }
            $column++;
        }
        echo "</ul>";
        fclose ($this->handler);
    }
}

//XMLファイルの読み込み・表示
class XMLFileReader implements Reader
{
    /**
     * 内容を表示するファイル名
     *
     * @access private
     */
    private $filename;

    /**
     * データを扱うハンドラ名
     *
     * @access private
     */
    private $handler;

    /**
     * コンストラクタ
     *
     * @param string ファイル名
     * @throws Exception
     */
    public function __construct($filename)
    {
        if (!is_readable($filename)) {
            throw new Exception('file "' . $filename . '" is not readable !');
        }
        $this->filename = $filename;
    }

    /**
     * 読み込みを行ないます
     */
    public function read()
    {
        $this->handler = simplexml_load_file($this->filename);
    }

    /**
     * 文字コードの変換を行います
     */
    private function convert($str)
    {
        return mb_convert_encoding($str, mb_internal_encoding(), "auto");
    }

    /**
     * 表示を行ないます
     */
    public function display()
    {
        foreach ($this->handler->artist as $artist) {
            echo "<b>" . $this->convert($artist['name']) . "</b>";
            echo "<ul>";
            foreach ($artist->music as $music) {
                echo "<li>";
                echo $this->convert($music['name']);
                echo "</li>";
            }
            echo "</ul>";
        }
    }
}

/************************/

//クライアント側
$filename = 'Music.xml';

$factory = new ReaderFactory();
$data = $factory->create($filename);
$data->read();
$data->display();
