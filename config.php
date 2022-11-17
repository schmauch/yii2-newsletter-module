<?php

return [
    'params' => [
        'files_path' => __DIR__ . '/mail/',
        'template_path' => __DIR__ . '/views/templates/',
        'allowed_attachment_extensions' => ['jpg', 'png', 'gif', 'svg', 'pdf'],
        'from' => ['mail@roger-schmutz.ch' => 'Roger Schmutz']
    ],
];
