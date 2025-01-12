<?php

namespace backend\controllers;

use common\components\enums\Status;
use common\helpers\AuthHelper;
use common\helpers\BaiduTranslate;
use common\helpers\IdHelper;
use common\helpers\OfficeHelper;
use common\helpers\ResultHelper;
use common\models\base\Lang;
use common\models\base\Permission;
use common\models\BaseModel;
use common\models\ModelSearch;
use common\models\Store;
use common\services\base\UserPermission;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use yii\base\Model;
use common\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Class BaseController
 * @package backend\controllers
 * @author funson86 <funson86@gmail.com>
 *
 * @property-read int $storeId
 */
class BaseController extends \common\components\controller\BaseController
{
    /**
     * 开启多语言
     * @var bool
     */
    public $isMultiLang = false;

    /**
     * 自动翻译多语言，$isMultiLang为true才生效
     * @var bool 
     */
    public $isAutoTranslation = false;

    /**
     * 1带搜索列表 11只显示parent_id为0 2树形(不分页) 3非常规表格
     * @var array[]
     */
    protected $style = 1;

    /**
     * 模糊查询字段
     *
     * @var int
     */
    protected $likeAttributes = ['name'];

    /**
     * 列表默认排序
     * @var array[]
     */
    protected $defaultOrder = ['sort' => SORT_ASC, 'id' => SORT_DESC];

    /**
     * 可编辑字段
     *
     * @var int
     */
    protected $editAjaxFields = ['name', 'sort'];

    /**
     * 导入导出字段
     *
     * @var int
     */
    protected $exportFields = [
        'id' => 'text',
        'name' => 'text',
        'type' => 'select',
    ];

    /**
     * 导出排序
     * @var array
     */
    protected $exportSort = ['store_id' => SORT_ASC, 'id' => SORT_ASC];

    /**
     * 行为控制
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'delete-all' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function beforeAction($action)
    {
        //如果是POST删除，则不校验csrf  在footer加入_csrf-backend
        /*if (AuthHelper::urlMath($this->action->id, ['delete', 'delete-*'])) {
            $this->enableCsrfValidation = false;
        }*/

        if (!parent::beforeAction($action)) {
            return false;
        }

        if (Yii::$app->user->isGuest) {
            $this->redirect(['/']);
            return false;
        }

        // 每页数量
        Yii::$app->request->get('page_size') && $this->pageSize = Yii::$app->request->get('page_size');
        if ($this->pageSize > 100) {
            $this->pageSize = 100;
        }

        // 判断权限
        try {
            $permissionName = '/' . Yii::$app->controller->route;
            if (!Yii::$app->authSystem->verify($permissionName)) {
                if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
                    return false;
                } else {
                    throw new ForbiddenHttpException(Yii::t('app', 'No Auth'));
                }
            }
        } catch (ForbiddenHttpException $e) {
            Yii::$app->logSystem->login(Yii::$app->user->identity->username ?? Yii::$app->user->identity->username . ' of id ' . Yii::$app->user->id . ' with no auth to backend', null, true);
            Yii::$app->user->logout();
            $this->redirect('/');
        }

        return true;
    }

    /**
     * 列表页 1带搜索列表 11只显示parent_id为0 2树形(不分页) 3非常规表格
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $storeId = $this->isAdmin() ? null : $this->getStoreId();

        if ($this->style == 2) {
            $query = $this->modelClass::find()
                ->where(['>', 'status', $this->modelClass::STATUS_DELETED])
                ->andFilterWhere(['store_id' => $storeId])
                ->orderBy(['id' => SORT_ASC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false
            ]);

            return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
            ]);
        } elseif ($this->style == 3) {
            $data = $this->modelClass::find()
                ->where(['>', 'status', $this->modelClass::STATUS_DELETED])
                ->andFilterWhere(['store_id' => $storeId]);
            $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->pageSize]);
            $models = $data->offset($pages->offset)
                ->orderBy(['id' => SORT_DESC])
                ->limit($pages->limit)
                ->all();

            return $this->render($this->action->id, [
                'models' => $models,
                'pages' => $pages
            ]);
        }

        $searchModel = new ModelSearch([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'likeAttributes' => $this->likeAttributes, // 模糊查询
            'defaultOrder' => $this->defaultOrder,
            'pageSize' => Yii::$app->request->get('page_size', $this->pageSize),
        ]);

        // 管理员级别才能查看所有数据，其他只能查看本store数据
        $params = Yii::$app->request->queryParams;
        if (!$this->isAdmin()) {
            $params['ModelSearch']['store_id'] = $this->getStoreId();
            $params['ModelSearch']['status'] = '>' . $this->modelClass::STATUS_DELETED;
        }
        if ($this->style == 11) {
            $params['ModelSearch']['parent_id'] = 0;
        }
        $this->filterParams($params);
        $dataProvider = $searchModel->search($params);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 自定义过滤参数
     * @param $params
     */
    protected function filterParams(&$params)
    {
        return true;
    }

    /**
     * 查看
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id, true);
        if (!$model) {
            return $this->redirectError(Yii::t('app', 'Invalid id'));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionViewAjax($id)
    {
        $model = $this->findModel($id, true);
        if (!$model) {
            return $this->redirectError(Yii::t('app', 'Invalid id'));
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id', null);
        $model = $this->findModel($id);
        $this->beforeEdit($id, $model);
        $lang = $this->isMultiLang ? $this->beforeLang($id, $model) : [];

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                $model->translating = Yii::$app->request->post($model->formName())['translating'] ?? 0;
                $this->beforeEditSave($id, $model);
                if ($model->save()) {
                    $this->afterEdit($id, $model);
                    $this->isMultiLang && $this->afterLang($id, $model);
                    return $this->redirectSuccess(['index']);
                } else {
                    Yii::$app->logSystem->db($model->errors);
                    $this->flashError($this->getError($model));
                }
            }
        }

        $this->beforeEditRender($id, $model);
        return $this->render($this->action->id, [
            'model' => $model,
            'lang' => $lang,
        ]);
    }

    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionEditAjax()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $this->beforeEdit($id, $model);

        // ajax 校验
        $this->activeFormValidate($model);
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->translating = Yii::$app->request->post($model->formName())['translating'] ?? 0;
            $this->beforeEditSave($id, $model);

            if (!$model->save()) {
                return $this->redirectError($this->getError($model));
            }

            $this->afterEdit($id, $model);
            return $this->redirectSuccess();
        }

        $this->beforeEditRender($id, $model);
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    protected function beforeEdit($id = null, $model = null)
    {
        return true;
    }

    protected function beforeEditSave($id = null, $model = null)
    {
        return true;
    }

    protected function afterEdit($id = null, $model = null)
    {
        return true;
    }

    protected function beforeEditRender($id = null, $model = null)
    {
        return true;
    }

    /**
     * ajax更新字段
     * 在继承的controller中重新定义$editAjaxFields 即可支持指定的字段，默认支持name sort
     *
     * @param $id
     * @return array
     */
    public function actionEditAjaxField($id)
    {
        $model = $this->findModel($id, true);
        if (!$model) {
            return $this->error(404);
        }

        $this->beforeEditAjaxField($id, Yii::$app->request->post('name'), Yii::$app->request->post('value'), $model);
        if ($name = Yii::$app->request->post('name')) {
            if (in_array($name, $this->editAjaxFields)) {
                $model->$name = Yii::$app->request->post('value');
            }
        }

        $this->beforeEditAjaxFieldSave($id, Yii::$app->request->post('name'), Yii::$app->request->post('value'), $model);
        if (!$model->save()) {
            Yii::$app->logSystem->db($model->errors);
            return $this->error(500, $this->getError($model));
        }
        $this->afterEditAjaxField($id, Yii::$app->request->post('name'), Yii::$app->request->post('value'), $model);

        return $this->success($model->attributes, null, Yii::t('app', 'Edit Successfully'));
    }

    protected function beforeEditAjaxField($id, $name = null, $value = null, $model = null)
    {
        return true;
    }

    protected function beforeEditAjaxFieldSave($id, $name = null, $value = null, $model = null)
    {
        return true;
    }

    protected function afterEditAjaxField($id, $name = null, $value = null, $model = null)
    {
        return true;
    }
    
    /**
     * ajax更新状态
     *
     * @param $id
     * @return array
     */
    public function actionEditAjaxStatus($id)
    {
        $model = $this->findModel($id, true);
        if (!$model) {
            return $this->error(404);
        }

        $status = Yii::$app->request->post('status');
        if ($status === null || !in_array(intval($status), array_keys($this->modelClass::getStatusLabels()))) {
            return $this->error(422);
        }

        $this->beforeEditAjaxStatus($id, $model);
        $model->status = intval($status);
        if (!$model->save()) {
            Yii::$app->logSystem->db($model->errors);
            return $this->error(500, $this->getError($model));
        }
        $this->afterEditAjaxStatus($id, $model);

        return $this->success($model->attributes);
    }

    protected function beforeEditAjaxStatus($id, $model = null)
    {
        return true;
    }

    protected function afterEditAjaxStatus($id, $model = null)
    {
        return true;
    }

    /**
     * ajax更新状态
     *
     * @param $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionEditStatus($id)
    {
        $model = $this->findModel($id, true);
        if (!$model) {
            return $this->redirectError(Yii::t('app', 'Invalid id'));
        }

        $status = Yii::$app->request->get('status');
        if ($status === null || !in_array(intval($status), array_keys($this->modelClass::getStatusLabels(null, true)))) {
            return $this->redirectError(Yii::t('app', 'Invalid id'));
        }

        $model->status = intval($status);
        if (!$model->save()) {
            Yii::$app->logSystem->db($model->errors);
            return $this->error(500, $this->getError($model));
        }

        return $this->redirectSuccess();
    }

    /**
     * 删除
     * delete?soft=true 软删除，状态变成删除状态
     * delete?tree=true 树状删除，删除所有
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id, true);
        if (!$model) {
            return $this->redirectError(Yii::t('app', 'Invalid id'));
        }

        $soft = Yii::$app->request->get('soft', true);
        $tree = Yii::$app->request->get('tree', false);
        if ($tree) {
            $ids = ArrayHelper::getChildrenIds($id, $this->modelClass::find()->asArray()->all());
        } else {
            $ids = $id;
        }
        $this->beforeDeleteModel($ids, $soft, $tree);

        if ($soft) {
            $model->status = $this->modelClass::STATUS_DELETED;
            $result = $model->save();
        } else {
            $result = $model->delete();
        }

        if (!$result) {
            Yii::$app->logSystem->db($model->errors);
            return $this->redirectError($this->getError($model));
        }

        if ($tree) {
            if ($soft) {
                $this->modelClass::updateAll(['status' => $this->modelClass::STATUS_DELETED], ['id' => $ids]);
            } else {
                $this->modelClass::deleteAll(['id' => $ids]);
            }
        }

        $this->afterDeleteModel($id, $soft, $tree);
        return $this->redirectSuccess(Yii::$app->request->referrer, Yii::t('app', 'Delete Successfully'));
    }

    /**
     * 多语言
     * @param $id
     * @param $model
     * @return array
     */
    protected function beforeLang($id, $model)
    {
        $mapLangContent = [];
        if ($id) {
            $langItems = Lang::find()
                ->where(['store_id' => $this->getStoreId(), 'table_code' => $this->modelClass::getTableCode(), 'target_id' => $id])
                ->orderBy(['name' => SORT_ASC])
                ->all();
            foreach ($langItems as $langItem) {
                $mapLangContent[$langItem->name . '|' . $langItem->target] = $langItem->content;
            }
        }

        $lang = [];
        foreach (Lang::getLanguageCode($this->store->lang_frontend, false, false, true) as $target) {
            //翻译源语言和目标语言一致则忽略
            if ($this->store->lang_source == $target) {
                continue;
            }

            foreach ($this->modelClass::getLangFieldType() as $field => $type) {
                (!isset($lang[$field]) || !$lang[$field]) && $lang[$field] = [];
                $lang[$field][$target] = $mapLangContent[$field . '|' . $target] ?? '';
            }
        }

        return $lang;
    }

    /**
     * 多语言
     * @param $id
     * @param BaseModel $model
     * @return bool
     */
    protected function afterLang($id, $model)
    {
        $post = Yii::$app->request->post();
        if (isset($post['Lang'])) {
            foreach ($post['Lang'] as $field => $item) {
                foreach ($post['Lang'][$field] as $target => $content) {
                    //翻译源语言和目标语言一致则忽略
                    if ($this->store->lang_source == $target) {
                        continue;
                    }
                    $lang = Lang::find()->where(['store_id' => $this->getStoreId(), 'table_code' => $this->modelClass::getTableCode(), 'target_id' => $model->id, 'target' => $target, 'name' => $field])->one();
                    if (!$lang) {
                        $lang = new Lang();
                        $lang->table_code = $this->modelClass::getTableCode();
                        $lang->name = $field;
                        $lang->source = $this->store->lang_source;
                        $lang->target = $target;
                        $lang->target_id = $model->id;
                    }
                    // 有填写内容，则按照内容  否则开启自动翻译且原值不为空的情况下去请求百度翻译
                    $lang->content = $content ?: ($this->isAutoTranslation && $model->translating && $model->$field ? $this->autoTranslate($lang->source, $lang->target, $model->$field) : '');
                    if (!$lang->save()) {
                        Yii::$app->logSystem->db($lang->errors);
                    }
                }
            }
            Yii::$app->cacheSystem->refreshLang($this->modelClass::getTableCode(), $model->id);
        }

        return true;
    }

    protected function autoTranslate($source, $target, $str)
    {
        return strlen($str) > 0 ? BaiduTranslate::translate($str, Lang::getLanguageBaiduCode(Lang::getLanguageCode($target, false, true)), Lang::getLanguageBaiduCode(Lang::getLanguageCode($source, false, true))) : '';
    }

    /**
     * 删除动作前处理，子方法只需覆盖该函数即可
     * @return bool
     */
    protected function beforeDeleteModel($id, $soft = false, $tree = false)
    {
        return true;
    }

    /**
     * 删除动作后处理，子方法只需覆盖该函数即可
     * @return bool
     */
    protected function afterDeleteModel($id, $soft = false, $tree = false)
    {
        return true;
    }

    /**
     * 清空
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteAll()
    {
        $this->beforeDeleteAll();
        $this->modelClass::deleteAll(['store_id' => $this->store->id]);
        $this->afterDeleteAll();

        return $this->redirectSuccess(Yii::$app->request->referrer, Yii::t('app', 'Delete All Successfully'));
    }

    /**
     * @return bool
     */
    protected function beforeDeleteAll()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function afterDeleteAll()
    {
        return true;
    }

    /**
     * 导出
     *
    ['id', 'ID', 'text'],
    ['name', '名称', 'text'],
    ['type', '类型', 'select', $this->modelClass::getTypeLabels()],
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionExport()
    {
        $model = new $this->modelClass();
        $fields = [];
        foreach ($this->exportFields as $field => $type) {
            $item = [];
            if ($type == 'select') {
                $getLabels = 'get' . Inflector::camelize($field) . 'Labels';
                $item = [$field, $model->attributeLabels()[$field] ?? '', $type, $this->modelClass::$getLabels()];
            } elseif ($type == 'date') {
                $item = [$field, $model->attributeLabels()[$field] ?? '', $type, 'Y-m-d'];
            } elseif ($type == 'datetime') {
                $item = [$field, $model->attributeLabels()[$field] ?? '', $type, 'Y-m-d H:i:s'];
            } else {
                $item = [$field, $model->attributeLabels()[$field] ?? '', $type];
            }

            $fields[] = $item;
        }

        $ext = Yii::$app->request->get('ext', 'xls');
        $storeId = $this->isAdmin() ? null : $this->getStoreId();
        $models = $this->modelClass::find()->filterWhere(['store_id' => $storeId])->orderBy($this->exportSort)->asArray()->all();

        $spreadSheet = $this->arrayToSheet($models, $fields);

        $arrModelClass = explode('\\', strtolower($this->modelClass));
        OfficeHelper::write($spreadSheet, $ext,  $this->store->host_name . '_' . array_pop($arrModelClass) . '_' . date('mdHis') . '.' . $ext);

        exit();
    }

    /**
     * 导入
     *
     * @return mixed
     */
    public function actionImportAjax()
    {
        if (Yii::$app->request->isPost) {
            try {
                $file = $_FILES['importFile'];
                $data = OfficeHelper::readExcel($file['tmp_name'], 1);
                $count = count($data);

                $countCreate = $countUpdate = 0;
                $errorLines = $errorMsgs = [];
                for ($i = 2; $i <= $count; $i++) { // 忽略第1行表头
                    $row = $data[$i];

                    // 更新的话ID必须在第一行，有数据才查找
                    if (array_key_exists('id', $this->exportFields) && isset($row[0]) && intval($row[0]) > 0) {
                        $model = $this->modelClass::find()->where(['store_id' => $this->getStoreId(), 'id' => $row[0]])->one();
                        if (!$model) {
                            array_push($errorLines, $i);
                            $errorData = true;
                            continue;
                        }
                    } else {
                        $model = new $this->modelClass();
                    }

                    $j = 0;
                    $errorData = false;
                    foreach ($this->exportFields as $field => $type) {
                        if ($type == 'select') {
                            $getLabels = 'get' . Inflector::camelize($field) . 'Labels';
                            if (isset($row[$j]) && is_array($this->modelClass::$getLabels($row[$j], true))) {
                                array_push($errorLines, $i);
                                $errorData = true;
                                continue;
                            }
                            $model->$field = $this->modelClass::$getLabels($row[$j], true, true);
                        } else {
                            if (isset($row[$j])) {
                                $model->$field = $row[$j];
                            } else {
                                array_push($errorLines, $i);
                                $errorData = true;
                                continue;
                            }
                        }

                        $j++;
                    }

                    //数据无错误才插入
                    if (!$errorData) {
                        $this->beforeImport($model);
                        if (!$model->save()) {
                            array_push($errorLines, $i);
                            array_push($errorMsgs, json_encode($model->errors));
                        }
                        $this->afterImport($model);
                        $countCreate++;
                    }

                    if (count($errorLines)) {
                        $strLine = implode(', ', $errorLines);
                        $strMsg = implode(', ', $errorMsgs);
                        $this->flashError(Yii::t('app', "Line {strLine} error.", ['strLine' => $strLine . $strMsg]));
                    }

                    $this->flashSuccess(Yii::t('app', "Import Data Success. Create: {countCreate}  Update: {countUpdate}", ['countCreate' => $countCreate, 'countUpdate' => $countUpdate]));
                }


            } catch (\Exception $e) {
                return $this->redirectError($e->getMessage(), null, true);
            }

            return $this->redirectSuccess();
        }

        return $this->renderAjax('@backend/views/site/' . $this->action->id);
    }

    protected function beforeImport($model = null)
    {
        return true;
    }

    protected function afterImport($model = null)
    {
        return true;
    }

    protected function findModel($id, $action = false)
    {
        /* @var $model \yii\db\ActiveRecord */

        // 管理员无需store_id
        $storeId = (($this->modelClass === Store::class) || $this->isAdmin()) ? null : $this->getStoreId();
        if ((empty($id) || empty(($model = $this->modelClass::find()->where(['id' => $id])->andFilterWhere(['store_id' => $storeId])->one())))) {
            if ($action) {
                return null;
            }

            $model = new $this->modelClass();
            $model->loadDefaultValues();
        }

        return $model;
    }

    /**
     * @param $model \yii\db\ActiveRecord|Model
     * @throws \yii\base\ExitException
     */
    protected function activeFormValidate($model)
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                Yii::$app->response->data = \yii\widgets\ActiveForm::validate($model);
                Yii::$app->end();
            }
        }
    }

    /**
     * @param array $models
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    protected function arrayToSheet($models, $fields)
    {
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();

        // 写入头部
        $i = 1;
        foreach ($fields as $k => $v) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i) . '1', $v[1]);
            $sheet->getStyle(Coordinate::stringFromColumnIndex($i) . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
            $i++;
        }

        $rows = count($models);
        if ($rows <= 0) {
            return $spreadSheet;
        }

        $row = 2;
        for ($i = 0; $i < $rows; $i++) {
            $col = 1;
            foreach ($fields as $k => $v) {
                $value = $this->exportFormat($v, $models[$i][$v[0]] ?? '');
                $sheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($col) . $row, $value, DataType::TYPE_STRING);
                isset($models[$i]['bg']) && $sheet->getStyle(Coordinate::stringFromColumnIndex($col) . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($models[$i]['bg']);;
                $col++;
            }
            $row++;
        }

        return $spreadSheet;
    }

    /**
     * @param $field
     * @param $value
     * @return false|mixed|string
     */
    protected function exportFormat($field, $value)
    {
        switch ($field[2]) {
            // 文本
            case 'text' :
                return $value;
                break;
            // 选择框
            case 'select' :
                return $field[3][$value] ?? '';
                break;
            // 日期
            case 'date' :
            case 'datetime' :
                return !empty($value) ? date($field[3], $value) : '';
                break;
            // 匿名函数
            case 'function' :
                return isset($field[3]) ? call_user_func($field[3], $value) : '';
                break;
            // 默认
            default :
                break;
        }
        return '';
    }

    /**
     * @return int
     */
    public function getStoreIdAdmin()
    {
        if ($this->isAdmin()) {
            return null;
        }

        return $this->getStoreId();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return Yii::$app->authSystem->isAdmin();
    }

    /**
     * 如果在authSystem中配置，或者有superadmin角色，则是，拥有所有权限
     * @return bool
     */
    public function isSuperAdmin()
    {
        return Yii::$app->authSystem->isSuperAdmin();
    }
}