<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


$this->title = 'Bearbeite Newsletter: ' . $model->subject;

echo '<h1>' . $this->title . '</h1>';

echo $this->render('_nav', ['id' => $model->id]);

echo '<h3>Verfügbare Attachments</h3><ul class="list-group mb-3">';
 
foreach($model->newsletterAttachments as $attachment) {
    echo '<li class="list-group-item">' . $attachment->file . '</li>';
}

echo '</ul>';

echo '<h3 class="pt-3 border-top">Weiteres Attachment hochladen</h3>';

$form = ActiveForm::begin();

echo $form->field($newAttachment, 'file')->fileInput(['class' => 'form-control'])->label('Datei auswählen', ['class' => 'form-label']);

echo Html::button('hochladen', ['type' => 'submit', 'class' => 'btn btn-success mt-3']);

$form->end();