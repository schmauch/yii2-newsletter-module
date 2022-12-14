# yii2-newsletter-module
Newsletter module to create/edit messages, add recipients and send the newsletter using queue

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/):

Either run

```
composer require --prefer-dist schmauch/yii2-newsletter-module
```

or add

```json
"schmauch/yii2-newsletter-module": "*"
```

to the require section of your composer.json.

### Migrate database

```
./yii migrate/up --migrationPath="vendor/schmauch/yii2-newsletter-module/migrations/"
```

### Add module to config file

```
    'modules' => [
        'newsletter' => [
            'class' => schmauch\newsletter\Module::class,
            'defaultRoute' => 'default',
        ...
    ],
```

### Configure mailer

Make shure you have a working mailer configured that derives from `\yii\base\Mailer`.


### Configure console

Add module to console config file

```
    'modules' => [
        'newsletter' => schmauch\newsletter\Module::class,
        ...
    ],
```
and make it avaliabla in bootstrap

```
    'bootstrap' => [
        'newsletter',
        ...
    ],
```

Configure url manager


## Usage
