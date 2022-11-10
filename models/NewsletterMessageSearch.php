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
            [['id', 'blacklisted'], 'integer'],
            [['subject', 'html_file', 'text_file', 'template', 'recipients_file', 'send_at', 'completed_at'], 'safe'],
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'send_at' => $this->send_at,
            'completed_at' => $this->completed_at,
            'blacklisted' => $this->blacklisted,
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'html_file', $this->html_file])
            ->andFilterWhere(['like', 'text_file', $this->text_file])
            ->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'recipients_file', $this->recipients_file]);

        return $dataProvider;
    }
}
