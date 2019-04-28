<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orders;

/**
 * OrdersSearch represents the model behind the search form of `app\models\Orders`.
 */
class OrdersSearch extends Orders
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'cost', 'payed', 'manager', 'status'], 'integer'],
            [['name', 'filling', 'description', 'deliv_date', 'address', 'order_date', 'update_date'], 'safe'],
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
        $query = Orders::find();

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

        // проверим роль пользователя и для КЛИЕНТА выведем только его заказы,
        // а для остальных ролей - все заказы
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        if($role['user'])
            $uid = Yii::$app->user->getId();
        else
            $uid = $this->uid;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'uid' => $uid,
            'deliv_date' => $this->deliv_date,
			'deliv_name' => $this->deliv_name,
			'deliv_phone' => $this->deliv_phone,
            'cost' => $this->cost,
            'payed' => $this->payed,
            'order_date' => $this->order_date,
            'update_date' => $this->update_date,
            'manager' => $this->manager,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'filling', $this->filling])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}
