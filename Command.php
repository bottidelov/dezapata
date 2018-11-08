<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 11.command
 * クラス自体を「命令」とし処理を担当させる。（chain of responsibility）の処理版
 * 個々の修正・追加処理が容易で、またキュー管理による振る舞いを実装できる。
 */

// 独立した処理クラス
 class File
 {
     private $name;
     public function __construct($name)
     {
         $this->name = $name;
     }
     public function getName()
     {
         return $this->name;
     }
     public function decompress()
     {
         echo $this->name . 'を展開しました<br>';
     }
     public function compress()
     {
         echo $this->name . 'を圧縮しました<br>';
     }
     public function create()
     {
         echo $this->name . 'を作成しました<br>';
     }
 }

//命令クラスのインターフェース
 interface Command
 {
     public function execute();
 }

//個々の命令クラス
class TouchCommand implements Command
{
    private $file;
    public function __construct(File $file)
    {
        $this->file = $file;
    }
    public function execute()
    {
        $this->file->create();
    }
}

class CompressCommand implements Command
{
    private $file;
    public function __construct(File $file)
    {
        $this->file = $file;
    }
    public function execute()
    {
        $this->file->compress();
    }
}

class CopyCommand implements Command
{
    private $file;
    public function __construct(File $file)
    {
        $this->file = $file;
    }
    public function execute()
    {
        $file = new File('copy_of_' . $this->file->getName());
        $file->create();
    }
}

//命令クラスをキューに保持するためのクラス(invoker
class Queue
{
    private $commands;
    private $current_index;

    public function __construct()
    {
        $this->commands = array();
        $this->current_index = 0;
    }

    public function addCommand(Command $command)
    {
        $this->commands[] = $command;
    }

    public function run()
    {
        while (!is_null($command = $this->next()))
        {
            $command->execute();
        }
    }

    private function next()
    {
        if (count($this->commands) === 0 || count($this->commands) <= $this->current_index)
        {
            return null;
        }
        else
        {
            return $this->commands[$this->current_index++];
        }
    }
}

//以下クライアント
$queue = new Queue();
$file = new File("sample.txt");
$queue->addCommand(new TouchCommand($file));
$queue->addCommand(new CompressCommand($file));
$queue->addCommand(new CopyCommand($file));

$queue->run();
