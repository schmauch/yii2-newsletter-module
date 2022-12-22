<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->registerMetaTag([
    'http-equiv' => 'refresh',
    'content' => '30',
]);


$this->title = 'Versende Newsletter: ' . $model->subject;

$states = [
    'waiting'     => 'in Warteschlange',
    'delayed'     => 'terminiert auf ' . $model->send_date . ' um ' . $model->send_time . ' Uhr',
    'reserved'    => 'in Bearbeitung',
    'done'        => 'gesendet',
];



echo '<h1>' . Html::encode($this->title) . '</h1>';

if (!empty($model->pid)) {
    echo '<div class="alert alert-warning">Die Warteschlange ist in Bearbeitung! [' . $model->pid . ']</div>';
}


echo '
<table class="table table-striped">
<thead>
    <tr class="table-dark">
        <td>Status</td>
        <td>Anzahl</td>
    </tr>
</thead>
';

echo '
    <tr>
        <td>Total Empfänger</td>
        <td>' . $model->recipientsObject->getDataProvider()->getTotalCount() . '</td>
    </tr>
';


foreach($jobs as $key => $value) {

    echo '
        <tr>
            <td>' . $states[$key] . '</td>
            <td>' . $value . '</td>
        </tr>
    ';
}

echo '
    <tr>
        <td>aus Liste entfernt (aufgrund eines Blacklist-Eintrags)</td>
        <td>' . $model->blacklisted . '</td>
    </tr>
';

echo '
</table>
';

if (empty($model->pid)) {
    echo 
    '<div>' .
        Html::a('Warteschlange abarbeiten.', Url::to(['run', 'id' => $model->id]), ['class' => 'btn btn-success btn-block']) .
    '</div>';
}

echo '';
?>
<div id="log" class="accordion">
    <div class="accordion-item">
        <h3 class="accordion-header" id="logHeading">
        
            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#logFile" aria-expanded="false" aria-controls="logFile">
                Verlauf ansehen
            </button>
        </h3>
        <div id="logFile" class="accordion-collapse collapse" aria-labelledby="logHeading" data-bs-parent="#log">
            <pre class="accordion-body alert alert-dark mb-0">
                <?php 
                    $logFile = $model->getMessageDir() . 'queue.log';
                    if (!is_file($logFile)) {
                        echo 'kein Logfile vorhanden.';
                    }
                    
                    include $logFile;
                ?>
            </pre>
        </div>
    </div>
</div>
