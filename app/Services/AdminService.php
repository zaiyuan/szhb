<?php

namespace App\Services;

use App\Helpers\JwtHelper;
use App\Helpers\pageHelper;
use App\Models\AdminLog;
use App\Models\system\Admin;
use Exception;
use Illuminate\Support\Facades\Crypt;

class AdminService
{
    public function login($data)
    {
        $admin=Admin::where('username',$data['username'])
            ->select('id','username','role_id','password','status')
            ->first();
        if(!$admin) {
            throw new Exception("账号或密码错误");
        }
        if(Crypt::decryptString($admin['password'])!=$data['password']){
            throw new Exception("账号或密码错误");
        }
        if($admin['status']!=1){
            throw new Exception("用户已禁用");
        }
        if($admin->role->status!=1){
            throw new Exception("用户角色已禁用");
        }
        $admin->last_login=date('Y-m-d H:i:s',time());
        $admin->last_ip=request()->ip();
        $admin->save();
        AdminLog::addOne([
            'admin_id'=>$admin->id,
            'desc'=>'管理员登陆',
            'created_at'=>date('Y-m-d H:i:s',time())
        ]);

        $token=JwtHelper::createJwt([
            'user_id'=>$admin->id,
            'username'=>$admin->username
        ]);
        return [
            'id'=>$admin->id,
            'username'=>$admin->username,
            'access_token'=>$token
        ];
    }

    public function getAdminList($params)
    {
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);

        $keyword=isset($params['keyword'])?$params['keyword']:"";
        $where=[];
        if(isset($params['role_id']) && $params['role_id']){
            $where[]=['role_id','=',$params['role_id']];
        }

        $count=Admin::where($where)
            ->when($keyword,function($query,$keyword){
                return $query->where(function($query)use($keyword){
                    $query->where('realname','like',"%{$keyword}%")
                    ->orWhere('username','like',"%{$keyword}%");
                });
            })
            ->count();
        $list=Admin::where($where)
            ->when($keyword,function($query,$keyword){
                return $query->where(function($query)use($keyword){
                    $query->where('realname','like',"%{$keyword}%")
                        ->orWhere('username','like',"%{$keyword}%");
                });
            })
            ->select('*')
            ->orderBy('id','asc')
            ->offset($pageParam['offset'])
            ->limit($params['pageSize'])
            ->with('role')
            ->get();
        $list=$list->append('can_delete')->toArray();
        return [
            'list'=>$list,
            'pagination'=>pageHelper::getPagination($pageParam['page'],$pageParam['pageSize'],$count)
        ];
    }

    public function getAll($remove_super)
    {
        $where=[];
        if($remove_super==1){
            $where[]=['id','>',1];
        }
        $list=Admin::select('id as value','realname as label')
            ->where($where)
            ->orderBy('id','asc')
            ->get()
            ->toArray();
        return $list;
    }

    //新增
    public function add($params)
    {
        $params['password']=Crypt::encryptString($params['password']);
        Admin::create($params);
    }

    //编辑
    public function update($params)
    {
        $model=Admin::find($params['id']);
        if(!$model){
            throw new Exception("数据不存在");
        }
        $model->role_id=$params['role_id'];
        $model->realname=$params['realname'];
        $model->username=$params['username'];
        if(isset($params['password']) && $params['password']){
            $model->password=Crypt::encryptString($params['password']);
        }
        $model->status=$params['status'];
        $model->save();
    }

    //设置状态
    public function set_status($id)
    {
        $model=Admin::find($id);
        if(!$model){
            throw new Exception("数据不存在");
        }
        $model->status=$model->status==1?0:1;
        $model->save();
    }


    public function getAdminLog($params)
    {
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);

        $where = function($query)use($params){
            if (isset($params['sdate']) && $params['sdate']) {
                $query->whereBetween('created_at',[date('Y-m-d 00:00:00', strtotime($params['sdate'])),date('Y-m-d 23:59:59', strtotime($params['edate']))]);
            }
            if (isset($params['admin_id']) && $params['admin_id']) {
                $query->where('admin_id',$params['admin_id']);
            }
        };

        $count=AdminLog::where($where)
            ->count();
        $list=AdminLog::where($where)
            ->select('*')
            ->orderBy('id','asc')
            ->with([
                'admin'
            ])
            ->offset($pageParam['offset'])
            ->limit($params['pageSize'])
            ->get()->toArray();
        return [
            'list'=>$list,
            'pagination'=>pageHelper::getPagination($pageParam['page'],$pageParam['pageSize'],$count)
        ];
    }

}
