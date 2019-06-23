<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 18.05.2019
 * Time: 16:57
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use rico\yii2images\models\Image;

class FreeOrderForm extends Model
{
    public $id = 777;
    public $deliv_date;
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
            [['deliv_date', 'description', 'address'], 'required'],
            [['deliv_date', 'description', 'address'], 'string'],
            [['images'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 3],
        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => 'Опишите желаемый торт',
            'deliv_date' => 'Дата доставки',
            'address' => 'Информация о доставке',
            'images' => 'Прикрепите фотографии'
        ];
    }





}