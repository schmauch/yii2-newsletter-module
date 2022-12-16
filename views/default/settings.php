<h1>Einstellungen</h1>
<table class="table table-striped table-hover">
    <tbody>
<?php

foreach($params as $key => $param) {
    echo '<tr><td>' . $key . '</td><td>' . print_r($param, true) . '</td></tr>';
}

?>
    </tbody>
</table>
