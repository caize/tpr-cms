<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/8/29 14:46
 */

namespace tpr\admin\system\controller;

use library\logic\NodeLogic;
use think\Db;
use tpr\admin\common\controller\AdminLogin;

/**
 * Class Node
 * @package tpr\admin\system\controller
 */
class Node extends AdminLogin
{
    /**
     * 权限管理
     */
    public function index(){
        if($this->request->isPost()){
            $is_page = $this->request->param('is_page',1);
            $page = $is_page ? $this->request->param('page',1) : false;
            $limit= $this->request->param('limit',10);
            $result = NodeLogic::adminNode($page , $limit);
            $this->tableData($result['list'],$result['count']);
        }

        return $this->fetch();
    }

    /**
     * 获取权限节点数据
     */
    public function auth(){
        $role_id = $this->request->param('role_id',0);
        if(empty($role_id)){
            $this->error('error');
        }

        $result = NodeLogic::adminNode(false);
        $list = $result['list'];

        $role_node = Db::name('role_node')->where('role_id',$role_id)->select();
        $role_path_list = [];
        foreach ($role_node as $rn){
            array_push($role_path_list , $rn['node_path']);
        }

        foreach ($list as &$l){
            $l['LAY_CHECKED'] = in_array($l['path'],$role_path_list) ? 1 : 0;
        }
        $this->tableData($list,$result['count']);
    }
}