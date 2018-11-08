<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 7.AbstractFactry
 *「抽象的な工場」
 * FactoryMethodと似た構造となるが、処理の分岐が個別処理クラスに移譲されている
 * （渡されたデータによって処理がクラス側で分岐する）
 * これによりクライアント側のコールは全て同一でよく、ホスト側を気にする必要がない＝強く隠蔽される
 * またDBがまだ設置されていない状況でのコード作成時に使用できる
 */

 // データベースへの接続と切断を定義する抽象クラス
 abstract class Database_Abstract
 {
     protected $_connection;

     // データベースへの接続
     abstract public function openConnection();

     // データベースから切断
     abstract public function closeConnection();
 }

//オラクルDBへのDAO
 class Oracle_Database extends Database_Abstract
 {
     static public function CreateFactory()
     {
         return new Oracle_Database();
     }

     public function openConnection()
     {
        // oracle データベースに接続する処理
        $this->_connection = $oci_connect('パラメータ', ・・・);
     }

     public function closeConnection()
     {
         oci_close($this->_connection);
         $this->_connection = null;
     }
 }

//MysqlDBへのDAO
 class Mysql_Database extends Database_Abstract
{
    static public function CreateFactory()
    {
        return new Mysql_Database();
    }

    public function openConnection()
    {
       // Mysql データベースに接続する処理
       $this->_connection = mysqli_init();
       mysqli_real_connect('パラメータ', ・・・);
    }

    public function closeConnection()
    {
        $this->_connection-&gt;close();
        $this->_connection = null;
    }
}

/*
 * 以下クライアント
 * データベースの違いを意識することはない
 */
 if($db == 'oracle')
 {
     // oracle への接続と切断
     $factory = Oracle_Database::CreateFactory();
 }else if($db == 'mysql')
 {
     // Mysql への接続と切断
     $factory = Mysql_Database::CreateFactory();
 }

// データベースへ接続
$factory->openConnection();

// データベースから切断
$factory->closeConnection();
