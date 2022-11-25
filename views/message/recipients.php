<?php

use common\models\NewsletterMessage;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

?>
<div class="newsletter-recipients">

    <h1>Newsletter recipients</h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'subject',
            //'html_file',
            //'text_file',
            //'template',
            //'recipients_file',
            //'send_at',
            //'completed_at',
            //'blacklisted',
            //[
            //    'class' => ActionColumn::className(),
            //    'urlCreator' => function ($action, $model, $key, $index, $column) {
            //        return Url::toRoute([$action, 'id' => $model->id]);
            //     }
            //],
        ],
    ]); ?>


</div>
