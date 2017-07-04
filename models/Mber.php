<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
class Mber extends ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%mber}}';
	}
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [];
	}
	/**
	 * @inheritdoc
	 */
	public function attributeLabels(){
		return [
			'id' => 'ID',
			"username" => '用户名',
			"email" => "邮箱",
			"mobile" => "手机号"
		];
	}

}
?>