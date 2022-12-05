<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


$this->title = 'Bearbeite Newsletter: ' . $model->subject;

echo '<h1>' . $this->title . '</h1>';

echo $this->render('_nav', ['id' => $model->id]);
 
foreach($model->newsletterAttachments as $attachment) {
    print_r($attachment);
}

$form = ActiveForm::begin();

echo $form->field($newAttachment, 'file')->input('file');

echo Html::button('hochladen', ['type' => 'submit', 'class' => 'btn btn-success']);

$form->end();