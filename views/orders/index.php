<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

use app\controllers\OrdersController;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//\app\controllers\OrdersController::setLog();

$this->title = 'Список заказов';
//echo 'client = '.\app\controllers\UserController::isClient();

if(\app\controllers\UserController::isClient()){
    $this->title = 'Мои заказы';

    // вывод данных клиента
    echo \app\controllers\UserController::renderUserInfo(Yii::$app->user->getId());
}

$this->params['breadcrumbs'][] = $this->title;

// статусы заказа


/* установим статус Выполнен */
\app\controllers\OrdersController::setOrderComplete();
/* /установим статус Выполнен */

?>
<div class="orders-index">

       <h1><?= Html::encode($this->title) ?>

        <?=$this->render('_search', ['model' => $searchModel]); ?>

       <?php
       $session = Yii::$app->session;
       if($session->get('fuid')):
       ?>
       <span class="status"><?= Html::a('Создать новый заказ', ['create'], ['class' => 'garamond']) ?></span>
       <?php
       endif;
       ?>


    </h1>


	
	<?php
    // панель Текущие заказы/История
	//if($isUser)
       echo $this->render('/blocks/orders_nav');

       //debug($dataProvider);
	?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {

            // если менеджер не назначен => заказ новый, выделим жирным
            if(in_array($model->status, [0, 10]) && !$model->manager)
                $class = 'new';
            return ['class' => $class];
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'label' => '№',
                'value' => function($data){
                    /*if(!\app\controllers\UserController::isClient())
                        return Html::a($data->id, ['view', 'id' => $data->id ]);
                    else*/
                        return $data->id;
                },
				'format' => 'html'				
            ],
            [
                'attribute' => 'order_date',
                //'label' => '№',
                'value' => function($data){
                    $data->order_date = \app\controllers\AppController::formatDate($data->order_date, true);

                    return $data->order_date;
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'name',
                'value' => function($data){

                    if(OrdersController::checkMyOrder($data->uid, $data->manager)){
                        return Html::a($data->name, ['view', 'id' => $data->id]);
                    }
                    else
                        return $data->name;


                        /*if (!\app\controllers\UserController::isClient())
                            return Html::a($data->name, ['view', 'id' => $data->id]);
                        else
                            return $data->name;*/

                },
				'format' => 'html'
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

                    $stat_style = [
                        '0' => 'success',
                        '1' => 'warning',
                        '2' => 'warning',
                        '3' => 'warning',
                        '4' => 'warning',
                        '5' => 'warning',
                        '6' => 'warning',
                        '7' => 'danger',

                        '10' => 'success',
                        '20' => 'primary',
                        '30' => 'default',
                        '40' => 'danger',
                        '50' => 'danger',
                    ];
                    return '<strong class="label label-'.$stat_style[$data->status].' label-status" >'.$stats[$data->status].'</strong>';
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
			//'deliv_name',
            [
                'attribute' => 'deliv_date',
                //'label' => '№',
                'value' => function($data){
                    $data->deliv_date = \app\controllers\AppController::formatDate($data->deliv_date);

                    return $data->deliv_date;
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'manager',
                'value' => function($data){
                    $users = \app\controllers\OrdersController::getPersons('manager');
                    return $users[$data->manager];
                }
            ],
            
            //'filling:ntext',
            //'description:ntext',

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
                        Url::to(['/orders/pdf', 'id' => $model->id]),
                        []
                        /*Url::to(['#', 'id' => $model->id]),
                        [
                            'data-id' => $model->id,
                            'data-pjax'=>true,
                            'action'=>Url::to(['cart/add']),
                            //'class'=>'btn btn-success gridview-add-to-cart',
                        ]*/
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

                        if(OrdersController::checkMyOrder($model->uid, $model->manager))
                            return Html::a($icon, $url, $options);
                        else
                            return '';
                    },
                ],

                'visibleButtons' => [
                    'view' => function($model){
                        return \Yii::$app->user->can('manager') && OrdersController::checkMyOrder($model->uid, $model->manager);
                    },
                    'update' => function($model){
                        return \Yii::$app->user->can('manager') && OrdersController::checkMyOrder($model->uid, $model->manager);
                    },
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

<?php
$search = Yii::$app->request->get('OrdersSearch')['name'];
$script = <<< JS
    $('.grid-view table tbody').highlight('$search');
JS;
//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_END);