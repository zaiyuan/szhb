<?php
/**
 * User: qiaohao
 * Date: 2020/7/30
 * Time: 13:36
 */

namespace App\Tools\phpexcel;


use App\lib\ArrayHelper;
use Exception;

require dirname(__FILE__) . '/PHPExcel/IOFactory.php';
require dirname(__FILE__) . '/PHPExcel.php';

class Excelhelper
{
    //导入
    public static function readexcel($filepath)
    {
        if (!file_exists($filepath)) {
            throw new Exception('文件不存在');
        }

        $objPHPExcel = \PHPExcel_IOFactory::load($filepath);
        $data = $objPHPExcel->getSheet(0)->toArray();
        if (count($data) == 0) {
            throw new Exception("文件内容不能为空");
        }
        return $data;
    }

    /**结算导出统一方法
     * @param array $list 导出数据
     * @param array $head_arr 表头(汉字)
     * @param array $key_arr 数据键
     * @param array $width_arr 列宽
     * @param array $param 统计数据
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public static function export($title,$list,$head_arr,$key_arr,$width_arr,$save_path="")
    {
        header("content-type:text/html;charset=utf-8");
        $objPHPExcel = new \PHPExcel();//实例化PHPExcel类，相当于新建一个excel表格

        //设置缓存方式
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
        if (!\PHPExcel_Settings::setCacheStorageMethod($cacheMethod)) {
            throw new Exception("缓存方法不可用");
        }

        $objSheet = $objPHPExcel->getActiveSheet();//获取当前活动sheet的操作对象
        $objSheet->setTitle($title); //给当前活动sheet设置标题
        //设置单元格水平垂直居中
        $objSheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $arr = range('A', 'Z');


        foreach($width_arr as $key=>$row){
            $objSheet->getColumnDimension($arr[$key])->setWidth($width_arr[$key]);
        }

        $objSheet->getStyle("A1:{$arr[count($width_arr)-1]}1")->getFont()->setSize(16)->setBold(True);
        $objSheet->setCellValue('A1', $title);
        $objSheet->mergeCells('A1:' . "{$arr[count($width_arr)-1]}1");

        //核心start
        //设置标题粗体  设置标题
        $objSheet->getStyle("A2:{$arr[count($width_arr)-1]}2")->getFont()->setSize(10)->setBold(True);
        foreach ($head_arr as $key => $row) {
            $objSheet->getStyle($arr[$key])->getAlignment()->setWrapText(true);
            $objSheet->setCellValue($arr[$key] . '2', $row);
        }
        //循环输入内容
        foreach ($list as $key => $row) {
            foreach ($key_arr as $k => $r) {
                $objSheet->getStyle($arr[$k] . (3 + $key))->getAlignment()->setWrapText(true);
                $objSheet->setCellValueExplicit($arr[$k] . (3 + $key), ArrayHelper::getValue($row,$r),\PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        //核心end

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');//按照指定格式生成excel文件
        $filename = $title . date('Ymd'). ".xlsx";
        if($save_path==""){
            self::browser_export('Excel2007', $filename);
        }else{
            if(!is_dir($save_path)){
                mkdir($save_path,0777,true);
            }
        }
        $objWriter->save($save_path?$save_path.'/'.$filename:"php://output");
    }

    public static function browser_export($type, $filename)
    {
        if ($type == 'Excel5') {
            header('Content-Type:application/vnd.ms-excel');
        } else {
            header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }
        header('Content-Disposition:attachment;filename="' . $filename . '"');
        header('Cache-Control:max-age=0');
        header("Access-Control-Allow-Origin:*");
    }

    public static function getCells($index)
    {
        $arr = range('A', 'Z');
        return $arr[$index];
    }
}
