<?php

use yii\helpers\Url;


$action = \Yii::$app->controller->action->id;

$active = [
    'update' => $action == 'update' ? ' active' : '',
    'edit-html' => $action == 'edit-html' ? ' active' : '',
    'edit-text' => $action == 'edit-text' ? ' active' : '',
]

?>

<ul class="nav nav-tabs my-3">
    <li class="nav-item">
        <a class="nav-link<?=$active['update']?>" href="<?=Url::to(['update', 'id' => $id])?>">Einstellungen</a>
    </li>
    <li class="nav-item">
        <a class="nav-link<?=$active['edit-html']?>" href="<?=Url::to(['edit-html', 'id' => $id])?>">Html bearbeiten</a>
    </li>
    <li class="nav-item">
        <a class="nav-link<?=$active['edit-text']?>" href="<?=Url::to(['edit-text', 'id' => $id])?>">Text bearbeiten</a>
    </li>
</ul>
