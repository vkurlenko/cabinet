<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        Ошибка оплаты. Обратитесь к менеджеру
        <?/*= nl2br(Html::encode($msg)) */?>
    </div>



</div>