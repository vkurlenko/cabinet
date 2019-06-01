<?php

use yii\helpers\Html;

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

<div class="password-reset">
    <p>Здравствуйте, <?= Html::encode($user->username) ?>!</p>
    <p>Ваша ссылка для восстановления пароля на сайте <?=Yii::$app->name?>:</p>
    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>