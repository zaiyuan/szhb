<?php
return [
    'type'=>'local',//存储类型：local 本地, ali_oss 阿里云oss, qiniu_oss 七牛云oss

    'suffix_arr'=>[
        'image'=>'jpg,jpeg,png,gif',
        'video'=>'mp4',
        'file'=>'zip,gz,doc,docx,txt,pdf,xls,xlsx'
    ],
    'size_arr'=>[//大小M
        'image'=>2,
        'video'=>100,
        'file'=>100
    ],
];
