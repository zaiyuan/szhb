<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\WalletRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;

/**
 * 钱包管理
 * App\Http\Controllers\admin
 * @author: qiaohao
 * @Time: 2023/2/21 20:53
 */
class WalletController extends Controller
{
    /**
     * 钱包列表
     * User: qiaohao
     * Date: 2023/2/21 20:54
     */
    public function index(Request $request)
    {
        $params=$request->input();
        $res=(new WalletService())->getList($params);
        return $this->success($res);
    }

    /**
     * 钱包新增
     * User: qiaohao
     * Date: 2023/2/21 21:03
     */
    public function add(WalletRequest $request)
    {
        $params=$request->input();
        (new WalletService())->add($params);
        return $this->success();
    }

    /**
     * 钱包编辑
     * User: qiaohao
     * Date: 2023/2/21 21:12
     */
    public function modify(WalletRequest $request)
    {
        $params=$request->input();
        (new WalletService())->modify($params);
        return $this->success();
    }

    /**
     * 删除钱包
     * @param $id
     * User: qiaohao
     * Date: 2023/2/21 21:14
     */
    public function delete($id)
    {
        (new WalletService())->delete($id);
        return $this->success();
    }
}
