<h1>Einstellungen</h1>
<ul>
<?php

foreach($params as $key => $param) {
    echo '<li>' . $key . ': ' . print_r($param, true) . '</li>';
}

?>
</ul>