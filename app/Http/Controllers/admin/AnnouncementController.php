<?php

namespace App\Http\Controllers\admin;

use App\Helpers\pageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\AnnouncementRequest;
use App\Models\Announcement;
use App\Models\Carousel;
use Illuminate\Http\Request;

/**
 * 公告管理
 * App\Http\Controllers\admin
 * @author: qiaohao
 * @Time: 2023/2/23 16:23
 */
class AnnouncementController extends Controller
{
    /**
     * 公告列表
     * User: qiaohao
     * Date: 2023/2/23 18:13
     */
    public function index(Request $request)
    {
        $params=$request->input();
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);
        $where=[];
        if(isset($params['title']) && $params['title']){
            $where[]=['title','like',"%{$params['title']}%"];
        }
        if(isset($params['is_online']) && $params['is_online']){
            $where[]=['is_online','=',$params['is_online']];
        }
        $count=Announcement::where($where)->count();
        $list=Announcement::where($where)
            ->select('*')
            ->orderBy('sort','asc')
            ->orderBy('id','desc')
            ->offset($pageParam['offset'])
            ->limit($pageParam['pageSize'])
            ->get()
            ->toArray();
        $res= [
            'list'=>$list,
            'pagination'=>pageHelper::getPagination($params['page'],$params['pageSize'],$count)
        ];
        return $this->success($res);
    }

    /**
     * 新增
     * User: qiaohao
     * Date: 2023/2/23 18:20
     */
    public function add(AnnouncementRequest $request)
    {
        $params=$request->input();
        Announcement::create($params);
        return $this->success();
    }

    /**
     * 状态
     * User: qiaohao
     * Date: 2023/2/23 18:34
     */
    public function detail($id)
    {
        $model=Announcement::find($id);
        return $this->success($model);
    }

    /**
     * 编辑
     * User: qiaohao
     * Date: 2023/2/23 18:25
     */
    public function modify(AnnouncementRequest $request)
    {
        $params=$request->input();
        $model=Announcement::find($params['id']);
        if(!$model) throw new \Exception("数据不存在");
        $model->fill($params);
        $model->save();
        return $this->success();
    }

    /**
     * 删除
     * User: qiaohao
     * Date: 2023/2/23 18:26
     */
    public function delete($id)
    {
        Announcement::destroy($id);
        return $this->success();
    }

    /**
     * 设置排序
     * User: qiaohao
     * Date: 2023/2/23 18:32
     */
    public function sort(Request $request)
    {
        $id=$request->input('id');
        $sort=$request->input('sort',0);
        $model=Announcement::find($id);
        if($model){
            $model->sort=$sort;
            $model->save();
        }
        return $this->success([]);
    }

    /**
     * 设置状态
     * User: qiaohao
     * Date: 2023/2/23 18:33
     */
    public function set_is_online($id)
    {
        $model=Announcement::find($id);
        if($model){
            $model->is_online=$model->is_online==1?2:1;
            $model->save();
        }
        return $this->success();
    }
}
