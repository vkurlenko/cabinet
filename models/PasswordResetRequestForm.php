<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        /*$vars = [
            'link' => Html::a($link, $link)
        ];

        //
        $send = Yii::$app
            ->mailer
            ->compose(
            //['html' => 'payLink-html', 'text' => 'payLink-html'],
                ['html' => 'tpl', 'text' => 'tpl'],
                ['tpl_alias' => 'pay_link', 'vars' => $vars]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject('Ваша ссылка для оплаты заказа на сайте '.Yii::$app->params['mainDomain'])
            ->attachContent($blank_content, ['fileName' => $blank_filename, 'contentType' => 'application/pdf'])
            ->send();*/

        /*return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Восстановление пароля на сайте ' . Yii::$app->name)
            ->send();*/

        $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
        $vars = [
            'reset_link' => Html::a($resetLink, $resetLink),
            'user_name' => $user->username,
            'domain' => Yii::$app->params['mainDomain']
        ];

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'tpl', 'text' => 'tpl'],
                ['tpl_alias' => 'pwd_recovery', 'vars' => $vars]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Восстановление пароля на сайте ' . Yii::$app->name)
            ->send();
    }

}