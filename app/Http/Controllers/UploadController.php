<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $dir=$request->input('dir','temp');
        $filetype=$request->input('filetype','image');
        $uploadHelper=new UploadHelper();
        $uploadHelper->upload_dir=$dir;
        $uploadHelper->filetype=$filetype;
        $res=$uploadHelper->uploadFile();
        if(!$res['success']){
            throw new \Exception($res['msg']);
        }
        return $this->success($res['data']);
    }
}
