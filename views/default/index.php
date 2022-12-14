<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Willkommen im Newsletter Module';

?>
<div class="jumbotron">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="lead">
        Hier können Newsletter gestaltet, die Empfänger definiert und die
        Nachrichten verschickt werden.
    </p>
    <p>
        <a class="btn btn-lg btn-success" href="<?= Url::to(['message/create']) ?>">Jetzt loslegen</a></p>
</div>

<div class="body-content my-5">

    <div class="row">
        <div class="col-lg-4">
            <h2>Nachrichten auflisten</h2>
            <p>
                Hier finden Sie alle Newsletter, die erstellt wurden. Sie können
                dies filtern nach gesendeten und nicht gesendeten. Auch können Sie 
                nach Newslettern suchen.
            </p>
            <p>
                <a href="<?= Url::to(['message/index']) ?>" class="btn btn-light">
                    Liste ansehen
                </a>
            </p>
        </div>

        <div class="col-lg-4">
            <h2>Blacklist</h2>
            <p>
                Personen, die vom Unternehmen keinen Newsletter mehr erhalten 
                möchten, werden in einer "schwarzen Liste" geführt. Damit wird
                sichergestellt, dass - falls sie zu einem späteren Zeitpunkt 
                wieder zur Empfängerliste hinzugefügt werden - sie trotzdem nicht 
                angeschrieben werden.
            </p>
            <p>
                <a href="<?= Url::to(['blacklist/index']) ?>" class="btn btn-light">
                    Schwarze Liste ansehen/bearbeiten
                </a>
            </p>
        </div>

        <div class="col-lg-4">
            <h2>Einstellungen</h2>
            <p>
                Hier können Sie alle nötigen Einstellungen vornehmen.
            </p>
            <p>
                <a href="<?= Url::to(['default/settings']) ?>" class="btn btn-light">
                    Einstellungen ansehen/bearbeiten
                </a>
            </p>
        </div>
    </div>

</div>
