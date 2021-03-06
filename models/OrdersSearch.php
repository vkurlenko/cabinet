<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orders;
use app\controllers\UserController;

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
            'sort'=>[
                'defaultOrder'=>[
                    'id'=>SORT_DESC,
                    'status' => SORT_ASC,
                    'manager'=>SORT_ASC,
                ]
            ]
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

        if(isset($params['fuid'])){
            UserController::actionSetFakeUid($params['fuid']);
        }

        // если в GET пришел id клиента, то выберем заказ только этого клиента
        // (работает только для manager+)
        if(isset($params['uid'])){
            $uid = $params['uid'];
        }
        else
            $uid = $this->uid;
		

        if($role['user']){
            $uid = Yii::$app->user->getId();
        }
		
		// текущие/история заказов
		if(isset($params['complete']))
			$status = ['in', 'status', [20, 30]];
		else
			$status = ['not in', 'status', [20, 30]];

        // выбор диапазона дат
        if(isset($params['daterange'])){
            $daterange = trim($params['daterange']);
            if($daterange != ''){
                $arr = explode(' - ', $daterange);
                if(count($arr) == 2){
                    $date_start = $arr[0];
                    $date_end = $arr[1].' 23:59:59';

                    $between = ['between', 'order_date', $date_start, $date_end];
                }
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'uid' => $uid,
            'deliv_date' => $this->deliv_date,
			'deliv_name' => $this->deliv_name,
			'deliv_phone' => $this->deliv_phone,
            'cost' => $this->cost,
            'payed' => $this->payed,
            //'order_date' => $this->order_date,
            'update_date' => $this->update_date,
            //'manager' => $manager,
            //'status' => $status //$this->status,
        ]);

        //$query->andFilterWhere(['like', 'name', $this->name])
        $query->andFilterWhere(['like', 'filling', $this->filling])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'address', $this->address])
			//->andFilterWhere($status)
            //->andFilterWhere($manager)
            ->andFilterWhere($between ? $between : [$this->order_date]);

        // если не задана строка поиска, то выводим только определенный статус
        if(!$params['OrdersSearch']['name']){
            $query->andFilterWhere($status);
        }

        /*if($role['user']){
            $query->andFilterWhere([
                'manager' => $this->manager
            ]);
        }
        else*/
        if($role['manager'] && !isset($params['all'])){
            $query->andFilterWhere(['in', 'manager', [0,  Yii::$app->user->getId()]]);
        }
        else{
            $query->andFilterWhere([
                'manager' => $this->manager
            ]);
        }

        /* простой поиск по имени клиента, номеру заказа, названию торта */
        if($params['OrdersSearch']['name']){

            // строка поиска
            $search_string = $params['OrdersSearch']['name'];

            // дополнительное условие для выборки на основании строки поиска
            $query->andFilterWhere($this->searchByString($search_string));
        }
        /* /простой поиск по имени клиента, номеру заказа, названию торта */

        return $dataProvider;
    }


    /**
     * Условие для поиска по строке
     *
     * @param $string - поисковый запрос
     *
     * @return array - условие выборки по запросу
     */
    private function searchByString($string)
    {
        $ids = [];
        $users = [];

        $arr = User::find()
                ->select(['id'])
                ->where(['like', 'username', $string])
                ->asArray()
                ->indexBy('id')
                ->all();

        if($arr){
            foreach($arr as $k => $v)
                $users[] = $k;
        }

        $query2 = Orders::find()
            ->where(['like', 'name', $string])
            ->orWhere(['like',   'id',  $string])
            ->orWhere(['in',   'uid',  $users])
            ->asArray()
            ->indexBy('id')
            ->all();

        foreach($query2 as $k => $v){
            $ids[] = $k;
        }

        $cond = ['in', 'id', $ids];

        return $cond;
    }


}
