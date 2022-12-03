<?php

$this->title = 'Bearbeite Newsletter: ' . $model->subject;


echo $this->render('choose-recipients', [
    'model' => $model,
    'options' => $options,
]);

if (!empty($model->recipients_class)) {
    echo $this->render('configure-recipients', [
        'model' => $model,
        //'recipients_object' => $recipients_object,
    ]);

    echo $this->render('view-recipients', [
        'model' => $model,
        //'dataProvider' => $dataProvider,
        //'columns' => $columns,
    ]);
}
