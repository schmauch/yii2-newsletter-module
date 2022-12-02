<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Bearbeite Newsletter: ' . $model->subject;

echo '<h1>' . $this->title . '</h1>';

echo $this->render('_nav', ['id' => $model->id]);

echo Html::a('Text aus HTML-Datei laden', Url::current(['loadFromHtml' => '1']), ['class' => 'btn btn-primary mb-2']);

$form = ActiveForm::begin();

echo $form->field($model, 'text')->textarea(['rows' => 20])->label(false);

echo '
    <div class="form-group">' .
        Html::submitButton('Save', ['class' => 'btn btn-success']) . ' ' .
        Html::a('Abbrechen', Url::previous(), ['class' => 'btn btn-dark m-3']) .
    '</div>';


ActiveForm::end();

