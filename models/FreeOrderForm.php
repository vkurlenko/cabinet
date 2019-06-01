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

class FreeOrderForm extends Model
{
    public $id;
    public $deliv_date;
    public $description = 'test';
    public $address;

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
            [['images'], 'file', 'extensions' => 'png, jpg'],
        ];
    }

    public function uploadImages()
    {
        if ($this->validate()) {
            foreach($this->images as $file){
                $path = 'upload/store/' . $file->baseName . '.' . $file->extension;
                $file->saveAs($path);
                $this->attachImage($path);
                unlink($path);
            }
            return true;
        }
        else {
            return false;
        }

    }
}