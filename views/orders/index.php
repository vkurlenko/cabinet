<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = 'Список заказов';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
if(count($roles) == 1 && $roles['user']){
    echo \app\controllers\UserController::renderUserInfo(Yii::$app->user->getId());
    $this->title = 'Мои заказы';
}

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="orders-index">

    <h1><?= Html::encode($this->title) ?><span class="status"><?= Html::a('Создать новый заказ', ['create'], ['class' => 'garamond']) ?></span></h1>

    <!--<p>
        <?/*= Html::a('Создать новый заказ', ['create'], ['class' => 'btn btn-success']) */?>
    </p>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <? //debug(\app\controllers\OrdersController::getProducts());?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'label' => '№',
                'value' => function($data){
                    return Html::a($data->id, ['update', 'id' => $data->id ]);
                },
				'format' => 'html'				
            ],
			'order_date',
            [
                'attribute' => 'name',
                'value' => function($data){
                    $products = \app\controllers\OrdersController::getProducts();
                    return $products[$data->name]['name'];
                }
            ],
			'cost',
            [
                'attribute' => 'status',
                'value' => function($data){
                    $stats = \app\models\Orders::getStatus();
                    return $stats[$data->status];
                }
            ],
            /*[
                'attribute' => 'uid',
                'value' => function($data){
					$users = \app\controllers\OrdersController::getPersons('user');
                    return Html::a($users[$data->uid], ['user/update?id='.$data->uid]);
                },
				'format' => 'html'
            ],*/
			'deliv_name',
            [
                'attribute' => 'manager',
                'value' => function($data){
                    $users = \app\controllers\OrdersController::getPersons('manager');
                    return $users[$data->manager];
                }
            ],
            
            //'filling:ntext',
            //'description:ntext',
            //'deliv_date',
            //'address:ntext',
            //'cost',
            //'payed',
            //'order_date',
            //'update_date',
            //'manager',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
