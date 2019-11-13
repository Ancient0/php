<?php

class Excel
{
    /**
     * 导入excel
     */
    public function implode($file)
    {
        if (!file_exists($file)) {
            exit('文件不存在');
        }
        $phpExcel = PHPExcel_IOFactory::load($file);
        $row = $phpExcel->getActiveSheet()->getHighestRow();
        $column = $phpExcel->getActiveSheet()->getHighestColumn();
        $data = [];
        for ($i = 1; $i <= $row; $i++) {
            for ($c = 'A'; $c <= $column; $c++) {
                $data[] = $phpExcel->getActiveSheet()->getCell($c . $i)->getValue();
            }
        }
        $result = explode(' ', $data[0]);
        return $result;
    }

    /**
     * 导出excel
     * @param $name 文件名
     * @param $data 数据
     * @param $title 映射
     * @throws Exception
     */
    public function explode($name, $data, $title)
    {
        //文件名
        $sheetName = $name;
        $saveFile = $sheetName . date('Y-m-d');

        //创建PHPExcel实例
        $excel = new PHPExcel();

        //设置excel属性
        $objActSheet = $excel->getActiveSheet();
        //设置当前的sheet
        $excel->setActiveSheetIndex(0);
        //设置sheet的name
        $objActSheet->setTitle($sheetName);

        $title = [
            'call_id' => '呼叫唯一id',
            'call_number' => '主叫号码',
            'called_number' => '被叫号码',
            'status' => '状态',
            'call_time' => '呼叫时间',
            'talk_time' => '通话时长',
            'result' => '结果',
            'end_reason' => '结束原因',
            'key_detail' => '按键情况'
        ];
        $char = getChar(count($title));

        $k = 0;
        //循环表头
        foreach ($title as $field => $v) {
            //设置表头字体是否加粗
            $objActSheet->getStyle($char[$k] . '1')->getFont()->setBold(true);
            $objActSheet->setCellValueExplicit($char[$k] . '1', $v);
            $k++;
        }

        //循环数据
        foreach ($data as $d_k => $v) {
            $i = 0;
            foreach ($title as $key => $value) {
                $objActSheet->setCellValueExplicit($char[$i] . (2 + $d_k), $v[$key]);
                $objActSheet->getColumnDimension($char[$i])->setWidth(13.71); //设置列宽
                $objActSheet->getStyle($char[$i])->getAlignment()->setWrapText(true); //自动换行
                $i++;
            }
            //设置数据行高
            $objActSheet->getRowDimension($d_k + 2)->setRowHeight(20);
        }
        $objActSheet->getColumnDimension('B')->setWidth(25.22); //设置列宽

        $finalChar = $char[count($char) - 1];
        //设置行高
        $objActSheet->getRowDimension(1)->setRowHeight(27);
        //设置字体大小
        $objActSheet->getStyle("A1:{$finalChar}999")->getFont()->setSize(10);
        //设置表头字体样式
        $objActSheet->getStyle("A1:{$finalChar}999")->getFont()->setName('微软雅黑');
        //设置垂直居中
        $objActSheet->getStyle("A1:{$finalChar}999")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objActSheet->getStyle("A1:{$finalChar}999")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //$write = new \PHPExcel_Writer_Excel2007($excel);
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="' . $saveFile . '.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
        echo '<script>alert("打印完成")</script>';
    }
}

if (!function_exists('getChar')) {
    /**
     * @param $len
     * @return array
     * 获取excel字母列
     */
    function getChar($len)
    {
        $st = 65; //A
        $charArr = array();
        for ($i = 0; $i <= $len - 1; $i++) {
            $charArr[] = chr($st + $i);
        }
        return $charArr;
    }
}