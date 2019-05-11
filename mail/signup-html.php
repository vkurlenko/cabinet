<?php

use yii\helpers\Html;

//$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

<div class="password-reset">
    <p>Вы зарегистрированы успешно <?= Html::encode($user->username) ?>,</p>
    <p>Follow the link below to reset your password:</p>

</div>