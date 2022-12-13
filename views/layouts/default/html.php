<?php

use yii\helpers\Url;

        $message = new class { 
            public $subject = 'Weihnachtsgruss';
            public function embed($file) {
                echo str_replace('/home/schmauch/www/', 'http://localhost/', $file);
            }
        };
        

?> 
<?php $this->beginPage() ?>
<!doctype html>
<html lang="de">
<head>
    <title><?php //$message->subject ?> Subject</title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: white;
            font-family: sans-serif;
        }
        
        table {
            width: 100%;
            border: none;
        }
        
        tr, td {
            border: none;
        }
        
        .left, .right {
            width: 16.666%;
        }
        
        .header {
            background-color: white;
        }
        
        .footer {
            background-color: black;
            color: white;
            text-align: center;
            font-size: smaller;
        }
        
        .footer .innerTd {
            width: 22%;
        }
        .footer a {
            color: #efefef;
            text-decoration: underline;
        }
        
        .footer .imprint {
            padding-top: 16px;
            padding-bottom: 16px;
            text-align: left;
        }
        
        
    </style>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    
    <table>
        <tr class="header">
            <td colspan="3">
                <img alt="logo.png" 
                src="<?= $message->embed('/home/schmauch/www/girardi/yii/dev/yii2-newsletter-module/views/layouts/default/assets/logo.png') ?>" 
                width="500" height="139">
            </td>
        </tr>
        <tr class="content">
            <td class="left">&nbsp;</td>
            <td>
                <?= $content ?>
            </td>
            <td class="right">&nbsp;</td>
        </tr>
        <tr class="footer">
            <td colspan="3">
                <p>
                    Falls Sie keine weiteren Nachrichten mehr erhalten möchten, können Sie sich
                    <a href="<?= Url::to(['blacklist/sign-off'], true) ?>">
                        vom Newsletter abmelden
                    </a>
                </p>
                <table class="imprint">
                    <tr>
                        <td class="left">&nbsp;
                        <td class="innerTd">
                            Arthur Girardi AG<br>
                            Maienbrunnenstrasse 5<br>
                            8908 Hedingen
                        </td>
                        <td class="innerTd" style="text-align: center;">
                            Tel. <a href="tel:+41433226699">043 322 66 99</a><br>
                            E-Mail: <a href="mailto:info@girardi.ch">info@girardi.ch</a><br>
                            <a href="https://www.girardi.ch">www.girardi.ch</a>
                        </td class="innerTd">
                        <td class="innerTd" style="text-align: right;">
                            <img alt="logo.png" 
                                src="<?= $message->embed('/home/schmauch/www/girardi/yii/dev/yii2-newsletter-module/views/layouts/default/assets/macher.png') ?>" 
                                width="113" height="100">
                        </td>
                        <td class="right">&nbsp;
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
