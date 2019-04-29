<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property int $id
 * @property int $order_id id заказа
 * @property string $datetime время изменения
 * @property int $uid id пользователя
 * @property int $old_status id старого статуса
 * @property int $new_status id нового статуса
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'datetime', 'uid', 'old_status', 'new_status'], 'required'],
            [['order_id', 'uid', 'old_status', 'new_status'], 'integer'],
            [['datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'datetime' => 'Datetime',
            'uid' => 'Uid',
            'old_status' => 'Old Status',
            'new_status' => 'New Status',
        ];
    }
}
