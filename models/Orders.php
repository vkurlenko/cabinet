<?php

namespace app\models;

use Yii;

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
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'name', 'status'], 'required'],
            [['uid', 'cost', 'payed', 'manager', 'status', 'tasting_set'], 'integer'],
            [['filling', 'description', 'address', 'deliv_name', 'deliv_phone'], 'string'],
            [['deliv_date', 'order_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'uid' => 'Заказчик',
            'name' => 'Название торта',
            'filling' => 'Начинка',
            'tasting_set' => 'Дегустационный сет',
            'description' => 'Описание заказа',
            'deliv_date' => 'Дата доставки',
			'deliv_name' => 'Клиент',
			'deliv_phone' => 'Телефон',
            'address' => 'Адрес доставки',
            'cost' => 'Стоимость заказа',
            'payed' => 'Ранее оплачено',
            'order_date' => 'Дата заказа',
            'update_date' => 'Дата изменения заказа',
            'manager' => 'Менеджер',
            'status' => 'Статус',
        ];
    }

    /**
     * статусы заказа
    */
    public function getStatus()
    {
        return [
            0 => 'Нет статуса',
            1 => 'Выставлен счет',
            2 => 'Ожидает подтверждения',
            3 => 'Ожидает оплаты',
            4 => 'Отправлен в банк',
            5 => 'Оплачен',
            6 => 'Оплата при доставке',
            7 => 'Редактируется'
        ];
    }

    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }


}
