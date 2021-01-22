<?php

namespace common\models\tool;

use Yii;
use common\models\User;
use common\models\Store;

/**
 * This is the model class for table "{{%tool_crud}}".
 *
 * @property int $id
 * @property int $store_id 商家
 * @property string $name 名称
 * @property string $description 简介
 * @property string $time 时间
 * @property string $date 日期
 * @property string $started_at 开始时间
 * @property string $ended_at 开始时间
 * @property string $tag 标签
 * @property string $image 图片
 * @property string $images 多图
 * @property string $file 文件
 * @property string $files 多文件
 * @property string|null $markdown Markdown编辑器
 * @property string|null $content 内容
 * @property int $type 类型
 * @property int $sort 排序
 * @property int $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $created_by 创建用户
 * @property int $updated_by 更新用户
 */
class Crud extends CrudBase
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tool_crud}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['store_id', 'type', 'sort', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'required'],
            [['markdown', 'content'], 'string'],
            [['name', 'description', 'time', 'date', 'started_at', 'ended_at', 'tag', 'image', 'images', 'file', 'files'], 'string', 'max' => 255],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        if (Yii::$app->language == Yii::$app->params['sqlCommentLanguage']) {
            return array_merge(parent::attributeLabels(), [
                'id' => Yii::t('app', 'ID'),
                'store_id' => '商家',
                'name' => '名称',
                'description' => '简介',
                'time' => '时间',
                'date' => '日期',
                'started_at' => '开始时间',
                'ended_at' => '开始时间',
                'tag' => '标签',
                'image' => '图片',
                'images' => '多图',
                'file' => '文件',
                'files' => '多文件',
                'markdown' => 'Markdown编辑器',
                'content' => '内容',
                'type' => '类型',
                'sort' => '排序',
                'status' => '状态',
                'created_at' => '创建时间',
                'updated_at' => '更新时间',
                'created_by' => '创建用户',
                'updated_by' => '更新用户',
            ]);
        } else {
            return array_merge(parent::attributeLabels(), [
                'id' => Yii::t('app', 'ID'),
                'store_id' => Yii::t('app', 'Store ID'),
                'name' => Yii::t('app', 'Name'),
                'description' => Yii::t('app', 'Description'),
                'time' => Yii::t('app', 'Time'),
                'date' => Yii::t('app', 'Date'),
                'started_at' => Yii::t('app', 'Started At'),
                'ended_at' => Yii::t('app', 'Ended At'),
                'tag' => Yii::t('app', 'Tag'),
                'image' => Yii::t('app', 'Image'),
                'images' => Yii::t('app', 'Images'),
                'file' => Yii::t('app', 'File'),
                'files' => Yii::t('app', 'Files'),
                'markdown' => Yii::t('app', 'Markdown'),
                'content' => Yii::t('app', 'Content'),
                'type' => Yii::t('app', 'Type'),
                'sort' => Yii::t('app', 'Sort'),
                'status' => Yii::t('app', 'Status'),
                'created_at' => Yii::t('app', 'Created At'),
                'updated_at' => Yii::t('app', 'Updated At'),
                'created_by' => Yii::t('app', 'Created By'),
                'updated_by' => Yii::t('app', 'Updated By'),
            ]);
        }
    }
}