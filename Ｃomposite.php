<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 12.composite
 * 「複合」の意味
 *  オブジェクトを木構造(ドキュメントツリー状)に構築する
 * クライアント→アクセスクラス(component)→枝(composite)→葉(Leaf)、若しくはクライアント→アクセス→葉と、
 *  ひとつのクラスから各クラスに階段状に子が連なっていく。処理・追加が容易
 */

//コンポーネントクラス
abstract class OrganizationEntry
{
    private $code;
    private $name;

    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    //子要素の追加、抽象クラスで設置し子クラスで自身を追加させる
    public abstract function add(OrganizationEntry $entry);

    //配下の組織を表示
    public function dump()
    {
        echo $this->code . ":" . $this->name . "<br>\n";
    }
}

class Group extends OrganizationEntry
{

    private $entries;

    public function __construct($code, $name)
    {
        parent::__construct($code, $name);
        $this->entries = array();
    }

    /**
     * 子要素を追加する
     */
    public function add(OrganizationEntry $entry)
    {
        array_push($this->entries, $entry);
    }

    /**
     * 組織ツリーを表示する
     * 自分自身と保持している子要素を表示
     */
    public function dump()
    {
        parent::dump();
        foreach ($this->entries as $entry) {
            $entry->dump();
        }
    }
}
