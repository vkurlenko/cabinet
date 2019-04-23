<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- Регистрация -->
<div class="row">
    <div class="site-signup col-md-6">
        <h1><?= Html::encode('Регистрация') ?></h1>
       <!-- <p>Please fill out the following fields to signup:</p>-->

            <div class="">
                <?php $form = ActiveForm::begin([
                        'id' => 'form-signup',
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'template' => "<div class=\"col-md-3\">{label}</div><div class=\"col-md-9\">{input}</div><br><div class=\"col-md-offset-4\">{error}</div>",
                            'labelOptions' => ['class' => ' control-label'],
                        ],
                        'action' => '/site/signup'
                ]); ?>

                <?= $form->field($signupModel, 'username')->textInput(['autofocus' => true])->label('Ваше имя') ?>
                <?= $form->field($signupModel, 'email')->label('E-mail') ?>
                <?= $form->field($signupModel, 'phone')->label('Телефон') ?>
                <?= $form->field($signupModel, 'password')->passwordInput()->label('Введите пароль') ?>
                <div class="form-group">
                    <div class="" style="text-align: center">
                    <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>

            </div>

    </div>
<!-- /Регистрация -->

<!-- Авторизация -->
    <div class="site-login  col-md-6">
        <h1><?= Html::encode($this->title) ?></h1>

        <!--<p>Please fill out the following fields to login:</p>-->

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "<div class=\"col-md-3\">{label}</div><div class=\"col-md-9\">{input}</div><br><div class=\"col-md-offset-4\">{error}</div>",
                'labelOptions' => ['class' => ' control-label'],
            ],
        ]); ?>

            <?/*= $form->field($model, 'username')->textInput(['autofocus' => true]) */?>
            <?= $form->field($loginModel, 'email')->textInput(['autofocus' => true])->label('E-mail (логин)') ?>
            <?= $form->field($loginModel, 'password')->passwordInput()->label('Пароль') ?>
            <?= $form->field($loginModel, 'rememberMe')->checkbox([
                'template' => "<div class=\"col-md-offset-4\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ])->label('Запомнить меня') ?>

        <div>
            <?= Html::a('Восстановить пароль', ['site/request-password-reset']) ?>
        </div>

        <div class="form-group">
            <div class="" style="text-align: center">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<!-- /Авторизация -->
</div>


