<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fx_menu_part".
 *
 * @property int $id
 * @property int $ParentID
 * @property string $Name
 * @property string $EnName
 * @property string $URLPart
 * @property string $Description
 * @property string $MetaTitle
 * @property string $MetaKeyWords
 * @property string $MetaDescription
 * @property string $EnDescription
 * @property string $EnMetaTitle
 * @property string $EnMetaKeyWords
 * @property string $EnMetaDescription
 * @property int $SortID
 * @property int $IsPublish
 * @property string $PartImg
 * @property int $MonToFri
 * @property int $ViewRestoran
 * @property int $ViewKonditerskaya
 * @property string $Link
 */
class FxMenuPart extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fx_menu_part';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ParentID', 'SortID', 'IsPublish', 'MonToFri', 'ViewRestoran', 'ViewKonditerskaya'], 'integer'],
            [['Name', 'EnName', 'URLPart', 'Description', 'MetaTitle', 'MetaKeyWords', 'MetaDescription', 'EnDescription', 'EnMetaTitle', 'EnMetaKeyWords', 'EnMetaDescription', 'PartImg', 'Link'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ParentID' => 'Parent ID',
            'Name' => 'Name',
            'EnName' => 'En Name',
            'URLPart' => 'Url Part',
            'Description' => 'Description',
            'MetaTitle' => 'Meta Title',
            'MetaKeyWords' => 'Meta Key Words',
            'MetaDescription' => 'Meta Description',
            'EnDescription' => 'En Description',
            'EnMetaTitle' => 'En Meta Title',
            'EnMetaKeyWords' => 'En Meta Key Words',
            'EnMetaDescription' => 'En Meta Description',
            'SortID' => 'Sort ID',
            'IsPublish' => 'Is Publish',
            'PartImg' => 'Part Img',
            'MonToFri' => 'Mon To Fri',
            'ViewRestoran' => 'View Restoran',
            'ViewKonditerskaya' => 'View Konditerskaya',
            'Link' => 'Link',
        ];
    }
}
