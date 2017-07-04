<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\swiftmailer\Message;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\HeaderCollection;
use app\common\Ftp;

class SiteController extends Controller
{
    //public $layout = null;
    public $enableCsrfValidation    =   false;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user'=>"user",
                'only' =>  ['logout','addupdate','updatelists','main'],
                'rules' => [
                    [
                        'actions' => ['logout','addupdate','updatelists','main'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'maxLength'=>4,
                'minLength'=>4,
            ],
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "",//图片访问路径前缀
                    "imagePathFormat" => "/backend/web/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}" //上传保存路径
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        if(!Yii::$app->user->isGuest){
            return $this->redirect(["site/main"]);
        }
            
        $this->layout = 'admin';
        $model = new \app\models\LoginForm();
        if(Yii::$app->request->post()){
            //var_dump(Yii::$app->request->post());
            $model->load(Yii::$app->request->post());
            if(Yii::$app->request->post("remember")!=="on"){
                $model->rememberMe=false;
            }
            //var_dump($model->getUser());
            if($model->validate()&&$model->login()){
                //var_dump(Yii::$app->user);
                //var_dump($model);die;
                return $this->redirect(["site/main"]);
            }
        }
        return $this->render('index',['model'=>$model]);
    }

    public function actionMain(){
        $this->layout   =   "main";

        $model  =   \app\models\Update::find()->where(["up_status"=>1])->orderBy("up_time desc")->one();
        return $this->render("main",["model"=>$model]);
    }

    public function actionAddupdate(){
        $this->layout   =       "main";
        $model           =       new \app\models\Update();
        $msg            =       "";
        if(Yii::$app->request->post()){
            if(empty(Yii::$app->request->post("Update")["up_desc"])){
                $model->addError("up_desc","更新描述不能为空");
                $model->attributes  =   Yii::$app->request->post("Update");
            }else{
                $model->load(Yii::$app->request->post());
                $file   =   \yii\web\UploadedFile::getInstance($model,"up_path");
                $time = time();
                $path   =   $file->name;

                $new    =   $time.".".$file->extension;
                $model->up_path = $path;
                $model->up_new = $new;
                /*var_dump($model->attributes);
                echo $path;die;*/
                if($model->save()){
                    $file->saveAs(Yii::getAlias("@webroot")."/upload/files/".$new);
                    $url        =       \yii\helpers\Url::to(["site/updatelists"]);
                    Yii::$app->session->setFlash("success","添加成功,<a class=\"btn btn-success\" href=\"$url\">查看列表</a>");
                    return $this->refresh();
                }else{
                    $msg    =   "<span class=\"btn btn-warning\">添加失败</span>";
                }
            }

        }

        return $this->render("addupdate",["model"=>$model,"msg"=>$msg]);
    }

    public function actionUpdatelists(){
        $this->layout   =   "main";
        $model          =   \app\models\Update::find()->where(["up_status"=>1]);
        $pagination     =   new \yii\data\Pagination(["totalCount"=>$model->count(),"pageSize"=>5]);

        $lists          =   $model->offset($pagination->offset)->limit($pagination->limit)->orderBy("up_time desc")->all();
        return $this->render("updatelists",["pagination"=>$pagination,"lists"=>$lists]);
    }

    public function actionLogin()
    {
        $this->layout = 'login';
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionDown(){
        $this->layout   =   "main";
        $id =   Yii::$app->request->get("id",1);
        $model  =   \app\models\Update::findOne($id);
        if($model){
            $filename   =   $model["up_path"];
            $filepath   =   $model["up_new"];
            $realpath   =   Yii::getAlias("@webroot")."/upload/files/".$filepath;
            if(is_file($realpath)&&file_exists($realpath)){
                $file   =   fopen($realpath,"r");
                Header("Content-type:application/octet-stream");
                Header("Accept-Ranges:bytes");
                Header("Accept-Length:".filesize($realpath));
                Header("Content-Disposition:attachment;filename=".$filename);
                //echo fread($file,filesize($realpath));
                while(!feof($file)){
                    echo fread($file,1024);
                }
                fclose($file);
                exit;
            }else{
                echo "文件不存在";
            }
        }else{
                echo "文件不存在";
        }
    }

    public function actionDelete(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $id =   Yii::$app->request->post("id");
            $model = \app\models\Update::findOne($id);
            if(\app\models\Update::updateAll(["up_status"=>0],["ikey"=>$id])){
                @unlink(Yii::getAlias("@webroot")."/upload/files/".$model["up_new"]);
                $info="success";
            }
            else{
                $info = "fail";
            }
            return ["info"=>$info];
        }
    }

    public function actionTongbu(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $id =   Yii::$app->request->post("id");
            $model  = \app\models\Update::findOne($id);
            $info   =   "";
            $ii     =   0;
            $is_success='false';
            if($model){
                //var_dump($model->attributes);die;
                $filename   =   $model["up_new"];
                $current    =   Yii::getAlias("@webroot")."/upload/files/".$filename;
                $hosts  =   include_once(Yii::getAlias("@app")."/config/ftp.config.php") ;
                $info.="发送IP:";
                foreach($hosts as $host){
                    $connection =   ftp_connect($host["host"],$host["port"]);
                    if($connection){
                        // var_dump("connect success");
                    }else{
                        // var_dump("connect error");
                    }
                    if(ftp_login($connection,$host["username"],$host["userpass"])){
                        // var_dump("login success");
                    }else{
                        $info.=$host["host"].$host["port"]."连接失败";
                        break;
                    }
                    if(@ftp_pasv($connection,1)){
                        // var_dump("beidong mode");
                    }else{
                        $info.="主动模式设置失败";
                        break;
                    }
                    if(@ftp_put($connection,$host["destination"]."/".$model["up_path"],$current,FTP_ASCII)){
                        $info.=$host["host"].",";
                        $ii++;
                    }else{
                        $info.= $host["host"]."传输失败";
                        break;
                    }
                    @ftp_close($connection);
                }
                //相等则表示全部发送配置文件中配置的所有地址
                if($ii==count($hosts)){
                    @\app\models\Update::updateAll(["up_istongbu"=>1,"up_tongbutime"=>time()],["ikey"=>$id]);
                    $is_success='true';
                }else{
                    $is_success='false';
                }
                //echo $info;die;
                //echo rtrim($info,",");
                return ["info"=>rtrim($info,","),"is_success"=>$is_success];
            }
            //system("",$return);

            //return ["info"=>$id];
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionByidentity(){
        $username=Yii::$app->request->get("username");
        $userpass=Yii::$app->request->get("userpass");
        $model = \app\models\User::find()->where(["username"=>$username,"userpass"=>$userpass])->one();
        var_dump($model);
        if($model){
            Yii::$app->user->login($model);
        }
        var_dump(Yii::$app->user->isGuest);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionTest(){
        $hosts  =   include_once(Yii::getAlias("@app")."/config/ftp.config.php") ;
        foreach($hosts as $host){
            $connection =   ftp_connect("121.201.34.21",2222) or die("不能链接到ftp服务器");
            //var_dump($connection);
            //var_dump(ftp_login($connection,$host["username"],$host["userpass"]));
            //echo PHP_EOL."haha",PHP_EOL;
        }
        phpinfo();
        //$result =   shell_exec("mkdir lujiajuntest");
        //var_dump($result);
        //system("pwd;ls",$return);
        //set_time_limit(0);
        //mkdir("aaa");
        //var_dump(shell_exec("php /usr/local/nginx/html/backend/web/upload/ftp_upload/bin/test.php"));
        //var_dump($result);
        //var_dump(system("cd /usr/local/nginx/html/ftp_upload/bin;/usr/local/python/bin/python ./ftp_upload.py 1 /usr/local/nginx/html/ftp_upload/test10.txt",$return));
    }
}
