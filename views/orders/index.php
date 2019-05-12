<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

//debug(Yii::$app->request->get());


/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//\app\controllers\OrdersController::setLog();

$this->title = 'Список заказов';


if(\app\controllers\UserController::isClient()){
    $this->title = 'Мои заказы';

    // вывод данных клиента
    echo \app\controllers\UserController::renderUserInfo(Yii::$app->user->getId());
}

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="orders-index">

    <h1><?= Html::encode($this->title) ?>
    <?php
    if(Yii::$app->user->can('user')):
    ?>
    <span class="status"><?= Html::a('Создать новый заказ', ['create'], ['class' => 'garamond']) ?></span>
    <?php
    endif;
    ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	
	<?php
    // панель Текущие заказы/История
	//if($isUser)
       echo $this->render('/blocks/orders_nav');
	?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'label' => '№',
                'value' => function($data){
                    if(!\app\controllers\UserController::isClient())
                        return Html::a($data->id, ['view', 'id' => $data->id ]);
                    else
                        return $data->id;
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
            [
                'attribute' => 'cost',
                'value' => function($data){
                    $sum = \app\controllers\OrdersController::getOrderCost($data->id);
                    return $sum ? $sum : 'не определена';
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    $stats = \app\models\Orders::getStatus();
                    return '<strong>'.$stats[$data->status].'</strong>';
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'uid',
                'value' => function($data){
					$users = \app\controllers\OrdersController::getPersons('user');
                    return Html::a($users[$data->uid], ['user/update?id='.$data->uid]);
                },
                'visible'=> $isUser ? false : true,
				'format' => 'html'
            ],
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

            [
                'header'=>'PDF',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) {
                    return Html::a(
                        Html::img('/img/pdf.png'),
                        Url::to(['#', 'id' => $model->id]),
                        [
                            'data-id' => $model->id,
                            'data-pjax'=>true,
                            'action'=>Url::to(['cart/add']),
                            //'class'=>'btn btn-success gridview-add-to-cart',
                        ]
                    );
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'delete' => function ($url, $model, $key){
                        $icon = Html::img('/img/delete.png');
                        $url = Url::to(['/orders/delete', 'id' => $key]);
                        $options = [
                            'data-pjax' => '0',
                            'data-method' => 'post',
                            'data-confirm' => 'Удалить заказ?',
                            'id' => $key,
                            'class' => 'icon-delete'
                        ];

                        return Html::a($icon, $url, $options);
                    },
                ],
                'visibleButtons' => [
                    'view' => \Yii::$app->user->can('manager'),
                    'update' => \Yii::$app->user->can('manager'),
                    'delete' => function($model){
                            if(\Yii::$app->user->can('user') && $model->status == 0 )
                                return true;
                            else
                                return false;
                    }
                ]

            ]
        ],
    ]); ?>


</div>
