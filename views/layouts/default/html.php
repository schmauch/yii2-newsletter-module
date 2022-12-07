<?php $this->beginPage() ?>
<!doctype html>
<html lang="de">
<head>
    <title><?php $message->subject ?></title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    
    <style type="text/css">
        body {
            color: white;
            background-color: black;
            text-align: center;
        }
        
        #main {
            max-width: 72em;
            margin:auto;
            padding: 2em;
            text-align: left;
            border: 1px solid white;
        }
    </style>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <main id="main">
        <?= $content ?>
    </main>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
