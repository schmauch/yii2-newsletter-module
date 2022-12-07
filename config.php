<?php

return [
    'components' => [
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db', 
            'tableName' => '{{%queue}}', 
            'channel' => 'default', 
            'mutex' => \yii\mutex\MysqlMutex::class,
            'as log' => \yii\queue\LogBehavior::class,
            'deleteReleased' => false,
        ],
    ],
    'params' => [
        'files_path' => __DIR__ . '/mail/',
        'template_path' => '/views/layouts/',
        'allowed_attachment_extensions' => ['jpg', 'png', 'gif', 'svg', 'pdf'],
        'senderEmail' => ['mail@roger-schmutz.ch' => 'Roger Schmutz'],
        'messages_limit' => 30,
        'messages_delay' => 300, 
    ],
];
