<?php

use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\helpers\Html;
use common\helpers\ImageHelper;

$store = $this->context->store;

$menus = [];
$defaultId = 0;
foreach (Yii::$app->authSystem->userPermissionsTree as $leftPermissions) {
    if ($defaultId == 0) {
        $defaultId = $leftPermissions['id'];
    }
    foreach ($leftPermissions['children'] as $permission) {
        $menu = [];

        $url = Url::to([$permission['path']]);
        if (count($permission['children']) > 0) {

            $url = '#';
            foreach ($permission['children'] as $child) {
                $subMenu = [
                    'label' => Yii::t('permission', $child['name']),
                    'icon' => $child['icon'],
                    'url' => [$child['path']],
                    'class' => $child['target'] ? '' : 'J_menuItem',
                    'target' => $child['target'] ? '_blank' : '_self',
                ];
                $menu['items'][] = $subMenu;
            }
        }

        $class = ' fbLeftMenuCat fbLeftMenuCat-' . $leftPermissions['id'];
        if ($leftPermissions['id'] != $defaultId) {
            $class .= ' hidden';
        }

        $menu += [
            'label' => Yii::t('permission', $permission['name']),
            'icon' => $permission['icon'],
            'url' => $url,
            'class' => $class,
        ];
        array_push($menus, $menu);
    }

}
if ($this->context->isAdmin()) { //管理员显示
    if (YII_ENV_DEV) {
        $subMenu = [
            ['label' => Yii::t('permission', 'Gii Crud'), 'icon' => 'fas fa-file-signature', 'url' => ['/gii/crud'], 'class' => 'nav-link', 'target' => '_blank'],
            ['label' => Yii::t('permission', 'Gii Model'), 'icon' => 'fas fa-download', 'url' => ['/gii/model'], 'class' => 'nav-link', 'target' => '_blank'],
            ['label' => Yii::t('permission', 'Gii All'), 'icon' => 'fas fa-code', 'url' => ['/gii'], 'class' => 'nav-link', 'target' => '_blank'],
        ];
        array_push($menus, ['label' => Yii::t('permission', 'Gii'), 'icon' => 'fas fa-code', 'url' => '#', 'class' => '', 'items' => $subMenu]);

        $subMenu = [
            ['label' => Yii::t('permission', '二维码'), 'icon' => 'fas fa-puzzle-piece', 'url' => ['/tool/qr'], 'class' => 'nav-link J_menuItem', 'target' => '_self'],
            ['label' => Yii::t('permission', 'Crud'), 'icon' => 'fas fa-puzzle-piece', 'url' => ['/tool/crud'], 'class' => 'nav-link J_menuItem', 'target' => '_self'],
            ['label' => Yii::t('permission', 'Crud Modal'), 'icon' => 'fas fa-puzzle-piece', 'url' => ['/tool/crud-modal'], 'class' => 'nav-link J_menuItem', 'target' => '_self'],
            ['label' => Yii::t('permission', '树形表格'), 'icon' => 'fas fa-puzzle-piece', 'url' => ['/tool/tree'], 'class' => 'nav-link J_menuItem', 'target' => '_self'],
            ['label' => Yii::t('permission', 'Mongodb Crud'), 'icon' => 'fas fa-puzzle-piece', 'url' => ['/tool/mongodb-crud'], 'class' => 'nav-link J_menuItem', 'target' => '_self'],
            ['label' => Yii::t('permission', 'Redis Crud'), 'icon' => 'fas fa-puzzle-piece', 'url' => ['/tool/redis-crud'], 'class' => 'nav-link J_menuItem', 'target' => '_self'],
            ['label' => Yii::t('permission', 'Elasticsearch Crud'), 'icon' => 'fas fa-puzzle-piece', 'url' => ['/tool/elasticsearch-crud'], 'class' => 'nav-link J_menuItem', 'target' => '_self'],
            ['label' => Yii::t('permission', 'Debug'), 'icon' => 'fas fa-bug', 'url' => ['/debug'], 'class' => 'nav-link', 'target' => '_blank'],
            ['label' => Yii::t('permission', '接口文档'), 'icon' => 'fas fa-file-alt', 'url' => ['/swagger/index'], 'class' => 'nav-link', 'target' => '_blank'],
        ];
        array_push($menus, ['label' => Yii::t('permission', 'Tools'), 'icon' => 'fas fa-puzzle-piece', 'url' => '#', 'class' => '', 'items' => $subMenu]);
    }
    array_push($menus, ['label' => Yii::t('permission', 'Funboot开发指南'), 'icon' => 'fa fa-book', 'url' => 'https://github.com/funson86/funboot/tree/master/docs/guide-zh-CN', 'target' => '_blank']);
    array_push($menus, ['label' => Yii::t('permission', 'QQ开发交流群'), 'icon' => 'fab fa-qq', 'url' => 'https://qm.qq.com/cgi-bin/qm/qr?k=jJwNMMAkEelzRPmHrSc-WXS5jrwVH-3x&jump_from=webapi', 'target' => '_blank']);
}
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= Yii::$app->homeUrl ?>" class="brand-link">
        <img src="<?= $store->settings['website_logo'] ?: ImageHelper::getLogo() ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= $store->settings['website_name'] ? substr($store->settings['website_name'], 0, 30) : Yii::$app->name ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= \common\helpers\ImageHelper::getAvatar() ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="javascript:;" class="d-block"><?= Yii::$app->user->identity->username ?></a>
            </div>
        </div>


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?= \common\widgets\adminlte\Menu::widget([
                'items' => $menus
            ]) ?>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
