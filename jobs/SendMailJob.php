<?php

namespace schmauch\newsletter\jobs;

use yii\base\BaseObject;
use yii\helpers\Console;
use yii\helpers\Url;

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
    public $channel;
    
    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $module = NewsletterModule::getInstance();
        $newsletter = NewsletterMessage::findOne($this->message_id);
                
        if (isset($module->senderEmail)) {
            if (isset($module->senderName)) {
                $from = [$module->senderEmail => $module->senderName];
            } else {
                $from = $module->senderEmail;
            }
        } else {
            $senderEmail = \Yii::$app->params['senderEmail'] ?? 'noreply@example.com';
            if (isset(\Yii::$app->params['senderName'])) {
                $from = [$senderEmail => \Yii::$app->params['senderName']];
            } else {
                $from = $senderEmail;
            }
        }
        
        
        
        $mailer = \Yii::$app->mailer;
        if (!is_a($mailer, '\yii\mail\MailerInterface')) {
            die('Kein Mailer!');
        }
        
        $mailer->viewPath = $newsletter->getMessageDir();
        
        $mailer->htmlLayout = '@schmauch/newsletter/' . 
            $module->params['template_path'] . $newsletter->template . '/html';
            
        

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
        
        if(!is_array($this->recipient)) {
            $this->recipient = new \ArrayObject($this->recipient);
        }
        
        foreach($newsletter->recipientsObject->getColumns() as $column) {
            $params[$column] = $this->recipient[$column];
        }
        
        $message = $mailer->compose([
                    'html' => 'message.html',
                    'txt' => 'message.txt'],
                    $params,
                    $embed, 
        );
        
        if (!is_a($message, '\yii\mail\MessageInterface')) {
            die('Keine Message!');
        }
        
        $message->setFrom($from);
        $message->setTo($this->recipient['email']);
        $message->setSubject($newsletter->subject);
        
        $message->view->params['title'] = $newsletter->subject;
        $message->view->params['email'] = $this->recipient['email'];

        foreach($attachments as $attachment) {
            $message->attach($attachment);
        }
        
        if($mailer->send($message)) {
            /*Console::stdout(
                'verarbeite ' . 
                Console::ansiFormat($this->recipient->email, [Console::FG_GREEN]) . "\n");*/
            echo '[' . date('Y-m-d H:i:s') . '] verarbeite ' . $this->recipient['email'] . "\n";
            $newsletter->mails_sent++;
            $newsletter->save();
        }
    }
}
