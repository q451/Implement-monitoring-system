<?php
/**
 * phpExcel 组件类
 *
 * @author zaccur<zaccur@juzigo.com>
 * @date 2017-07-10 19:05:16
 */

namespace app\common\components;

use Yii;
use yii\base\Component;
use PHPExcel;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Excel extends Component
{
    public $phpExcel;

    const num2ABC = [
        '','A','B','C','D','E','F',
        'G','H','I','J','K','L','M',
        'N','O','P','Q','R','S','T',
        'U','V','W','X','Y','Z'
    ];
    public function init()
    {
        parent::init();
        $this->phpExcel = new PHPExcel();
    }

    /**
     * 获取Excel数据（导入）
     *
     * @param file     $file  Excel文件
     * @param interger $sheet Excel第几个工作簿，默认为0
     *
     * @return array
     */
    public function import($file, $sheet = 0)
    {
        $exts = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $file['tmp_name'];

        if ($exts == 'xls') {
            $phpReader = new \PHPExcel_Reader_Excel5();
        }elseif ($exts == 'xlsx') {
            $phpReader = new \PHPExcel_Reader_Excel2007();
        }else {
            return [];
        }

        $this->phpExcel = $phpReader->load($filename);

        $currentSheet = $this->phpExcel->getSheet($sheet);

        $allColumn = $currentSheet->getHighestColumn();

        $allRow = $currentSheet->getHighestRow();

        for ($currentRow = 1; $currentRow <= $allRow ; $currentRow++) {
 
            for ($currentColumn = 'A'; $currentColumn <= $allColumn||strlen($currentColumn )<strlen($allColumn); $currentColumn++) {
                $address = $currentColumn.$currentRow;
                
                $cell = $currentSheet->getCell($address)->getFormattedValue();

                if (is_object($cell)) {
                    $cell = $cell->__toString();
                }
                $data[$currentRow][$currentColumn] = $cell;

            }
        }
        return $data;
    }

    /**
     * 数据导出
     * @params array $data 需要导出的数据
     * @params array $header 标题栏
     * @params string $filename 文件名
     *
     */
    public function exportData ($data, $header, $filename = "data", $isOutput = 1, $savePath = '')
    {
        // Add some data
        $this->phpExcel->setActiveSheetIndex(0);
        //添加头部
        $hk = 0;
        foreach ($header as $k => $v)
        {
            $colum = \PHPExcel_Cell::stringFromColumnIndex($hk);
            $this->phpExcel->setActiveSheetIndex(0)->setCellValue($colum."1", $v);
            $hk += 1;
        }
        $column = 2;
        $objActSheet = $this->phpExcel->getActiveSheet();
        if(!empty($data)) {
            foreach ($data as $key => $rows)  //行写入
            {
                $span = 0;
                foreach ($rows as $keyName => $value) // 列写入
                {
                    $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                    // $objActSheet->setCellValue($j.$column, trim($value));
                    $value = $this->handleEmoj($value);
                    $objActSheet->setCellValueExplicit($j . $column, trim($value), \PHPExcel_Cell_DataType::TYPE_STRING);
                    $objActSheet->getStyle($j . $column)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                    $span++;
                }
                $column++;
            }
        }
		
        //导出.xls格式的话使用Excel5,若是想导出.xlsx需要使用Excel2007
        $objWriter= \PHPExcel_IOFactory::createWriter($this->phpExcel,'Excel5');

        if(ob_get_contents()) {
            ob_end_clean();
        }
        ob_start();
		if($isOutput == 1) {
			//设置输出文件名及格式
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:'.$this->downloadFileName($filename));
            header("Content-Transfer-Encoding: binary");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");
			$objWriter->save('php://output');
		}else {
			$objWriter->save($savePath . $filename . '.xls');
		}
        ob_end_flush();
		
        //清空数据缓存
        unset($data);
        /*$spreadsheet = new Spreadsheet();
        $this->phpExcel = new PHPExcel();
        
        
        $objActSheet = $this->phpExcel->getActiveSheet();
            
        // $objActSheet->setTitle($filename);  //设置当前sheet的标题          
        // $objActSheet->getStyle('A1:E1')->getFont()->setBold(true)->setName('Arial')->setSize(10);
        // $objActSheet->getStyle('B1')->getFont()->setBold(true);   
        // $objActSheet->getDefaultColumnDimension()->setWidth(30);

        //设置第一栏的标题
        $worksheet->setCellValue('A1', '交易流水号');
        $worksheet->setCellValue('B1', '开户名');
        $worksheet->setCellValue('C1', '卡号');
        $worksheet->setCellValue('D1', '交易金额');
        $worksheet->setCellValue('E1', '交易时间');
        //$worksheet->setCellValueByColumnAndRow($hk."1", '交易流水号');
        //添加头部
        $hk = 0;
        foreach ($header as $k => $v)
        {
            $colum = \Cell::stringFromColumnIndex($hk);
            $this->phpExcel->setActiveSheetIndex(0)->setCellValue($colum."1", $v);
            $hk += 1;
        }
        //默认填充数据
        $explame_data_list = array(
            array(
                'bank_deal_no' => '1234567890123456',
                'account_name' => '小明',
                'bank_card' => '4231456987436654',
                'deal_money' => '100.00',
                'deal_time' => date("Y-m-d H:i:s"),
            ),
        );

        //第二行起
        $baseRow = 2; //数据从N-1行开始往下输出 这里是避免头信息被覆盖
        foreach ($explame_data_list as $k => $val) {
            $i = $k + $baseRow;
            $worksheet->setCellValue('A' . $i, $val['bank_deal_no']);
            $worksheet->setCellValue('B' . $i, $val['account_name']);
            $worksheet->setCellValue('C' . $i, $val['bank_card']);
            $worksheet->setCellValue('D' . $i, $val['deal_money']);
            $worksheet->setCellValue('E' . $i, $val['deal_time']);;
        }
        
        //处理 数字过大会进行科学计数法
        $worksheet->getStyle('A2')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
        $worksheet->getStyle('C2')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);


        $this->downloadExcel($spreadsheet, '批量导入模板-合同表单选项', 'Xls');*/
    }
    
    /**
     * 不规则数据导出
     * @params array $data 需要导出的数据
     * @params array $header 标题栏
     * @params string $filename 文件名
     *
     */
    public function exportMultiData ($dataOriginal, $data, $header, $filename = "data")
    {
        // Add some data
        $this->phpExcel->setActiveSheetIndex(0);
        //添加头部
        $hk = 0;
        foreach ($header as $k => $v)
        {
            $colum = \PHPExcel_Cell::stringFromColumnIndex($hk);
            $this->phpExcel->setActiveSheetIndex(0)->setCellValue($colum."1", $v);
            $hk += 1;
        }
        $column = 2;
        $objActSheet = $this->phpExcel->getActiveSheet();
        foreach($data as $key => $rows)  //行写入
        {
            $span = 0;
            foreach($rows as $keyName => $value) // 列写入
            {
                $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                // $objActSheet->setCellValue($j.$column, trim($value));
                $value = $this->handleEmoj($value);
                $objActSheet->setCellValueExplicit($j.$column, trim($value), \PHPExcel_Cell_DataType::TYPE_STRING);
                $objActSheet->getStyle($j.$column)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $span++;
            }
            $column++;
        }
        
        //合并列
        $k = 2;
        foreach($dataOriginal as $info)
        {
          $temp = 0;
          foreach($info['judge'] as $judge)
          {
            $temp++;
          }
          $str = 'A'.$k.':A'.($k+$temp-1);
          $this->phpExcel->getActiveSheet()->mergeCells($str);
          $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
          $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
          $str = 'B'.$k.':B'.($k+$temp-1);
          $this->phpExcel->getActiveSheet()->mergeCells($str);
          $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
          $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
          $str = 'C'.$k.':C'.($k+$temp-1);
          $this->phpExcel->getActiveSheet()->mergeCells($str);
          $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
          $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
          $str = 'D'.$k.':D'.($k+$temp-1);
          $this->phpExcel->getActiveSheet()->mergeCells($str);
          $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
          $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
          // $str = 'J'.$k.':J'.($k+$temp-1);
          // $this->phpExcel->getActiveSheet()->mergeCells($str);
          // $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
          // $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
          $k += $temp;
        }
        
        if(ob_get_contents()) {
            ob_end_clean();
        }
        ob_start();

        //设置输出文件名及格式
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:'.$this->downloadFileName($filename));
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");

        //导出.xls格式的话使用Excel5,若是想导出.xlsx需要使用Excel2007
        $objWriter= \PHPExcel_IOFactory::createWriter($this->phpExcel,'Excel5');
        $objWriter->save('php://output');
        ob_end_flush();

        //清空数据缓存
        unset($data);
    }

    /**
     * 数据导出
     * @params array $data 需要导出的数据
     * @params array $header 标题栏
     * @params string $filename 文件名
     * @params array $spectials 下拉框内容 [['column'=>'A','select_options'=>['共青团员','中共党员']]]
     *
     */
    public function exportDataSelectOptions ($data, $header, $filename = "data", $spectials)
    {
        // Add some data
        $this->phpExcel->setActiveSheetIndex(0);
        //添加头部
        $hk = 0;
        foreach ($header as $k => $v)
        {
            // if($hk == 25) {
                // $hk += 1;
                // continue; // 跳过Z列，Z列用于放下拉框内容
            // }
            $colum = \PHPExcel_Cell::stringFromColumnIndex($hk);
            $this->phpExcel->setActiveSheetIndex(0)->setCellValue($colum."1", $v);
            $hk += 1;
        }
        $colum = \PHPExcel_Cell::stringFromColumnIndex($hk);
        $colum = 'FA';
		
        $column = 2;
        $objActSheet = $this->phpExcel->getActiveSheet();
        
        //设置下拉框
        $start = 2;
        $end0 = 2;
        foreach($spectials as $spectial)
        {
            $optionsString = implode(',', $spectial['select_options']);
            // 若下拉框内容超过255，则使用以下方法，将每个来源字串分解到一个空闲的单元格中
            $str_len = strlen($optionsString);
            $formula1 = "";
            if ($str_len >= 255) {
                $str_list_arr = $spectial['select_options']; 
                if ($str_list_arr) {
                    foreach ($str_list_arr as $d) {
                        $c = $colum . $end0;
                        $objActSheet->setCellValue($c,$d);
                        $end0 ++;
                    } 
                }
                $endcell = $c;
                $objActSheet->getColumnDimension($colum)->setVisible(false);
                $formula1 = $objActSheet->getTitle() . "!$" . $colum . "$" . $start . ":$" . $colum . "$" . ($end0 - 1);
                $start = $end0;

                //若有多列超过255，则递增最后字符，注意这里如果超过26列多于255字符会出问题
                //（但我估计应该不会有这么多列多于255字符的选项吧）
                $latestC = ord($colum[1]);
                $latestC ++;
                $colum[1] = chr($latestC);
            }else {
                $formula1 = '"' . $optionsString . '"'; 
            }
            
            $n = 2;
            while($n < 1000) {
                $objValidation = $objActSheet->getCell($spectial['column'].(string)$n)->getDataValidation(); //这一句为要设置数据有效性的单元格
                $objValidation -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
                -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_STOP)
                -> setAllowBlank(true)
                -> setShowInputMessage(true)
                -> setShowErrorMessage(true)
                -> setShowDropDown(true)
                -> setErrorTitle('输入的值有误')
                -> setError('您输入的值不在下拉框列表内.')
                -> setPromptTitle('')
                -> setPrompt('')
                -> setOperator(\PHPExcel_Cell_DataValidation::OPERATOR_BETWEEN)
                -> setFormula1($formula1);
                
                $n++;
            }
        }
        foreach($data as $key => $rows)  //行写入
        {
            $span = 0;
            foreach($rows as $keyName => $value) // 列写入
            {
                if($span == 25) {
                    $span++;
                    continue; // 跳过Z列，Z列用于放下拉框内容
                }
                $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                // $objActSheet->setCellValue($j.$column, trim($value));
                $value = $this->handleEmoj($value);
                $objActSheet->setCellValueExplicit($j.$column, trim($value), \PHPExcel_Cell_DataType::TYPE_STRING);
                $objActSheet->getStyle($j.$column)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $span++;
            }
            $column++;
        }
        if(ob_get_contents()) {
            ob_end_clean();
        }
        ob_start();

        $objWriter = new \PHPExcel_Writer_Excel5($this->phpExcel);
        //设置输出文件名及格式
        // header('Content-Type : application/vnd.ms-excel');
        // header('Content-Disposition:attachment;filename="'.$filename.'.xls"');

        //导出.xls格式的话使用Excel5,若是想导出.xlsx需要使用Excel2007
        // $objWriter= \PHPExcel_IOFactory::createWriter($this->phpExcel,'Excel5');
        // $objWriter->save('php://output');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:'.$this->downloadFileName($filename));
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
        ob_end_flush();

        //清空数据缓存
        unset($data);
    }

    /**
     * 数据导出（多sheet）
     * @params array $data 需要导出的数据 [['oneSheetData'=>[],'header'=>[],'title'=>'']]
     * @params array $header 标题栏
     * @params string $filename 文件名
     *
     */
    public function exportDataMultiSheet ($allData, $filename = "data", $isOutput = 1, $savePath = '')
    {
        $header = [];
        $data = [];
        $title = '';
        foreach($allData as $key => $oneSheetData)
        {
            $data = $oneSheetData['oneSheetData'];
            $header = $oneSheetData['header'];
            $title = $oneSheetData['title'] ?? (string)$key;
            // Add some data
            if($key > 0)
                $this->phpExcel->createSheet();
            $this->phpExcel->setActiveSheetIndex($key);
            $this->phpExcel->getActiveSheet()->setTitle($title);
            $objActSheet = $this->phpExcel->getActiveSheet();
            //添加头部
            $hk = 0;
            foreach ($header as $k => $v)
            {
                $colum = \PHPExcel_Cell::stringFromColumnIndex($hk);
                $objActSheet->setCellValue($colum."1", $v);
                $hk += 1;
            }
            $column = 2;
            foreach($data as $key2 => $rows)  //行写入
            {
                $span = 0;
                foreach($rows as $keyName => $value) // 列写入
                {
                    $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                    // $objActSheet->setCellValue($j.$column, trim($value));
                    $value = $this->handleEmoj($value);
                    $objActSheet->setCellValueExplicit($j.$column, trim($value), \PHPExcel_Cell_DataType::TYPE_STRING);
                    $objActSheet->getStyle($j.$column)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                    $span++;
                }
                $column++;
            }
        }
        //清空数据缓存
        unset($allData);
        
        //导出.xls格式的话使用Excel5,若是想导出.xlsx需要使用Excel2007
        $objWriter= \PHPExcel_IOFactory::createWriter($this->phpExcel,'Excel5');

        if(ob_get_contents()) {
            ob_end_clean();
        }
        ob_start();
		if($isOutput == 1) {
			//设置输出文件名及格式
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:'.$this->downloadFileName($filename));
            header("Content-Transfer-Encoding: binary");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");

			$objWriter->save('php://output');
		}else {
			$objWriter->save($savePath . $filename . '.xls');
		}
        ob_end_flush();
    }

    /**
     * 获取下载文件所需headers头文件名信息
     *
     * @param string $filename
     * @return string
     */
    private function downloadFileName($filename)
    {
//        // 兼容各浏览器下载文件名乱码问题
//        if (preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"])) {
//            $filename = urlencode($filename);
//            $filename = str_replace("+", "%20", $filename);// 替换空格
//            $attachment = 'attachment; filename="' . $filename .'.xls"';
//        }else if (preg_match("/Chrome/", $_SERVER["HTTP_USER_AGENT"])) {
//            $attachment = 'attachment; filename="' . $filename . '.xls"';
//        }else if (preg_match("/Firefox/", $_SERVER["HTTP_USER_AGENT"])) {
//            $attachment = 'attachment; filename*="utf-8\'\'' . $filename.'.xls"';
//        }else if (preg_match("/Safari/", $_SERVER["HTTP_USER_AGENT"])) {
////            $filename = iconv("UTF-8", "ISO-8859-1", $filename);
//            $filename = mb_convert_encoding($filename, "ISO-8859-1");
////            $filename = rawurlencode($filename.'.xls'); // 注意：rawurlencode与urlencode的区别
//            $attachment = 'attachment; filename*="' . $filename . '.xls"';
//        }else {
//            $attachment = 'attachment; filename="' . $filename . '.xls"';
//        }
        $attachment = 'attachment; filename="' . $filename . '.xls"';
        return $attachment;
    }

    private function handleEmoj($value) {
        $value = json_encode($value);
        $value = preg_replace("/\\\u[ed][0-9a-f]{3}\\\u[ed][0-9a-f]{3}/","*",$value);//替换成*
        return json_decode($value);
    }
}
