<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pay */

$this->title = 'Create Pay';
$this->params['breadcrumbs'][] = ['label' => 'Pays', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
