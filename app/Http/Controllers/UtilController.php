<?php

namespace App\Http\Controllers;

use App\Helpers\EmailHelper;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Exception;

class UtilController extends Controller
{
    public function test()
    {
        $emailHelper=new EmailHelper();
        $emailHelper->sendCode('1049645051@qq.com','qiaohao','10496');
        return $this->success([]);
    }
}
