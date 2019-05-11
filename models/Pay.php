<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pay".
 *
 * @property int $id id транзакции
 * @property int $orderNumber id заказа на сайте
 * @property int $orderId id заказа в платежной системе
 * @property int $errorCode код ошибки
 * @property string $errorMessage текст ошибки
 * @property string $datetime дата и время
 */
class Pay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orderNumber', 'datetime'], 'required'],
            [['orderNumber', 'orderId', 'errorCode'], 'integer'],
            [['errorMessage'], 'string'],
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
            'orderNumber' => 'Order Number',
            'orderId' => 'Order ID',
            'errorCode' => 'Error Code',
            'errorMessage' => 'Error Message',
            'datetime' => 'Datetime',
        ];
    }
}
