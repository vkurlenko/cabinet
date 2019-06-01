<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{

    public $username;
    public $email;
    public $phone;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Это имя уже используется'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот email уже используется'],
            ['phone', 'required'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup($roleName = null)
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        if($user->save()){
            if(!$roleName){
                $role = new AuthAssignment();
                $role->user_id = $user->id;
                $role->item_name = 'user';
                $role->save(false);
            }

            $vars = [
                'user_name' => $user->username,
                'user_login' => $user->email,
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
                ->setTo($this->email)
                ->setSubject('Вы успешно зарегистрированы на сайте ' . Yii::$app->name)
                ->send();

            Yii::$app->session->setFlash('success', 'Вы успешно зарегистрированы!');

            return $user;
        }
        else{
            Yii::$app->session->setFlash('danger', 'Ошибка регистрации');

            return null;
        }
    }

}