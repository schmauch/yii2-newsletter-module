<?php

use yii\helpers\Url;

?>

Die Schreinerei
ARTHUR GIRARDI AG

<?= $content ?>

Falls Sie keine weiteren Nachrichten mehr erhalten möchten, können Sie sich unter folgendem Link vom Newsletter abmelden:
<?php echo Url::to(['blacklist/sign-off', 'email' => $message->to], true) ?>


Arthur Girardi AG           Tel. 043 322 66 99
Maienbrunnenstrasse 5       E-Mail: info@girardi.ch
8908 Hedingen               www.girardi.ch


