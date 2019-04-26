<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fx_menu_product_content".
 *
 * @property int $ID
 * @property string $Name
 * @property string $Description
 * @property double $Price
 * @property int $IsPublish
 * @property int $SortID
 * @property int $ProductID
 */
class FxMenuProductContent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fx_menu_product_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'Description'], 'string'],
            [['Price'], 'number'],
            [['IsPublish', 'SortID', 'ProductID'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'Name' => 'Name',
            'Description' => 'Description',
            'Price' => 'Price',
            'IsPublish' => 'Is Publish',
            'SortID' => 'Sort ID',
            'ProductID' => 'Product ID',
        ];
    }
}
