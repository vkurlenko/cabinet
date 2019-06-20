<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

function arrDropDownRoles(){
    $arr = [];
    $roles = \app\models\AuthItem::find()->where(['type' => 1])->asArray()->all();

    foreach($roles as $role){
        $arr[$role['name']] = $role['description'];
    }
    return $arr;
}

$role = \app\models\AuthAssignment::find()->where(['user_id' => $model->id])->asArray()->one();
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput() ?>

    <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->params['phoneMask'],]) ?>

    <?php
    // если пользователь == клиент, то скроем поля редактирования Активности и Роли
    if(Yii::$app->user->can('manager')):
    ?>
        <?= $form->field($model, 'email')->textInput() ?>
        <?= $form->field($model, 'status')->dropDownList([1 => 'активен', 0 => 'отключен'], ['options' => [$model->status => ['selected' => true]]]) ?>
        <?= $form->field($model, 'role')->dropDownList(arrDropDownRoles(), ['options' => [$role['item_name'] => ['selected' => true]]]) ?>
    <?php
    endif;
    ?>

    <?php
    $model->password_hash = '';
    ?>
    <?= $form->field($model, 'password_hash')->passwordInput()->hint('Чтобы изменить текущий пароль введите новый') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn ext-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
