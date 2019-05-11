<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{

    public $roleName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['phone'], 'string'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'phone', 'roleName'], 'safe'],
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
        $query = User::find();
        $query->joinWith(['role']);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['roleName'] = [
            'asc' => [AuthAssignment::tableName().'.item_name' => SORT_ASC],
            'desc' => [AuthAssignment::tableName().'.item_name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if(isset($params['active'])){
            // только активные клиенты (у кого заказы со статусом < 20, т.е. до "Выполнен")
            $active_users = Orders::find()->select('uid')->where(['<', 'status', 20])->distinct()->asArray()->all();

            $arr = [];
            foreach($active_users as $u)
               $arr[] = $u['uid'];

            $query->andFilterWhere(['in', 'id', $arr]);
        }
        else
            // все клиенты
            $query->andFilterWhere(['id' => $this->id]);


        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        // админ может видеть список всех
        if(Yii::$app->user->can('admin')){
            if($params['role'])
                $query->andFilterWhere(['like', AuthAssignment::tableName().'.item_name', $params['role']]);
            else
                $query->andFilterWhere(['like', AuthAssignment::tableName().'.item_name', $this->roleName]);
        }
        // директор может видеть список всех
        elseif(Yii::$app->user->can('director')){
            if($params['role'])
                $query->andFilterWhere(['like', AuthAssignment::tableName().'.item_name', $params['role']]);
            else
                $query->andFilterWhere(['like', AuthAssignment::tableName().'.item_name', $this->roleName]);
        }
        // менеджер может видеть список только клиентов
        elseif(Yii::$app->user->can('manager')){
            $query->andFilterWhere(['like', AuthAssignment::tableName().'.item_name', 'user']);
        }

        return $dataProvider;
    }
}
