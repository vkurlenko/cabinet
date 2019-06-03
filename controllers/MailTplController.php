<?php

namespace app\controllers;

use Yii;
use app\models\MailTpl;
use app\models\MailTplSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MailTplController implements the CRUD actions for MailTpl model.
 */
class MailTplController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete', 'update'],
                        'roles' => ['admin', 'director', 'manager']
                    ],
                   /* [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete', 'pdf', 'free-order-form', 'remove-img', 'set-main-img'],
                        'roles' => ['user']
                    ],*/
                    /* [
                         'allow' => true,
                         'actions' => ['update'],
                         'roles' => ['admin', 'director', 'manager']
                     ],
                     [
                         'allow' => true,
                         'actions' => ['index', 'view', 'create', 'delete'],
                         'roles' => ['admin', 'director', 'manager', 'user']
                     ],*/
                ],
            ],
        ];
    }

    /**
     * Lists all MailTpl models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MailTplSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MailTpl model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MailTpl model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MailTpl();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MailTpl model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MailTpl model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Формирование текста письма из шаблона
     *
     * @param null $alias - алиас шаблона
     * @param array $vars - массив замен вхождений
     *
     * @return bool|mixed|string - готовое тело письма
     */
    public function getTplByAlias($alias = null, $vars = [])
    {
        if($alias){
            $model = MailTpl::findOne(['alias' => $alias]);

            return self::replaceInserts($model->tpl, $vars);
        }

        return false;
    }

    public static function getSubject($alias = null, $vars = [])
    {
        if($alias){
            $model = MailTpl::findOne(['alias' => $alias]);

            return self::replaceInserts($model->subject, $vars);
        }

        return false;
    }

    /**
     * Замена вхождений {{xxx}} на их значение из массива $vars
     *
     * @param string $code - код шаблона (вида: Привет {{user_name}})
     * @param array $vars - массив замен (user_name => Вася)
     *
     * @return bool|mixed|string
     */
    protected function replaceInserts($code = '', $vars = [])
    {
        $begin = Yii::$app->params['insertBegin'];  // {{
        $end = Yii::$app->params['insertEnd'];      // }}

        if($code && $vars){
            foreach($vars as $k => $v){
                $code = str_replace($begin.$k.$end, $v, $code);
            }

            return $code;
        }

        return false;
    }



    /**
     * Finds the MailTpl model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MailTpl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MailTpl::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}
