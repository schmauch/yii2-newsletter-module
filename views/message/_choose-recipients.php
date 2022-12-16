<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<h2>Art der Empfänger</h2>
<p>Hier kann ausgewählt werden, wie die Empfänger bereit gestellt werden.</p>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'recipients_class')->dropDownList($options)->label(false) ?>

<div class="form-group my-3">
    <?= Html::submitButton('speichern', ['class' => 'btn btn-success mr-3']) ?>&nbsp;&nbsp;&nbsp;
    <?= Html::a('abbrechen', Url::previous(), ['class' => 'btn btn-dark mr-3']) ?>
</div>

<?php ActiveForm::end(); ?>
