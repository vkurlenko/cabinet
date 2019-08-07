<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 18.05.2019
 * Time: 16:57
 */

namespace app\models;

use app\controllers\UserController;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use rico\yii2images\models\Image;

class FreeOrderForm extends Model
{
    public $id = '';
    public $uid;
    public $deliv_date;
    public $deliv_name;
    public $deliv_phone;
    public $email;
    public $description = '';
    public $address;
    public $primaryKey = "id";

    public $images;

    /*public static function tableName()
    {
        return 'orders';
    }*/

    public function behaviors()
    {
        return [
            'images' => [
                'class' => 'rico\yii2images\behaviors\ImageBehave',
            ]
        ];
    }

    public function rules() {
        return [
            [['deliv_date', 'deliv_name', 'deliv_phone', 'email', 'description', 'address'], 'required'],
            [['deliv_date', 'deliv_name', 'email', 'description', 'address'], 'string'],
            [['uid'], 'string'],
            ['deliv_phone', 'match', 'pattern' => '/^\+7[0-9]{10}$/', 'message' => 'Введите номер в формате +71234567890'],
            [['images'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 3],
        ];
    }

    public function attributeLabels()
    {
        return [
            'uid' => '',
            'description' => 'Опишите желаемый торт',
            'deliv_date' => 'Дата доставки',
            'deliv_name' => 'Заказчик',
            'deliv_phone' => 'Телефон',
            'email' => 'Email',
            'address' => 'Информация о доставке',
            'images' => 'Прикрепите фотографии'
        ];
    }

    public function getClient()
    {
        $session = Yii::$app->session;
        $user = null;

        $user_id = $session->get('fuid') ? $session->get('fuid') : (Yii::$app->user->getId() ? Yii::$app->user->getId() : null);

        if($user_id){
            $user = UserController::getUser($user_id);
        }

        return $user;
    }
}