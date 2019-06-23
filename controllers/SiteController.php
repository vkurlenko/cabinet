<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\SignupForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\controllers\SignupService;

class SiteController extends Controller
{
    //public $enableCsrfValidation = false;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //return $this->render('index');

        $session = Yii::$app->session;

        if(!Yii::$app->user->getId())
            //return $this->redirect('/site/login');
            return $this->redirect('login');
        else{
            if($session->has('is-free-order')){
                return $this->redirect('/orders/free-order-form');
            }
            else
			    return $this->redirect('/orders/index');
		}
    }

    public function actionErrors()
    {
        $error = Yii::$app->request->get('msg');

        if($error){
            $msg = Yii::$app->params['errors'][$error];
        }

        return $this->render('errors', compact('msg'));
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
        }

        $loginModel = new LoginForm();
        $signupModel = new SignupForm();

        if ($loginModel->load(Yii::$app->request->post()) && $loginModel->login()) {
			return $this->goHome();
        }

        $loginModel->password = '';
		
        return $this->render('login', [
            'loginModel' => $loginModel,
            'signupModel' => $signupModel
        ]);
    }

    /**
    *   отменим проверку csrf для организации logout с основного сайта
    */
    public function beforeAction($action)
    {
        if ($action->id == 'logout') {
            $this->enableCsrfValidation = false;
        }

        if ($action->id == 'login') {
            // сохраним в сессию адрес предыдущей страницы, если она есть страница торта
            // в дальнейшем используем для редиректа на эту страницу после авторизации
            $arr = explode('.', Yii::$app->request->referrer);

            if($arr[count($arr) - 1] == 'html'){
                $session = Yii::$app->session;
                $session->set('ref', Yii::$app->request->referrer);
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        //echo 'logout'; die;
        Yii::$app->user->logout();

        //return $this->goHome();

        $uid_logout = true; // флаг разлогинивания для передачи на основной домен
        return $this->render('index', compact('uid_logout'));
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionAddAdmin() {
        $model = User::find()->where(['username' => 'vkurlenko'])->one();
        if (empty($model)) {
            $user = new User();
            $user->username = 'vkurlenko';
            $user->email = 'vkurlenko@yandex.ru';
            $user->setPassword('bibTar');
            $user->generateAuthKey();
            if ($user->save()) {
                echo 'good';
            }
        }
    }

    public function actionSignup()
    {
        /*$model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);*/

        $form = new SignupForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $signupService = new SignupService();

            try{
                $user = $signupService->signup($form);
                Yii::$app->session->setFlash('success', 'Для подтверждения регистрации проверьте ваш почтовый ящик');
                $signupService->sentEmailConfirm($user);
                return $this->goHome();
            } catch (\RuntimeException $e){
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('signup', [
            'model' => $form,
        ]);
    }

    public function actionSignupConfirm($token)
    {
        $signupService = new SignupService();

        try{
            $signupService->confirmation($token);
            Yii::$app->session->setFlash('success', 'Вы успешно зарегистрированы на сайте');
        } catch (\Exception $e){
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->goHome();
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте почту для получения дальнейших инструкций.', false);
                //return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Извините, ошибка восстановления пароля дла указанного email.', false);
            }
        }

        //return $this->render('requestPasswordResetToken', [
        return $this->render('passwordResetRequestForm', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль успешно сохранен.');
            return $this->goHome();
        }

        return $this->render('resetPasswordForm', [
            'model' => $model,
            ]);
      }

      public function getRole($id = null)
      {
          $arr = [];

          if($id){
              $roles = Yii::$app->authManager->getRolesByUser($id);

              if($roles){
                  foreach($roles as $role => $data){
                      $arr[] = $role;
                  }
              }
          }

          return $arr;
      }


      public static function alertTitle()
      {
          return [
              'danger' => 'Ошибка:',
              'success' => '',
              'primary' => 'Информация:',
              'warning' => 'Внимание:',
          ];
      }
}
