<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/17 10:10
 */

namespace tpr\admin\index\controller;

use tpr\admin\common\controller\AdminLogin;
use think\Db;
use tpr\admin\common\model\MenuModel;

class Index extends AdminLogin
{
    /**
     * 后台系统主页面
     * @return mixed
     */
    public function index()
    {
        $menu = $this->menu();
        $this->assign('menu', $menu);
        return $this->fetch('index');
    }

    protected function menu()
    {
        $role_id = user_info('role_id');
        $parent_menu = MenuModel::model()->menus(0 , $role_id);
        foreach ($parent_menu as &$m) {
            $m['sub'] = MenuModel::model()->menus($m['id'] , $role_id);
        }
        return $parent_menu;
    }

    /**
     * 后台首页
     * @return mixed
     */
    public function main()
    {
        if ($this->request->isPost()) {
            $disk_size = floor(disk_free_space(ROOT_PATH) / (1024 * 1024));
            $disk_size = $disk_size < 100 ? '磁盘空间已小于100M' : $disk_size . 'M';

            $data = [
                'domain'     => $this->request->domain(),
                'os'         => PHP_OS,
                'server_ip'  => $_SERVER['SERVER_ADDR'],
                'server_env' => PHP_VERSION,
                'disk'       => $disk_size,
                'username'   => $this->user['username'],
                'upload_limit' => ini_get('upload_max_filesize')
            ];

            $today = get_day_begin_end_time(date("Y-m-d"));
            $req = [
                'env'                => $data,
                'users_number'       => Db::name('users')->count(),
                'users_number_today' => Db::name('users')->where('created_at', 'between', [$today['begin'], $today['end']])
            ];
            $this->ajaxReturn($req);
        }
        return $this->fetch('main');
    }
}