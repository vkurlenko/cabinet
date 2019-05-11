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
?>

<div class="orders-nav">
    <?=Html::a('Все клиенты', '?all', ['class' => getClass('all')])?>
    <?=Html::a('Активные клиенты', '?active', ['class' => getClass('active')])?>
</div>
