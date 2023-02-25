<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Carousel;
use App\Models\Instruction;
use App\Models\Option;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * 轮播图列表
     * User: qiaohao
     * Date: 2023/2/23 16:02
     */
    public function carousel()
    {
        $list = Carousel::where('is_online', 1)
            ->select('id', 'image', 'link')
            ->orderBy('sort', 'asc')
            ->get();
        return $this->success($list);
    }

    /**
     * 公告列表
     * User: qiaohao
     * Date: 2023/2/23 19:29
     */
    public function announcement()
    {
        $list = Announcement::where('is_online', 1)
            ->orderBy('sort', 'asc')
            ->select("id", "title")
            ->get();
        return $this->success($list);
    }

    /**
     * 公告详情
     * User: qiaohao
     * Date: 2023/2/23 19:31
     */
    public function announcementDetail($id)
    {
        $model = Announcement::find($id);
        return $this->success($model);
    }

    /**
     * 操作指南列表
     * User: qiaohao
     * Date: 2023/2/23 19:31
     */
    public function instruction()
    {
        $list = Instruction::where('is_online', 1)
            ->orderBy('sort', 'asc')
            ->select("id", "title")
            ->get();
        return $this->success($list);
    }

    /**
     * 操作指南详情
     * User: qiaohao
     * Date: 2023/2/23 19:31
     */
    public function instructionDetail($id)
    {
        $model = Instruction::find($id);
        return $this->success($model);
    }

    //获取配置
    public function getConfig(Request $request)
    {
        $names=$request->input('name',[]);
        $configs=Option::getCacheConfig();
        return $this->success($configs);
    }
}
