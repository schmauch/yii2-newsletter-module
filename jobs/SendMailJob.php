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
        
        
        // get sender email from config        
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
        
        // instanciate mailer
        $mailer = \Yii::$app->mailer;
        if (!is_a($mailer, '\yii\mail\MailerInterface')) {
            die('Kein Mailer!');
        }
        
        // set view path
        $mailer->viewPath = $newsletter->getMessageDir();
        
        // set layout
        $mailer->htmlLayout = '@schmauch/newsletter/' . 
            $module->params['template_path'] . $newsletter->template . '/html';
        
        // make recipient's params available as array
        if(!is_array($this->recipient)) {
            $this->recipient = new \ArrayObject($this->recipient);
        }
        
        // get email column
        if (isset($this->recipient['email'])) {
            $emailColumn = $this->recipient['email'];
        } else {
            $emailColumn = array_search('email', array_column($newsletter->recipientsObject->getColumns(), 'header'));
        }
                
        // make subject and email available in template
        $mailer->view->params['title'] = $newsletter->subject;
        $mailer->view->params['email'] = $this->recipient[$emailColumn];

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
        
        
        foreach($newsletter->recipientsObject->getColumns() as $column) {
            if(is_array($column)) {
                $params[$column['header']] = $this->recipient[$column['attribute']];
            } else {
                $params[$column] = $this->recipient[$column];
            }
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
        $message->setTo($this->recipient[$emailColumn]);
        $message->setSubject($newsletter->subject);
        

        foreach($attachments as $attachment) {
            $message->attach($attachment);
        }
        
        if($mailer->send($message)) {
            echo '[' . date('Y-m-d H:i:s') . '] verarbeite ' . $this->recipient[$emailColumn] . "\n";
            $newsletter->mails_sent++;
            $newsletter->save();
        }
    }
}
