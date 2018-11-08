<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 17.Memento
 * 「記憶」の意味、snapshotとも言われる
 * クラス内部に状態を保持し、呼び出すことでアンドゥを実現する構造
 */

//情報を保存しておくためのクラス
 class DataSnapshot
 {
     private $comment;

     protected function __construct($comment)
     {
         $this->comment = $comment;
     }

     protected function getComment()
     {
         return $this->comment;
     }
 }

//final修飾にてこれ以下の継承を禁止
final class Data extends DataSnapshot
{
    private $comment;

    public function __construct()
    {
        $this->comment = array();
    }

    public function takeSnapshot()
    {
        return new DataSnapshot($this->comment);
    }

    public function restoreSnapshot(DataSnapshot $snapshot)
    {
        $this->comment = $snapshot->getComment();
    }

    public function addComment($comment)
    {
        $this->comment[] = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }
}

//スナップショットをセッションに保存
class DataCaretaker
{
    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function setSnapshot($snapshot)
    {
        $this->snapshot = $snapshot;
        $_SESSION['snapshot'] = $this->snapshot;
    }

    public function getSnapshot()
    {
        return (isset($_SESSION['snapshot']) ? $_SESSION['snapshot'] : null);
    }
}


//以下クライアント
session_start();

$caretaker = new DataCaretaker();
$data = isset($_SESSION['data']) ? $_SESSION['data'] : new Data();

$mode = (isset($_POST['mode'])? $_POST['mode'] : '');

switch ($mode)
{
    case 'add':
        /**
         * コメントをDataオブジェクトに登録する
         * 現時点のコメントはセッションに保存している事に注意
         */
        $data->addComment(　(isset($_POST['comment']) ? $_POST['comment'] : '')　);
        break;
    case 'save':
        /**
         * データのスナップショットを取り、DataCaretakerに依頼して
         * 保存する
         */
        $caretaker->setSnapshot($data->takeSnapshot());
        echo '<font style="color: #dd0000;">データを保存しました。</font><br>';
        break;
    case 'restore':
        /**
         * DataCaretakerに依頼して保存したスナップショットを取得し、
         * データを復元する
         */
        $data->restoreSnapshot($caretaker->getSnapshot());
        echo '<font style="color: #00aa00;">データを復元しました。</font><br>';
        break;
    case 'clear':
        $data = new Data();
}

/**
 * 登録したコメントを表示する
 */
echo '今までのコメント';
if (!is_null($data))
{
    echo '<ol>';
    foreach ($data->getComment() as $comment) {
        echo '<li>'
        . htmlspecialchars($comment, ENT_QUOTES, mb_internal_encoding())
        . '</li>';
    }
    echo '</ol>';
}

/**
 * 次のアクセスで使うデータをセッションに保存
 */
$_SESSION['data'] = $data;
?>
<form action="" method="post">
コメント：<input type="text" name="comment"><br>
<input type="submit" name="mode" value="add">
<input type="submit" name="mode" value="save">
<input type="submit" name="mode" value="restore">
<input type="submit" name="mode" value="clear">
</form>
