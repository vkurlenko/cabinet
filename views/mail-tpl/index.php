<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MailTplSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Шаблоны писем';
$this->params['breadcrumbs'][] = $this->title;

//debug(\app\controllers\UserController::getManagersEmails());
?>
<div class="mail-tpl-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать шаблон', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'name',
                'value' => function($data){
                    return Html::a($data->name, ['/mail-tpl/update/?id='.$data->id]);
                },
                'format' => 'html'

            ],
            'alias',
            'subject',
            'tpl:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
