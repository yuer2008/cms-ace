<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%update}}".
 *
 * @property integer $ikey
 * @property string $up_name
 * @property string $up_path
 * @property integer $up_time
 * @property string $up_admin
 * @property string $up_desc
 * @property integer $up_istongbu
 * @property integer $up_tongbutime
 * @property integer $up_status
 */
class Update extends \yii\db\ActiveRecord
{

    public $verifycode;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%update}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ["up_type","required","message"=>"更新类型必填"],
            ["up_name","required","message"=>"更新记录名必填"],
            ["up_desc","required","message"=>"请输入更新内容"],
            [['up_time', 'up_istongbu', 'up_tongbutime'], 'integer'],
            [['up_desc'], 'string'],
            [['up_name', 'up_path', 'up_admin'], 'string', 'max' => 110],
            ["verifycode","captcha","message"=>"请输入正确的验证码"],
            ["up_path","required","message"=>"请上传更新附件"],
            ["up_path","file","message"=>"请上传更新附件"],
            ["up_version","required","message"=>"请填入版本号"],
        ];
    }

    public function beforeSave($insert){
        if(parent::beforeSave($insert)){
            if($insert){
                $this->up_time=time();
                $this->up_admin = Yii::$app->user->identity->username;
                $this->up_istongbu = 0;
                $this->up_status=1;
                return true;
            }
        }else{
                return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ikey' => 'Ikey',
            'up_name' => 'Up Name',
            'up_path' => 'Up Path',
            'up_time' => 'Up Time',
            'up_admin' => 'Up Admin',
            'up_desc' => 'Up Desc',
            'up_istongbu' => 'Up Istongbu',
            'up_tongbutime' => 'Up Tongbutime',
        ];
    }
}
