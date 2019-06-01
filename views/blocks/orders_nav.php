<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 30.04.2019
 * Time: 13:49
 */
use yii\helpers\Html;

function getClass($p)
{
    $get = Yii::$app->request->get();

    if(isset($get[$p]) ){
        return 'active';
    }
    elseif(!$get && $p == 'active')
        return 'active';
}

$uid = Yii::$app->request->get('uid') ? Yii::$app->request->get('uid') : '';

//debug(Yii::$app->request->get('uid'));
?>

<div class="orders-nav">
    <?=Html::a('Текущие заказы', '?active&uid='.$uid, ['class' => getClass('active')])?>
    <?=Html::a('История заказов', '?complete&uid='.$uid, ['class' => getClass('complete')])?>
</div>
