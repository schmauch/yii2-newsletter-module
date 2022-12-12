<?php

use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'Bearbeite Newsletter: ' . $model->subject;

?>

<div class="newsletter-message-update">

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_nav', ['id' => $model->id]) ?>


<?php
echo $this->render('_choose-recipients', [
    'model' => $model,
    'options' => $options,
]);

if (!empty($model->recipients_class)) {
    echo $this->render('_configure-recipients', [
        'model' => $model,
        //'recipients_object' => $recipients_object,
    ]);

    echo $this->render('_view-recipients', [
        'model' => $model,
        //'dataProvider' => $dataProvider,
        //'columns' => $columns,
    ]);
}
