<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionMatrix() {
        Yii::import("ext.EAHPHelper");
        $ListKriteria = CHtml::ListData(Lookup::model()->findAll("LookupGroup='AHP_CRITERIA'"), "Id", "LookupValue");
        $ahp = new EAHPHelper($ListKriteria);

        if (Yii::app()->request->isPostRequest) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $selectionData = explode(",", Yii::app()->request->getParam('emrviewdata'));
                $ahp->setData($selectionData);

                $lookups = Lookup::model()->findAll("LookupGroup='AHP_VALUE'");
                foreach ($lookups as $lookup) {
                    $lookup->delete();
                }
                $result = "";
                $ahpValues = array("CI" => $ahp->getCI(), "RC" => $ahp->getRC(), "CR" => $ahp->getCR());
                foreach ($ahpValues as $name => $value) {
                    $lookup = new Lookup();
                    $lookup->LookupGroup = 'AHP_VALUE';
                    $lookup->LookupName = $name;
                    $lookup->LookupValue = $value;
                    $lookup->save();

                    $result .= "<b> $name </b> : " . $value . "<br />";
                }

                $ev = $ahp->getEigenVector(true);
                foreach ($ev as $kriteria => $eigenvector) {
                    $lookup = new Lookup();
                    $lookup->LookupGroup = 'AHP_VALUE';
                    $lookup->LookupName = "EV_$kriteria";
                    $lookup->LookupValue = $eigenvector;
                    $lookup->save();

                    $result .= "<b> $lookup->LookupName </b> : " . $eigenvector . "<br />";
                }
                $transaction->commit();
                Yii::app()->user->setFlash("success-msg", "Result: <br /> $result <hr />");
//                    Yii::app()->user->setState("ahpKriteria", $ahpKriteria);
            } catch (Exception $e) {
                $transaction->rollback();
                Yii::app()->user->setFlash("error-msg", "Cannot save AHP Kriteria. Detail: " . $e);
            }

//            $this->redirect(array("site/ahpKampus",
//                'prodi_id' => Yii::app()->request->getParam('prodi_id'),
//                'jenjang_id' => Yii::app()->request->getParam('jenjang_id'),
//                'akreditasi_id' => Yii::app()->request->getParam('akreditasi_id'),
//                'lokasi_id' => Yii::app()->request->getParam('lokasi_id'),
//                'kelas_id' => Yii::app()->request->getParam('kelas_id'),
//            ));
//            return;
        }
        $this->pageTitle = Yii::app()->name . "AHP Kriteria";
        $this->render('ahpKriteria', array(
            'data_ahp' => $ahp->getOptionArray(),
        ));
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        $this->render('index');
//        echo 'Web Service';
    }

    public function actionCategoryList() {
        $model = Lookup::model()->schoolCategory()->findAll();

        echo CJSON::encode($model);
    }

    public function actionCategoryValue($id) {
        $model = null;
        if (is_numeric($id)) {
            $model = Lookup::model()->schoolCategory()->findByPk($id);
        } else {
            $model = Lookup::model()->schoolCategory()->find("LookupName='$id'");
        }

        if ($model !== null) {
            echo $model->LookupValue;
            return;
        }

        throw new CHttpException("404", "Page not found.");
    }

    private function sortModel($model, $field) {
        $model2 = null;
        foreach ($model as $data) {
            if ($model2 == null)
                $model2[] = $data;
            elseif (count($model2) == 1) {
                if ($model2[0]->$field > $data->$field)
                    $model2 = array_merge(array($data), $model2);
                else
                    $model2[] = $data;
            }
            else {

                $totalModel2 = count($model2);
                if ($totalModel2 > 1) {
                    $i = 0;
                    while ($model2[$i]->$field < $data->$field) {
                        $i++;
                        if ($i >= $totalModel2) {
                            $i = $totalModel2;
                            break;
                        }
                    }

                    $model2 = array_merge(array_slice($model2, 0, $i), array($data), array_slice($model2, $i));
                }
            }
        }

        return $model2;
    }

    public function actionSchoolList($id = 0, $lat = 0, $lng = 0, $search = null) {
        if ($lat == 0)
            $lat = -6.572486;
        if ($lng == 0)
            $lng = 106.748271;
        set_time_limit(0);
        $search_query = $id == 0 ? "" : "CategoryId=$id";
        if ($search != null) {
            $search_query = strlen($search_query) == 0 ? "" : "$search_query AND ";
            $search_query .= "Name LIKE '%$search%'";
        }
        $model = School::model()->findAll($search_query);
        if ($model !== null) {
            foreach ($model as &$value) {
                $value->Distance = DistanceAlgorithm::DrivingDistanceBetweenPlaces($lng, $lat, $value->Longitude, $value->Latitude);
            }
            $model = $this->sortModel($model, "Distance");
        }

        echo CJSON::encode(array_slice($model, 0, 10));
        return;
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
                $headers = "From: $name <{$model->email}>\r\n" .
                        "Reply-To: {$model->email}\r\n" .
                        "MIME-Version: 1.0\r\n" .
                        "Content-type: text/plain; charset=UTF-8";

                mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

}

