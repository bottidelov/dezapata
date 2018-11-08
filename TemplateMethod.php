<?php
/*
* 2.TemplateMethod
* 規定クラスにメソッドの雛型を作成、abstractにより継承を保証し、
* サブクラスにて処理の定義および引数渡しを行う
*/
abstract class AbstractArticle
{
    //コンストラクタにて引数の値をプロパティに保持
    public function __construct($data)
    {
        $this->title = $data['title'];
        $this->author = $data['author'];
    }

    //テンプレートメソット
    public function display()
    {
        return "Title:{$this->getTitle()}<br />Author:{$this->getAuthor()}<br />Content:{$this->getContent()}";
        $this->getTitle();
        $this->getAuthor();
        $this->getContent();
    }

    //getterメソッド
    public function getTitle()
    {
        return $this->title;
    }

    //同上
    public function getAuthor()
    {
        return $this->author;
    }

    //処理が定義されていないがabstractが設定されたメソッド
    protected abstract function getContent();
}

//空のabstractメソッドを定義するクラス
class CorporateArticle extends AbstractArticle
{
    protected function getContent()
    {
        return 'This is a Corporate Article. Here write your things.';
    }
}

//data変数をインスタンスに設定し、display関数でプロパティを表示
// ※ CorporateArticleクラス自体には引数が設定されていないが、親のコンストラクタに渡すことができる
$data = [
    "title" => "What is the Template Method?",
    "author" => "Qiita Tarou."
];

$corporate_article = new CorporateArticle($data);

echo $corporate_article->display();
