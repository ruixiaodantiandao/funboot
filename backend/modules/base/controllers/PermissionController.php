<?php

namespace backend\modules\base\controllers;

use common\helpers\ArrayHelper;
use common\models\base\RolePermission;
use Yii;
use common\models\base\Permission;
use common\models\ModelSearch;
use backend\controllers\BaseController;
use yii\data\ActiveDataProvider;

/**
* Permission
*
* Class PermissionController
* @package backend\modules\base\controllers
*/
class PermissionController extends BaseController
{
    /**
    * @var Permission
    */
    public $modelClass = Permission::class;

    /**
     * 1带搜索列表 2树形 3非常规表格
     * @var array[]
     */
    protected $style = 2;

    /**
      * 模糊查询字段
      * @var string[]
      */
    public $likeAttributes = ['name', 'brief'];

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

    protected function beforeEdit($id = null, $model = null)
    {
        $model->parent_id == 0 && $model->parent_id = Yii::$app->request->get('parent_id', 0);

        // 计算level
        $model->level = 0;
        if ($model->parent_id > 0) {
            $parent = $this->modelClass::findOne($model->parent_id);
            if ($parent) {
                $model->level = $parent->level + 1;
            }
        }
    }

    protected function afterEdit($id = null, $model = null)
    {
        Yii::$app->cacheSystem->clearAllPermission(); // 清理缓存
    }

    /**
     * @param $id
     * @return bool|void
     */
    protected function beforeDeleteModel($id, $soft = false, $tree = false)
    {
        if (!$soft) {
            RolePermission::deleteAll(['permission_id' => $id]);
        }
    }

    /**
     * @param $id
     * @return bool
     */
    protected function afterDeleteModel($id, $soft = false, $tree = false)
    {
        return Yii::$app->cacheSystem->clearAllPermission(); // 清理缓存
    }
}
