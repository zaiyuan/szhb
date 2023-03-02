<?php

namespace App\Http\Controllers;

use App\Tools\websocket\WebSocketClient;

class UtilController extends Controller
{
    public function test()
    {
        try {
            $ws = new WebSocketClient('wss://api.huobi.pro:80/ws');
            var_dump($ws->ping());
            $ws->send('market.$symbol.ticker');
            $frame = $ws->recv();
            echo "收到服务器响应数据：" . $frame->playload . PHP_EOL;
            var_dump($ws->close());

        } catch (\Exception $e) {
            echo "错误: " ;
            var_dump($e->__toString());
        }
    }
}
