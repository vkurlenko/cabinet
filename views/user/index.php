<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\controllers\UserController;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

//debug($this->params);
echo date('d-m-Y', 1456224205);
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Новый пользователь', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],
            'id',
            'username',
            [
                'attribute' => 'role.item_name',
                'label' => 'Роль',
                'value' => function($data){
                    return UserController::getRoleName($data->role->item_name);
                }
            ],
            'email:email',
            'phone',
            [
                'attribute' => 'status',
                'value' => function($data){
                    return $data->status ? 'активен' : 'отключен';
                    //return UserController::getRoleName($data->role->item_name);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'created_at',
                'value' => function($data){
                    return date('d-m-Y', $data->created_at);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($data){
                    return date('d-m-Y', $data->updated_at);
                },
                'format' => 'html'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
