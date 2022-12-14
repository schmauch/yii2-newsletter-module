<?php $this->beginPage() ?>
<!doctype html>
<html lang="de">
<head>
    <title><?php echo $this->params['title'] ?></title>
    
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
            color: white;
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
                src="<?= $message->embed('/home/girardi/www/yii/dev/yii2-newsletter-module/views/layouts/default/assets/logo.png') ?>" 
                width="500" height="139">
            </td>
        </tr>
        <tr class="content">
            <td class="left">&nbsp;</td>
            <td>
                <p>&nbsp;</p>
                
                <?= $content ?>
                
                <p>&nbsp;</p>
            </td>
            <td class="right">&nbsp;</td>
        </tr>
        <tr class="footer">
            <td colspan="3">
                <p>&nbsp;</p>
                <p>
                    Falls Sie keine weiteren Nachrichten mehr erhalten m??chten, k??nnen Sie sich
                    <a href="https://newsletter.girardi.ch/list/sign-off?email=<?= $this->params['email'] ?>">
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
                                src="<?= $message->embed('/home/girardi/www/yii/dev/yii2-newsletter-module/views/layouts/default/assets/macher.png') ?>" 
                                width="113" height="100">
                        </td>
                        <td class="right">&nbsp;
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
