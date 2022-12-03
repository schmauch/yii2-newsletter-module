<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<div class="newsletter-message-update">

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_nav', ['id' => $model->id]) ?>

<h2>Art der Empfänger</h2>
<p>Hier kann ausgewählt werden, wie die Empfänger bereit gestellt werden.</p>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'recipients_class')->dropDownList($options)->label(false) ?>

<div class="form-group my-3">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success mr-3']) ?>
    <?= Html::a('Abbrechen', Url::previous(), ['class' => 'btn btn-dark mr-3']) ?>
</div>

<?php ActiveForm::end(); ?>
