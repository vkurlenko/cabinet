<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hash".
 *
 * @property int $id
 * @property int $order_id id заказа на сайте
 * @property string $hash hash для ссылки на оплату
 * @property string $hash_datetime время генерации hash
 */
class Hash extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hash';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'hash', 'hash_datetime'], 'required'],
            [['order_id'], 'integer'],
            [['hash_datetime'], 'safe'],
            [['hash'], 'string', 'max' => 255],
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
            'hash' => 'Hash',
            'hash_datetime' => 'Hash Datetime',
        ];
    }
}
