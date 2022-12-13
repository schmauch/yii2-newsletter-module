<?php

use yii\db\Migration;

/**
 * Class m221110_103330_create_newsletter_tables
 */
class m221110_103330_create_newsletter_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('newsletter_messages', [
            'id' => $this->primaryKey(),
            'slug' => $this->string()->unique(),
            'subject' => $this->string()->notNull(),
            'template' => $this->string(),
            'recipients_class' => $this->text(),
            'recipients_config' => $this->text(),
            'send_at' => $this->timestamp(),
            'mails_sent' => $this->integer(),
            'blacklisted' => $this->integer(),
            'pid' => $this->integer(),
            'completed_at' => $this->datetime(),
        ]);
        
        $this->createTable('newsletter_attachments', [
            'id' => $this->primaryKey(),
            'message_id' => $this->integer()->notNull(),
            'file' => $this->string()->notNull(),
            'mode' => $this->integer()
        ]);
        
        // creates index for column `message_id`
        $this->createIndex(
            'idx-newsletter_attachments-message_id',
            'newsletter_attachments',
            'message_id'
        );
        
        // add foreign key for table `newsletter_messages`
        $this->addForeignKey(
            'fk-newsletter_attachments-message_id',
            'newsletter_attachments',
            'message_id',
            'newsletter_messages',
            'id',
            'CASCADE'
        );
        
        $this->createTable('newsletter_blacklist', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'added_at' => $this->timestamp()->notNull(),
        ]);         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `newsletter_messages`
        $this->dropForeignKey(
            'fk-newsletter_attachments-message_id',
            'newsletter_attachments'
        );
        
        // drops index for column `author_id`
        $this->dropIndex(
            'idx-newsletter_attachments-message_id',
            'newsletter_attachments'
        );
        
        // drop tables
        $this->dropTable('newsletter_attachments');
        $this->dropTable('newsletter_messages');
        $this->dropTable('newsletter_blacklist');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221110_103330_create_newsletter_tables cannot be reverted.\n";

        return false;
    }
    */
}
