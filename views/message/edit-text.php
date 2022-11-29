<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Bearbeite Newsletter: ' . $model->subject;


?>


<div class="newsletter-message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_nav', ['id' => $model->id]) ?>

<?php

echo Html::a('Text aus HTML-Datei laden', Url::current(['loadFromHtml' => '1']), ['class' => 'btn btn-primary mb-2']);

$form = ActiveForm::begin();

echo $form->field($model, 'text')->textarea(['rows' => 20])->label(false);

echo '
    <div class="form-group">' .
        Html::submitButton('Save', ['class' => 'btn btn-success mr-3 mt-3']) . ' ' .
        Html::a('Abbrechen', Url::previous(), ['class' => 'btn btn-secondary mr-3 mt-3']) .
    '</div>';


ActiveForm::end();

