<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pays';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="pay-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Pay', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'orderNumber',
            'orderId',
            'amount',
            [
                'header'=>'Статус',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) {

                    $orderStatus = array(
                        0 => 'Заказ зарегистрирован, но не оплачен',
                        1 => 'Предавторизованная сумма захолдирована (для двухстадийных платежей)',
                        2 => 'Проведена полная авторизация суммы заказа',
                        3 => 'Авторизация отменена',
                        4 => 'По транзакции была проведена операция возврата',
                        5 => 'Инициирована авторизация через ACS банка-эмитента',
                        6 => 'Авторизация отклонена',
                    );

                    $res = \app\controllers\PayController::getPayStatus($model->orderId);
                    return $orderStatus[$res['orderStatus']];

                }
            ],
            'errorCode',
            'errorMessage:ntext',
            //'datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
