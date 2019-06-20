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

?>

<div class="orders-nav">
    <?=Html::a('Текущие заказы', '?active&uid='.$uid, ['class' => getClass('active')])?>
    <?=Html::a('История заказов', '?complete&uid='.$uid, ['class' => getClass('complete')])?>

    <?php
    if(\app\controllers\UserController::isManager()){
        echo Html::a('Все заказы', '?all', ['class' => getClass('all')]);
    }
    ?>

    <div class="date-range">
        <?=Html::a('Выбрать за период', '#')?>
        <input type="text" name="dates" value="<?=Yii::$app->request->get('daterange') ? Yii::$app->request->get('daterange') : '';?>">
    </div>

</div>
