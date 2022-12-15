<?php

namespace schmauch\newsletter\controllers;

use schmauch\newsletter\models\NewsletterBlacklist;
use schmauch\newsletter\models\NewsletterBlacklistSearch;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Session;

/**
 * BlacklistController implements the CRUD actions for NewsletterBlacklist model.
 */
class BlacklistController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['sign-off'],
                            'roles' => ['?'],
                        ],
                    ],
                ],            
            ],
        );
    }

    /**
     * Lists all NewsletterBlacklist models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new NewsletterBlacklistSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NewsletterBlacklist model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new NewsletterBlacklist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionSignOff($email = null)
    {
        $email = $this->request->post('NewsletterBlacklist')['email'] ?? $email;
        
        // if the address is already blacklisted, show success page
        if ($email && $model = NewsletterBlacklist::findOne(['email' => $email])) {
            return $this->render('success', ['model' => $model]);
        }
        
        // 
        $model = new NewsletterBlacklist();
        $model->email = $email;
        
        if ($this->request->isPost) {
            $model->added_at = date('Y-m-d H:i:s');
            if(!$model->validate()) {
                $message = 'Die E-Mail-Adresse ' . $model->email . ' konnte nicht aus der Liste entfernt werden.';
                $message .= $model->getErrorSummary();
                \Yii::$app->session->setFlash('error',  $message);
            }

            if($model->save()) {
                return $this->redirect(['sign-off', 'email' => $email]);
            }
        }        

        return $this->render('form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing NewsletterBlacklist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing NewsletterBlacklist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the NewsletterBlacklist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return NewsletterBlacklist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NewsletterBlacklist::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
