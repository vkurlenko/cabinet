<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MailTpl */

$this->title = 'Создать шаблон';
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны писем', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mail-tpl-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
