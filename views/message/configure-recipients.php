<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/** @var yii\web\View $this */
/** @var common\models\NewsletterMessage $model */

$this->title = 'Bearbeite Newsletter: ' . $model->subject;


?>

<div class="newsletter-message-update">

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_nav', ['id' => $model->id]) ?>

<?php 

$form = ActiveForm::begin(); 

?>



<div class="form-group mt-3">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success mr-3']) ?>
    <?= Html::a('Abbrechen', Url::previous(), ['class' => 'btn btn-dark mr-3']) ?>
</div>

<?php ActiveForm::end(); ?>
