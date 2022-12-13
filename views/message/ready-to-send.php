<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Bearbeite Newsletter: ' . $model->subject;

echo '<h1>' . $this->title . '</h1>';

echo $this->render('_nav', ['id' => $model->id]);

$messages = [
    'recipients_object' => [
        'Es gibt keine Empfänger-Klasse.',
        'Empfänger-Klasse &#x2192; ok'
    ],
    'recipients' => [
        'Die Liste der Empfänger ist leer',
        'Empfänger &#x2192; ok'
    ],    
    'html' => [
        'Es gibt keinen HTML-Content',
        'HTML-Content &#x2192; ok'
    ],
    'text' => [
        'Es gibt keinen Plain-Text-Content',
        'Plain-Text-Content &#x2192; ok'
    ],
    'placeholders' => [
        'Es gibt nicht für alle Platzhalter eine Entsprechung in den Empfänger-Daten',
        'Platzhalter &#x2192; ok'
    ],
    'attachments' => [
        'Die Attachments konnten nicht angehängt werden.',
        'Attachments &#x2192; ok'
    ],
];

$links = [
    'recipients_object' => 'choose-recipients',
    'recipients' => 'choose-recipients',
    'html' => 'edit-html',
    'text' => 'edit-text',
    'placeholders' => 'edit-html',
    'attachments' => 'attachments'
];

$classes = ['danger', 'success'];

$icons = ['&times', '&#x2714; '];

foreach($checks as $key => $value) {
    echo '
        <div class="d-block alert alert-' . $classes[$value] . '">' .
            $icons[$value] . ' ' . $messages[$key][$value]; 
    if(!$value) {
        echo ' &#x2192; ' . Html::a('beheben', Url::to([$links[$key], 'id' => $model->id]), 
            ['class' => 'btn btn-danger px-2 py-0']
        );
    }
    echo '</div>';
}

if (array_product($checks) && empty($model->pid)) {
    echo Html::a('Newsletter versenden', Url::to(['queue/queue', 'id' => $model->id]), ['class' => 'btn d-block btn-primary']);
} else {
    echo Html::a('Versandbericht ansehen', Url::to(['queue/status', 'id' => $model->id]), ['class' => 'btn d-block btn-primary']);
}
