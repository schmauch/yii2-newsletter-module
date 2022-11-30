<?php

use common\models\NewsletterMessage;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\DataColumn;

?>
<div class="newsletter-recipients">

    <h1>Newsletter recipients</h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
        /*print_r($data);
        $nav ='<ul class="nav nav-tabs">';
        $content ='<div class="tab-content">';
        
        foreach($data as $index => $page) {
            $active = $index == 0 ? ' active' : '';
            $nav .= '
                <li><a data-toggle="tab" href="#page' . $index . '">
                    Seite ' . $index + 1 . '</a></li>';
            $content .= '
            <div id="page' . $index . '" class="tab-pane fade ' . $active . '">';
            foreach($page as $model) {
                $content .= '<div class="row">';
                foreach($model as $column) {
                    $content .= '<div class="col-3">'.$column.'</div>';
                }
                $content .= '</div>
                ';
            }
            $content .= '</div>';
        }
        $content .= '
            </div>';
        $nav .= '</ul>';
        
        echo $nav;
        echo $content;*/
    ?>

    <?php 
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => $columns,
        /*[
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => DataColumn::class,
                'label' => 'überschrift',
                'content' => function($model, $key, $index, $column) {
                    return $key . var_export($model, true);
                }
            ],*/
            //$keys
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
        //],
    ]);
?>


</div>
