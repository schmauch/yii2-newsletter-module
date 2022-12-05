<?php

use yii\helpers\Url;


$action = \Yii::$app->controller->action->id;

$active = [
    'update' => $action == 'update' ? ' active' : '',
    'edit-html' => $action == 'edit-html' ? ' active' : '',
    'edit-text' => $action == 'edit-text' ? ' active' : '',
    'attachments' => $action == 'attachments' ? ' active' : '',
    'choose-recipients' => $action == 'choose-recipients' ? ' active' : '',
    'ready-to-send' => $action == 'ready-to-send' ? ' active' : '',
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
    <li class="nav-item">
        <a class="nav-link<?=$active['attachments']?>" href="<?=Url::to(['attachments', 'id' => $id])?>">Attachments</a>
    </li>
    <li class="nav-item">
        <a class="nav-link<?=$active['choose-recipients']?>" href="<?=Url::to(['choose-recipients', 'id' => $id])?>">Empfänger auswählen</a>
    </li>
    <li class="nav-item">
        <a class="nav-link<?=$active['ready-to-send']?>" href="<?=Url::to(['ready-to-send', 'id' => $id])?>">Versenden</a>
    </li>
</ul>
