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
            'subject' => $this->string()->notNull(),
            'html_file' => $this->string(),
            'text_file' => $this->string(),
            'template' => $this->string(),
            'recipients_file' => $this->string(),
            'send_at' => $this->datetime(),
            'completed_at' => $this->datetime(),
            'blacklisted' => $this->integer()
        ]);
        
        $this->createTable('newsletter_attachments', [
            'id' => $this->primaryKey(),
            'message_id' => $this->integer()->notNull(),
            'file_name' => $this->string()->notNull(),
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
            'added_at' => $this->datetime()->notNull(),
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
