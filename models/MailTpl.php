<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mail_tpl".
 *
 * @property int $id
 * @property string $name имя шаблона
 * @property string $alias
 * @property string $subject - тема
 * @property string $tpl код шаблона
 */
class MailTpl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mail_tpl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'alias', 'tpl'], 'required'],
            [['subject', 'tpl'], 'string'],
            [['name', 'alias'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название шаблона',
            'alias' => 'Alias',
            'subject' => 'Тема',
            'tpl' => 'Код шаблона',
        ];
    }
}
