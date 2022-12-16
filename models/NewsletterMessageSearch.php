<?php

namespace schmauch\newsletter\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use schmauch\newsletter\models\NewsletterMessage;

/**
 * NewsletterMessageSearch represents the model behind the search form of `common\models\NewsletterMessage`.
 */
class NewsletterMessageSearch extends NewsletterMessage
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['id', 'blacklisted', 'mails_sent'], 'integer'],
            [['subject', 'template', 'send_date', 'send_time', 'completed_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = NewsletterMessage::find();

        // add conditions that should always apply here

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'send_date' => $this->send_date,
            'send_time' => $this->send_time,
            'mails_sent' => $this->mails_sent,
            'blacklisted' => $this->blacklisted,
            'completed_at' => $this->completed_at,
        ]);
        
        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'template', $this->template]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
