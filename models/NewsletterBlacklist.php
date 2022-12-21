<?php

namespace schmauch\newsletter\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "newsletter_blacklist".
 *
 * @property int $id
 * @property string $email
 * @property string $added_at
 */
class NewsletterBlacklist extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'newsletter_blacklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'added_at'], 'required'],
            [['added_at'], 'safe'],
            //[['added_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['email'], 'email'],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'E-Mail-Adresse',
            'added_at' => 'abgemeldet am',
        ];
    }
}
