<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CarouselRequest;
use App\Models\Carousel;
use App\Services\CarouselService;
use Illuminate\Http\Request;
use Exception;
//轮播图
class CarouselController extends Controller
{
    //列表
    public function index(Request $request)
    {
        $params=$request->input();
        $res=(new CarouselService())->getList($params);
        return $this->success($res);
    }

    //新增
    public function add(CarouselRequest $request)
    {
        $params=$request->input();
        (new CarouselService())->add($params);
        return $this->success();
    }

    //编辑
    public function update(CarouselRequest $request)
    {
        $params=$request->input();
        (new CarouselService())->update($params);
        return $this->success();
    }

    //删除
    public function delete($id)
    {
        Carousel::destroy($id);
        return $this->success();
    }

    //排序
    public function set_sort(Request $request)
    {
        $id=$request->input('id');
        $sort=$request->input('sort');
        Carousel::where('id',$id)->update(['sort'=>$sort]);
        return $this->success();
    }

    //设置上下架
    public function set_is_online($id)
    {
        $carousel=Carousel::find($id);
        $carousel->is_online=$carousel->is_online==1?2:1;
        $carousel->save();
        return $this->success();
    }
}
