<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fx_menu_product".
 *
 * @property int $id
 * @property int $SortID
 * @property int $PartID
 * @property string $Name
 * @property string $EnName
 * @property double $Price
 * @property string $URLPart
 * @property string $DescriptionShort
 * @property string $DescriptionLong
 * @property string $EnDescriptionShort
 * @property string $EnDescriptionLong
 * @property int $IsPublish
 * @property string $MetaTitle
 * @property string $MetaDescription
 * @property string $MetaKeyWords
 * @property string $EnMetaTitle
 * @property string $EnMetaDescription
 * @property string $EnMetaKeyWords
 * @property string $ProductImgSmall
 * @property string $ProductImgLarge
 * @property string $Weight
 * @property int $InOrder
 * @property int $WeekNumber
 * @property int $DayNumber
 * @property double $MinWeight
 * @property double $MaxWeight
 * @property double $DecorPrice
 * @property string $Nachinki
 * @property int $TortType
 * @property double $PirojnoePrice
 * @property int $FotoGallery
 * @property string $SWF
 * @property string $ProductImgLarge2
 * @property string $WeightMinimaize
 */
class FxMenuProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fx_menu_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['SortID', 'PartID', 'IsPublish', 'InOrder', 'WeekNumber', 'DayNumber', 'TortType', 'FotoGallery'], 'integer'],
            [['Name', 'EnName', 'URLPart', 'DescriptionShort', 'DescriptionLong', 'EnDescriptionShort', 'EnDescriptionLong', 'MetaTitle', 'MetaDescription', 'MetaKeyWords', 'EnMetaTitle', 'EnMetaDescription', 'EnMetaKeyWords', 'ProductImgSmall', 'ProductImgLarge', 'Weight', 'Nachinki', 'SWF', 'ProductImgLarge2', 'WeightMinimaize'], 'string'],
            [['Price', 'MinWeight', 'MaxWeight', 'DecorPrice', 'PirojnoePrice'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'SortID' => 'Sort ID',
            'PartID' => 'Part ID',
            'Name' => 'Name',
            'EnName' => 'En Name',
            'Price' => 'Price',
            'URLPart' => 'Url Part',
            'DescriptionShort' => 'Description Short',
            'DescriptionLong' => 'Description Long',
            'EnDescriptionShort' => 'En Description Short',
            'EnDescriptionLong' => 'En Description Long',
            'IsPublish' => 'Is Publish',
            'MetaTitle' => 'Meta Title',
            'MetaDescription' => 'Meta Description',
            'MetaKeyWords' => 'Meta Key Words',
            'EnMetaTitle' => 'En Meta Title',
            'EnMetaDescription' => 'En Meta Description',
            'EnMetaKeyWords' => 'En Meta Key Words',
            'ProductImgSmall' => 'Product Img Small',
            'ProductImgLarge' => 'Product Img Large',
            'Weight' => 'Weight',
            'InOrder' => 'In Order',
            'WeekNumber' => 'Week Number',
            'DayNumber' => 'Day Number',
            'MinWeight' => 'Min Weight',
            'MaxWeight' => 'Max Weight',
            'DecorPrice' => 'Decor Price',
            'Nachinki' => 'Nachinki',
            'TortType' => 'Tort Type',
            'PirojnoePrice' => 'Pirojnoe Price',
            'FotoGallery' => 'Foto Gallery',
            'SWF' => 'Swf',
            'ProductImgLarge2' => 'Product Img Large2',
            'WeightMinimaize' => 'Weight Minimaize',
        ];
    }


}
