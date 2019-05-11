<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pay;

/**
 * PaySearch represents the model behind the search form of `app\models\Pay`.
 */
class PaySearch extends Pay
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'orderNumber', 'orderId', 'errorCode'], 'integer'],
            [['errorMessage', 'datetime'], 'safe'],
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
        $query = Pay::find();

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
            'orderNumber' => $this->orderNumber,
            'orderId' => $this->orderId,
            'errorCode' => $this->errorCode,
            'datetime' => $this->datetime,
        ]);

        $query->andFilterWhere(['like', 'errorMessage', $this->errorMessage]);

        return $dataProvider;
    }
}
