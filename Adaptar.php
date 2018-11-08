<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 3.Adaptar
 * 基底クラスと子クラスの間にラッパークラス(インターフェースにより実装)を挟むことにより、
 * 既存コードに影響を与えず、別関数として同機能を実装することができる
 * 機能追加・修正する際も子クラスを修正するだけで良い
 */
 class ShowData
 {
     private $data;

     public function __construct($data)
     {
         $this->data = $data;
     }

     public function showOriginalData()
     {
         echo $this->data;
     }

     //この関数のコピーを以後利用していく流れ
     public function showProcessedData()
     {
         echo $this->data . 'How are you?';
     }
 }

//ラッパークラス
 interface ShowSourceData
 {
    public function show();
 }

//子クラス
class ShowSourceDataImpl extends ShowData implements ShowSourceData
{
    //親クラスに引数を送る
    public function __construct($data)
    {
        parent::__construct($data);
    }

    //親クラスのshowProcessedDataを別名で利用
    public function show()
    {
        parent::showProcessedData();
    }
}

$show_data = new ShowSourceDataImpl('Hello! Mr. Data.');
$show_data->show();




//別パターン(移譲)
//基底クラスを継承せず、クラス内でインスタンスを取得し直接実装している
class ShowSourceDataImpl implements ShowSourceData
{
    private $show_data;

    public function __construct($data) {
        $this->show_data = new ShowData($data);
    }

    public function show() {
        $this->show_data->showProcessedData();
    }
}
