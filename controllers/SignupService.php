<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 20.06.2019
 * Time: 10:59
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use app\models\SignupForm;
use app\models\AuthAssignment;


class SignupService
{
    public function signup(SignupForm $form, $roleName = null)
    {
       /* $user = new User();
        $user->username = $form->username;
        $user->generateAuthKey();
        $user->setPassword($form->password);
        $user->email = $form->email;
        $user->email_confirm_token = Yii::$app->security->generateRandomString();
        $user->status = User::STATUS_WAIT;

        if(!$user->save()){
            throw new \RuntimeException('Saving error.');
        }

        return $user;*/

        $user = new User();
        $user->username = $form->username;
        $user->email = $form->email;
        $user->email_confirm_token = Yii::$app->security->generateRandomString();
        $user->phone = $form->phone;
        //$user->setPassword($form->password);
        $user->password_hash = $form->password;
        $user->generateAuthKey();
        $user->status = User::STATUS_WAIT;

        if($user->save()){
            if(!$roleName){
                $role = new AuthAssignment();
                $role->user_id = $user->id;
                $role->item_name = 'user';
                $role->save(false);
            }
            return $user;
        }
        else{
            throw new \RuntimeException('Saving error.');
        }

        return $user;
    }


    public function sentEmailConfirm(User $user)
    {
        $email = $user->email;

        $sent = Yii::$app->mailer
            ->compose(
                ['html' => 'user-signup-confirm-html', 'text' => 'user-signup-confirm-html'],
                ['user' => $user])
            ->setTo($email)
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setSubject('Подтверждение регистрации на сайте '.Yii::$app->name)
            ->send();


        if (!$sent) {
            throw new \RuntimeException('Sending error.');
        }
    }


    public function confirmation($token)
    {
        if (empty($token)) {
            throw new \DomainException('Empty confirm token.');
        }

        $user = User::findOne(['email_confirm_token' => $token]);
        if (!$user) {
            throw new \DomainException('User is not found.');
        }

        $pwd = $user->password_hash;
        $user->email_confirm_token = null;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($user->password_hash);

        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }
        else{
            $vars = [
                'user_name' => $user->username,
                'user_login' => $user->email,
                'user_password' => $pwd,
                'domain' => Yii::$app->params['mainDomain']
            ];

            Yii::$app
                ->mailer
                ->compose(
                /* ['html' => 'signup-html', 'text' => 'signup-html'],
                 ['user' => $user]*/
                    ['html' => 'tpl', 'text' => 'tpl'],
                    ['tpl_alias' => 'new_cab', 'vars' => $vars]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($user->email)
                ->setSubject('Вы успешно зарегистрированы на сайте ' . Yii::$app->name)
                ->send();

            Yii::$app->session->setFlash('success', 'Вы успешно зарегистрированы!');

            return $user;
        }

        if (!Yii::$app->getUser()->login($user)){
            throw new \RuntimeException('Ошибка авторизации.');
        }
    }
}