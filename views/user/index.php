<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\controllers\UserController;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/*$this->title = UserController::isManager() ? 'Клиенты' : 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

$newUserTitle = UserController::isManager() ? 'Клиент' : 'Пользователь';

$this->title = UserController::isDirector() ? 'Клиенты' : 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

$newUserTitle = UserController::isDirector() ? 'Клиент' : 'Пользователь';*/


if(UserController::isManager() || UserController::isDirector()){
    $newUserTitle = 'Клиент';
    $this->title = 'Клиенты';
}
else{
    $newUserTitle = 'Пользователь';
    $this->title = 'Пользователи';
}

?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?><span class="status"><?= Html::a('Новый '.$newUserTitle, ['create'], ['class' => 'garamond']) ?></span></h1>

    <!--<p>
        <?/*= Html::a('Новый пользователь', ['create'], ['class' => 'btn btn-success']) */?>
    </p>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    // панель Текущие заказы/История
    if(!UserController::isClient())
        echo $this->render('/blocks/users_nav');
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],
            'id',
            //'username',
			[
                'attribute' => 'username',
                //'label' => 'Роль',
                'value' => function($data){
                    //return Html::a($data->username, ['user/update/?id='.$data->id]);
                    return Html::a($data->username, ['/orders/index/?uid='.$data->id]);
                },
				'format' => 'html'
 				
            ],
            [
                'attribute' => 'role.item_name',
                'label' => 'Роль',
                'value' => function($data){
                    return UserController::getRoleName($data->role->item_name);
                }
            ],
            'email',
            'phone',
            [
                'attribute' => 'status',
                'value' => function($data){
                    $status = '';
                    switch ($data->status){
                        case 0: $status = 'отключен';
                            break;
                        case 1: $status = 'активен';
                            break;
                        case 5: $status = 'не подтвержден';
                            break;
                    }
                    //return $data->status ? 'активен' : 'отключен';
                    return $status;
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'created_at',
                'value' => function($data){
                    return date('d.m.Y', $data->created_at);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($data){
                    return date('d.m.Y', $data->updated_at);
                },
                'format' => 'html'
            ],
            [
                'header'=>'',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) {
                    if(Yii::$app->request->get('role') == 'user')
                        //return Html::a('заказ от имени клиента', Url::to(['/orders/create', 'uid' => $model->id]), ['style' => 'color: #c33']);
                        return Html::a('заказ от имени клиента', Url::to(['/orders/index', 'uid' => $model->id, 'fuid' => $model->id ]), ['style' => 'color: #c33']);
                    else
                        return '';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
