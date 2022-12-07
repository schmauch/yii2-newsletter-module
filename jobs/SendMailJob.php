<?php

namespace schmauch\newsletter\jobs;

use yii\base\BaseObject;
use yii\helpers\Console;

use schmauch\newsletter\Module as NewsletterModule;
use schmauch\newsletter\models\NewsletterMessage as NewsletterMessage;

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
    public $message_id;
    public $recipient;
    
    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $module = NewsletterModule::getInstance();
        $newsletter = NewsletterMessage::findOne($this->message_id)
                
        if (isset($module->params['senderEmail'])) {
            $from =  $module->params['senderEmail'];
        } else {
            $from = \Yii::$app->params['senderEmail'];
        }
         
        $mailer = \Yii::$app->mailer;
        if (!is_a($mailer, '\yii\mail\MailerInterface')) {
            die('Kein Mailer!');
        }
        
        $mailer->viewPath = $newsletter->getMessageDir();

        $mailer->htmlLayout = '@schmauch/newsletter/' . 
            $this->module->params['template_path'] . $newsletter->template . '/html';

        $embed = [];
        $attachments = [];
        foreach($newsletter->newsletterAttachments as $attachment) {
            $file = $newsletter->getMessageDir() . 'attachments/' . $attachment->file;
            if ($attachment->mode) {
                $embed[$attachment->file] = $file;
            } else {
                $attachments[$attachment->file] = $file;
            }
        }
        
        $message = $mailer->compose([
                    'html' => 'message.html',
                    'txt' => 'message.txt'],
                    [
                        $embed, 
                        $params,
                    ],
        ]);
        
        if (!is_a($message, '\yii\mail\MessageInterface')) {
            die('Keine Message!');
        }
        
        foreach($attachments as $attachment) {
            $message->attach($attachment);
        }
        
        
        if(is_object($this->recipient)) {
            $message->setTo($this->recipient->email);
            foreach($newsletter->getColumns() as $column) {
                $params[$column] => $this->recipient->$column;
            }
        } else {
            $message->setTo($this->recipient->email);
            foreach($newsletter->getColumns() as $column) {
                $params[$column] => $this->recipient->$column;
            }
        }
        
        $message->setSubject($newsletter->subject);
        $message->setFrom($from);
        
        if($mailer->send($message)) {
            Console::stdout(
                'verarbeite ' . 
                Console::ansiFormat($this->recipient, [Console::FG_GREEN]) . "\n");
        }
    }
}
