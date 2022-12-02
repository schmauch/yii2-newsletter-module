<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


?>

<h2 class="border-top pt-3">Einstellungen</h2>

<div class="container">

<?php

$form = ActiveForm::begin();

$class = $model->recipients_object;
$view = strtolower(ltrim(preg_replace('/([A-Z])/', '-$1', $class), '-'));

echo $this->render('recipients/'.$view, [
    'form' => $form, 
    'model' => $model,
    'recipients_object' => $recipients_object
]);

?>

</div>

<div class="form-group mx-0 my-3 ">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success mr-3 ml-0 ']) ?>
    <?= Html::a('Abbrechen', Url::previous(), ['class' => 'btn btn-dark mr-3']) ?>
</div>

<?php ActiveForm::end(); ?>
