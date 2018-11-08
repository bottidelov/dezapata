<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ９.Build
 * 構築
 * 処理を「材料」(builder)、「出力」(directer)に分け、前者を後者にて利用する形の構造
 * 上記の結びつきは弱いので処理自体の修正・入れ替えが可能
 */

//「出力に」に相当するクラス
 class NewsDirector
 {
     private $builder;
     private $url;

     public function __construct(NewsBuilder $builder, $url)
     {
         $this->builder = $builder;
         $this->url = $url;
     }

     public function getNews()
     {
         $news_list = $this->builder->parse($this->url);
         return $news_list;
     }
 }

 // 「材料」、Builderクラスのインターフェース
 interface NewsBuilder
 {
     public function parse($data);
 }

//「材料」クラス
class RssNewsBuilder implements NewsBuilder
{
    public function parse($url)
    {
        $data = simplexml_load_file($url);
        if ($data === false) {
            throw new Exception('read data [' .
                                htmlspecialchars($url, ENT_QUOTES, mb_internal_encoding())
                                . '] failed !');
        }

        $list = array();
        foreach ($data->item as $item) {
            $dc = $item->children('http://purl.org/dc/elements/1.1/');
            //Newsクラスを使用
            $list[] = new News($item->title,
                               $item->link,
                               $dc->date);
        }
        return $list;
    }
}

//処理で使用する独立クラス
 class News
 {
     private $title;
     private $url;
     private $target_date;

     public function __construct($title, $url, $target_date)
     {
         $this->title = $title;
         $this->url = $url;
         $this->target_date = $target_date;
     }

     public function getTitle()
     {
         return $this->title;
     }

     public function getUrl()
     {
         return $this->url;
     }

     public function getDate()
     {
         return $this->target_date;
     }
 }


//以下クライアント
$builder = new RssNewsBuilder();
$url = 'http://www.php.net/news.rss';

$director = new NewsDirector($builder, $url);
foreach ($director->getNews() as $article) {
    printf('<li>[%s] <a href="%s">%s</a></li>',
           $article->getDate(),
           $article->getUrl(),
           htmlspecialchars($article->getTitle(), ENT_QUOTES, mb_internal_encoding())
    );
}
