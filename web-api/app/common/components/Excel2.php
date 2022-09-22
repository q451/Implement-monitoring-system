<?php
/**
 * PhpSpreadsheet 组件类
 *
 * @author mazhenling<1779813868@qq.com>
 * @date 2020-09-14 21:42:16
 */

namespace app\common\components;

use Yii;
use yii\base\Component;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use \PhpOffice\PhpSpreadsheet\Style\Alignment;
use \PhpOffice\PhpSpreadsheet\Cell\DataValidation;
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
        $this->phpExcel = new Spreadsheet();
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

        if (strtolower($exts) == 'xls') {
            $phpReader = IOFactory::createReader('Xls');
        }elseif (strtolower($exts) == 'xlsx') {
            $phpReader = IOFactory::createReader('Xlsx');
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
    public function exportData ($data, $header, $filename = "data")
    {
        // Add some data
        $this->phpExcel->setActiveSheetIndex(0);
        //添加头部
        $hk = 0;
        foreach ($header as $k => $v)
        {
            $colum = Coordinate::stringFromColumnIndex($hk);
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
                $j = Coordinate::stringFromColumnIndex($span);
                // $objActSheet->setCellValue($j.$column, trim($value));
                $objActSheet->setCellValueExplicit($j.$column, trim($value), DataType::TYPE_STRING);
                $objActSheet->getStyle($j.$column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $span++;
            }
            $column++;
        }
        ob_end_clean();
        ob_start();
        
        $format = 'Xls';
        if (strtolower($format) == 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif (strtolower($format) == 'xls') {
            header('Content-Type: application/vnd.ms-excel');
        }
        //设置输出文件名及格式
        header('Content-Disposition:attachment;filename='.$filename.'.'.strtolower($format));

        //导出.xls格式的话使用Excel5,若是想导出.xlsx需要使用Excel2007
        $objWriter= IOFactory::createWriter($this->phpExcel, $format);
        $objWriter->save('php://output');
        ob_end_flush();

        //清空数据缓存
        unset($data);
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
            $colum = Cell::stringFromColumnIndex($hk);
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
                $j = Cell::stringFromColumnIndex($span);
                // $objActSheet->setCellValue($j.$column, trim($value));
                $objActSheet->setCellValueExplicit($j.$column, trim($value), DataType::TYPE_STRING);
                $objActSheet->getStyle($j.$column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
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
            $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $str = 'B'.$k.':B'.($k+$temp-1);
            $this->phpExcel->getActiveSheet()->mergeCells($str);
            $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $str = 'C'.$k.':C'.($k+$temp-1);
            $this->phpExcel->getActiveSheet()->mergeCells($str);
            $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $str = 'D'.$k.':D'.($k+$temp-1);
            $this->phpExcel->getActiveSheet()->mergeCells($str);
            $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // $str = 'J'.$k.':J'.($k+$temp-1);
            // $this->phpExcel->getActiveSheet()->mergeCells($str);
            // $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            // $this->phpExcel->getActiveSheet()->getStyle($str)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $k += $temp;
        }
        
        ob_end_clean();
        ob_start();
        
        $format = 'Xls';
        if (strtolower($format) == 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif (strtolower($format) == 'xls') {
            header('Content-Type: application/vnd.ms-excel');
        }
        //设置输出文件名及格式
        header('Content-Disposition:attachment;filename='.$filename.'.'.strtolower($format));

        //导出.xls格式的话使用Excel5,若是想导出.xlsx需要使用Excel2007
        $objWriter= IOFactory::createWriter($this->phpExcel, $format);
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
     * @params array $spectials 文件名 [['column'=>'A','select_options'=>['共青团员','中共党员']]]
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
            $colum = Cell::stringFromColumnIndex($hk);
            $this->phpExcel->setActiveSheetIndex(0)->setCellValue($colum."1", $v);
            $hk += 1;
        }
        
        $column = 2;
        $objActSheet = $this->phpExcel->getActiveSheet();
        
        //设置下拉框
        foreach($spectials as $spectial)
        {
            $optionsString = implode(',', $spectial['select_options']);
            
            $n = 2;
            while($n < 1000) {
                $objValidation = $objActSheet->getCell($spectial['column'].(string)$n)->getDataValidation(); //这一句为要设置数据有效性的单元格
                $objValidation -> setType(DataValidation::TYPE_LIST)
                -> setErrorStyle(DataValidation::STYLE_STOP)
                -> setAllowBlank(true)
                -> setShowInputMessage(true)
                -> setShowErrorMessage(true)
                -> setShowDropDown(true)
                -> setErrorTitle('输入的值有误')
                -> setError('您输入的值不在下拉框列表内.')
                -> setPromptTitle('')
                -> setPrompt('')
                -> setOperator(DataValidation::OPERATOR_BETWEEN)
                -> setFormula1('"'.$optionsString.'"');
                
                $n++;
            }
        }
        foreach($data as $key => $rows)  //行写入
        {
            $span = 0;
            foreach($rows as $keyName => $value) // 列写入
            {
                $j = Cell::stringFromColumnIndex($span);
                // $objActSheet->setCellValue($j.$column, trim($value));
                $objActSheet->setCellValueExplicit($j.$column, trim($value), DataType::TYPE_STRING);
                $objActSheet->getStyle($j.$column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $span++;
            }
            $column++;
        }
        ob_end_clean();
        ob_start();

        $format = 'Xls';
        if (strtolower($format) == 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif (strtolower($format) == 'xls') {
            header('Content-Type: application/vnd.ms-excel');
        }
        //设置输出文件名及格式
        header('Content-Disposition:attachment;filename='.$filename.'.'.strtolower($format));

        //导出.xls格式的话使用Excel5,若是想导出.xlsx需要使用Excel2007
        $objWriter= IOFactory::createWriter($this->phpExcel, $format);
        $objWriter->save('php://output');
        ob_end_flush();
        // header("Content-Type: application/force-download");
        // header("Content-Type: application/octet-stream");
        // header("Content-Type: application/download");
        // header('Content-Disposition:inline;filename="'.$filename.'.xls"');
        // header("Content-Transfer-Encoding: binary");
        // header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        // header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        // header("Pragma: no-cache");
        // $objWriter->save('php://output');

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
    public function exportDataMultiSheet ($allData, $filename = "data")
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
                $colum = Cell::stringFromColumnIndex($hk);
                $objActSheet->setCellValue($colum."1", $v);
                $hk += 1;
            }
            $column = 2;
            foreach($data as $key2 => $rows)  //行写入
            {
                $span = 0;
                foreach($rows as $keyName => $value) // 列写入
                {
                    $j = Cell::stringFromColumnIndex($span);
                    // $objActSheet->setCellValue($j.$column, trim($value));
                    $objActSheet->setCellValueExplicit($j.$column, trim($value), DataType::TYPE_STRING);
                    $objActSheet->getStyle($j.$column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $span++;
                }
                $column++;
            }
        }
        //清空数据缓存
        unset($allData);
        
        ob_end_clean();
        ob_start();
        $format = 'Xls';
        if (strtolower($format) == 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif (strtolower($format) == 'xls') {
            header('Content-Type: application/vnd.ms-excel');
        }
        //设置输出文件名及格式
        header('Content-Disposition:attachment;filename='.$filename.'.'.strtolower($format));

        //导出.xls格式的话使用Excel5,若是想导出.xlsx需要使用Excel2007
        $objWriter= IOFactory::createWriter($this->phpExcel, $format);
        $objWriter->save('php://output');
        
        ob_end_flush();
    }
}
