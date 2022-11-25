<?php

namespace schmauch\newsletter\jobs;

use yii\base\BaseObject;
use yii\helpers\Console;

use schmauch\newsletter\Module as NewsletterModule;

/*
 * Queue job to send out mails
 *
 * @property string|array $message the view file(s) used for yii\mail\MailerInterface::compose()
 * @property array|null $params the parameters used for yii\mail\MailerInterface::compose()
 * @property string|array $recipient the recipient used for yii\mail\MessageInterface::setTo()
 * @property string|array $recipient the recipient used for yii\mail\MessageInterface::setSubject()
 */
class SendMailJob extends BaseObject implements \yii\queue\JobInterface
{
    public $message;
    public $params = [];
    public $recipient;
    public $subject;
    
    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $module = \schmauch\newsletter\Module::getInstance();
                
        if (isset($module->params['senderEmail'])) {
            $from =  $module->params['senderEmail'];
        } else {
            $from = \Yii::$app->params['senderEmail'];
        }
         
        $mailer = \Yii::$app->mailer;
        if (!is_a($mailer, '\yii\mail\MailerInterface')) {
            die('Kein Mailer!');
        }
        
        $message = $mailer->compose($this->message, $this->params);
        if (!is_a($message, '\yii\mail\MessageInterface')) {
            die('Keine Message!');
        }
        
        $message->setTo($this->recipient);
        $message->setSubject($this->subject);
        $message->setFrom($from);
        
        //$mailer->send($message);

        sleep(60);
        echo 'irgendwas!';
        flush();
        Console::stdout(
            'verarbeite ' . 
            Console::ansiFormat($this->recipient, [Console::FG_GREEN]) . "\n");
    }
}
