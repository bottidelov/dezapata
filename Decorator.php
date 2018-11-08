<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 13.Decorator
 * 「装飾者」
 * 具体的実装を行うクラスに一つづつ機能追加の子クラスを追加していく構造（継承でなく委譲を行う）
 *  追加修正・管理がしやすく、上層に具体的実装を行わずに済む
 */

 interface Text
 {
     public function getText();
     public function setText($str);
 }

//プレーンテキストを返すコンポーネントクラス
class PlainText implements Text
{
    private $textString = null;

    public function getText()
    {
        return $this->textString;
    }

    public function setText($str)
    {
        $this->textString = $str;
    }
}

//Decoratorクラス、抽象クラスであり委譲先の子クラスで具体的実装を行う
abstract class TextDecorator implements Text
{
    private $text;

    public function __construct(Text $target)
    {
        $this->text = $target;
    }

    public function getText()
    {
        return $this->text->getText();
    }

    public function setText($str)
    {
        $this->text->setText($str);
    }
}

//以下子クラス
class UpperCaseText extends TextDecorator
{
    public function __construct(Text $target)
    {
        parent::__construct($target);
    }

    public function getText()
    {
        $str = parent::getText();
        $str = mb_strtoupper($str);
        return $str;
    }
}

class DoubleByteText extends TextDecorator
{
    public function __construct(Text $target)
    {
        parent::__construct($target);
    }

    public function getText()
    {
        $str = parent::getText();
        $str = mb_convert_kana($str,"RANSKV");
        return $str;
    }
}


//以下クライアント
$text = (isset($_POST['text'])? $_POST['text'] : '');
$decorate = (isset($_POST['decorate'])? $_POST['decorate'] : array());
if ($text !== '')
{
    $text_object = new PlainText();
    $text_object->setText($text);

    foreach ($decorate as $val)
    {
        switch ($val)
        {
            case 'double':
                $text_object = new DoubleByteText($text_object);
                break;
            case 'upper':
                $text_object = new UpperCaseText($text_object);
                break;
            default:
                throw new RuntimeException('invalid decorator');
        }
    }
    echo htmlspecialchars($text_object->getText(), ENT_QUOTES, mb_internal_encoding()) . "<br>";
}
