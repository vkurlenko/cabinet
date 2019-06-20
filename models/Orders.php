<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use rico\yii2images\models\Image;

/**
 * This is the model class for table "orders".
 *
 * @property int $id id (номер) заказа
 * @property int $uid id клиента
 * @property string $name название торта
 * @property string $filling начинка
 * @property string $description описание заказа
 * @property string $deliv_date дата доставки
 * @property string $address адрес доставки
 * @property int $cost стоимость
 * @property int $payed оплачено
 * @property string $order_date дата заказа
 * @property string $update_date дата изменения заказа
 * @property int $manager менеджер
 * @property int $status статус
 */
class Orders extends \yii\db\ActiveRecord
{
    public $images;
    public $fill = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    public function behaviors()
    {
        return [
            'images' => [
                'class' => 'rico\yii2images\behaviors\ImageBehave',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'name'], 'required'],
            [['uid', 'cost', 'payed', 'manager', 'status', 'tasting_set'], 'integer'],
            [['filling', 'description', 'address', 'deliv_name'], 'string'],
            ['deliv_phone', 'match', 'pattern' => '/^\+7[0-9]{10}$/', 'message' => 'Введите номер в формате +71234567890'],
            [['deliv_date', 'order_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['images'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'uid' => 'Клиент',
            'name' => 'Название торта',
            'filling' => 'Начинка',
            'tasting_set' => 'Дегустационный сет',
            'description' => 'Описание заказа',
            'deliv_date' => 'Дата доставки',
			'deliv_name' => 'Заказчик',
			'deliv_phone' => 'Телефон',
            'address' => 'Информация о доставке',
            'cost' => 'Стоимость заказа',
            'payed' => 'Ранее оплачено',
            'order_date' => 'Дата заказа',
            'update_date' => 'Дата изменения заказа',
            'manager' => 'Менеджер',
            'status' => 'Статус',

            'images' => 'Прикрепите фотографии'
        ];
    }

    /**
     * статусы заказа
    */
    public function getStatus()
    {
        return [
            0 => 'Новый',               // только что созданный заказ
            1 => 'Выставлен счет',      // ссылка на оплату отправлена клиенту
            2 => 'Ожидает подтверждения',
            3 => 'Ожидает оплаты',
            4 => 'Отправлен в банк',    // клиент прошел по ссылке на оплату в банк
            5 => 'Оплачен',             // если произведена оплата в обход банка ИЛИ банк подтвердил оплату
            6 => 'Оплата при доставке', // если оплата будет при доставке
            7 => 'Редактируется',       // +
						
			10 => 'Создан',             // = новый
			20 => 'Выполнен',           // заказ автоматически переходит в состояние выполнен в 23.30 в день доставки
			30 => 'Удален',             // можно удалить в любом статусе до отправлен в банк, если оплачено 0
            40 => 'Перезаказ',          // изменение номера заказа копированием в новый заказ
        ];
    }

    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public function uploadImages()
    {
        //debug($this->images); die;
        //if ($this->validate()) {
            //echo 'id='.$this->id;
            foreach($this->images as $file){
                $path = 'upload/store/' . $file->baseName . '.' . $file->extension;
                $file->saveAs($path);
                $this->attachImage($path);
                unlink($path);
            }
            return true;
        /*}
        else {
            return false;
        }*/

    }

    public function beforeSave($insert)
    {
        // если $insert== true значит, метод вызвался при создании записи, иначе при обновлении
        $saveContinue = parent::beforeSave($insert); // если $saveContinue == false, сохранение будет отменено
        if($insert)
        {
            // здесь, например, можно скорректировать сохраняемые данных
        }

        if($_POST['Orders']['fill']){
            $this->filling = implode('|', $_POST['Orders']['fill']);
        }

        //echo 'beforesave'; die;

        return $saveContinue ;
    }


}
