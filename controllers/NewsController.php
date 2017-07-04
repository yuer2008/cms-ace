<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Mber;
class NewsController extends Controller {
	public $layout = 'admin';
	public function actionIndex(){
		// $this->layout = 'admin';
		return $this->render('index', []);
	}
}
?>