<?php

use common\models\NewsletterMessage;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\DataColumn;

?>

<h2 class="border-top pt-3">Ausgewählte Empfänger</h2>

<div class="newsletter-recipients">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php 
    echo GridView::widget([
        'dataProvider' => $model->recipientsObject->dataProvider,
        //'filterModel' => $searchModel,
        'columns' => $model->recipientsObject->getColumns(),
    ]);
?>


</div>
