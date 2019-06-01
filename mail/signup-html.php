<?php

use yii\helpers\Html;

?>

<div class="password-reset">
    <p><?= Html::encode($user->username) ?>, Вы успешно зарегистрированы на сайте <?=Html::a(Yii::$app->params['mainDomain'], Yii::$app->params['mainDomain']);?>. </p>
    <p>Ваши реквизиты для <?=Html::a('входа', Yii::$app->params['subDomain'].'/login');?>:</p>
    <p>Логин: <?= Html::encode($user->email) ?></p>

</div>