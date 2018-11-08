<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 10.chain of responsibility
 * 「責任」
 * クライアントからハンドラクラスを通して各子クラスに順に処理の解決を移譲する
 *  処理を依頼する側と処理を行う側に分割でき、修正・追加が容易
 */

//ハンドラクラス
 abstract class ValidationHandler
 {
     private $next_handler;

     public function __construct()
     {
         $this->next_handler = null;
     }

     public function setHandler(ValidationHandler $handler)
     {
         $this->next_handler = $handler;
         return $this;
     }

     public function getNextHandler()
     {
         return $this->next_handler;
     }

     //チェーンの実行
     public function validate($input)
     {
         $result = $this->execValidation($input);
         if (!$result) {
             return $this->getErrorMessage();
         } elseif (!is_null($this->getNextHandler())) {
             return $this->getNextHandler()->validate($input);
         } else {
             return true;
         }
     }

    //自身が解決を実行
     protected abstract function execValidation($input);

     //エラーメッセージの取得
     protected abstract function getErrorMessage();
 }

//以下、実体処理を行う子クラス
 class AlphabetValidationHandler extends ValidationHandler
 {
     protected function execValidation($input)
     {
         return preg_match('/^[a-z]*$/i', $input);
     }

     protected function getErrorMessage()
     {
         return '半角英字で入力してください';
     }
 }

 class NumberValidationHandler extends ValidationHandler
{

    protected function execValidation($input)
    {
        return (preg_match('/^[0-9]*$/', $input) > 0);
    }

    protected function getErrorMessage()
    {
        return '半角数字で入力してください';
    }
}

class NotNullValidationHandler extends ValidationHandler
{
    protected function execValidation($input)
    {
        return (is_string($input) && $input !== '');
    }

    protected function getErrorMessage()
    {
        return '入力されていません';
    }
}


//以下クライアント
$validate_type = $_POST['validate_type'];
$input = $_POST['input'];

   /**
    * チェーンの作成
    * validate_typeの値によってチェーンを動的に変更
    */
   $not_null_handler = new NotNullValidationHandler();
   $length_handler = new MaxLengthValidationHandler(8);

   $option_handler = null;
   switch ($validate_type)
   {
       case 1:
           include_once 'AlphabetValidationHandler.class.php';
           $option_handler = new AlphabetValidationHandler();
           break;
       case 2:
           include_once 'NumberValidationHandler.class.php';
           $option_handler = new NumberValidationHandler();
           break;
   }

   if (!is_null($option_handler))
   {
       $length_handler->setHandler($option_handler);
   }
   $handler = $not_null_handler->setHandler($length_handler);

   /**
    * 処理実行と結果メッセージの表示
    */
   $result = $handler->validate($_POST['input']);
   if ($result === false)
   {
       echo '検証できませんでした';
   }
   elseif (is_string($result) && $result !== '')
   {
       echo '<p style="color: #dd0000;">' . $result . '</p>';
   }
   else
   {
       echo '<p style="color: #008800;">OK</p>';
   }

<form action="" method="post">
 <div>
   値：<input type="text" name="input">
 </div>
 <div>
   検証内容：<select name="validate_type">
   <option value="0">任意</option>
   <option value="1">半角英字で入力されているか</option>
   <option value="2">半角数字で入力されているか</option>
   </select>
 </div>
 <div>
   <input type="submit">
 </div>
</form>
