<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 15.Interpreter
 * 「翻訳」の意味
 *  文法を解析し、その結果を利用して処理をおこなうことを目的としている
 * 言語的解釈による機能を実装する事で高速に問題を処理できる場合があります。
 */

 class Context
{
    private $commands;
    private $index  = 0;

    public function __construct($command)
    {
        $this->commands = explode(' ', trim($command));
    }

    public function next()
    {
        $this->index++;
        return $this;
    }

    public function getCommand()
    {
        if (!array_key_exists($this->index, $this->commands))
        {
            return null;
        }
        return $this->commands[$this->index];
    }
}

interface ExpressionInterface
{
    public function execute(Context $context);
}

class JobExpression implements ExpressionInterface
{
    public function execute(Context $context)
    {
        if ($context->getCommand() !== '$') {
            throw new Exception('Missing opening tag "$"');
        }
        $command_list = new CommandExpression();
        $command_list->execute($context->next());
    }
}

class CommandExpression implements ExpressionInterface
{
    public function execute(Context $context)
    {
        while (true) {
            $text = $context->getCommand();
            if (is_null($text)) {
                throw new Exception('There is no closing command "/"');
            } else if ($text === '/') {
                break;
            } else {
                $expression = new DatetimeExpression();
                $expression->execute($context);
            }
            $context->next();
        }
    }
}

class DatetimeExpression implements ExpressionInterface
{
    public function execute(Context $context)
    {
        $command = $context->getCommand();

        switch ($command) {
            case 'year':
                echo date('Y') . ' ';
                break;
            case 'month':
                echo date('m') . ' ';
                break;
            case 'day':
                echo date('d') . ' ';
                break;
            case 'time':
                echo date('H:i') . ' ';
                break;
            case 'second':
                echo date('s') . ' ';
                break;
        }
    }
}


//以下クライアント
$command = '$ year month day time second /';

$job = new JobExpression();
$job->execute(new Context($command));
