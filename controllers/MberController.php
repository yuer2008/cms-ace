<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Mber;
use yii\data\Pagination;

class MberController extends Controller {
	public $layout = 'admin';
	public function actionIndex(){
		// $this->layout = 'admin';
		$data = Mber::find();
		$pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => 15]);
		$mber = $data->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('index', [
			'pages' => $pages,
			'mber' => $mber
			]);
	}
}
?>