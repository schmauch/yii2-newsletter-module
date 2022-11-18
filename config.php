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
        ],
    ],
    'params' => [
        'files_path' => __DIR__ . '/mail/',
        'template_path' => __DIR__ . '/views/templates/',
        'allowed_attachment_extensions' => ['jpg', 'png', 'gif', 'svg', 'pdf'],
        'senderEmail' => ['mail@roger-schmutz.ch' => 'Roger Schmutz']
    ],
];
