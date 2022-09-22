<?php
/**
 * phpWord 组件类
 *
 * @author heyijia
 * @date 2018-12-06
 */

namespace app\common\components;

use app\common\utils\StringMatch;
use app\modules\config\service\ConfigService;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\TablePosition;
use app\modules\counseling\service\RecordService;
use app\modules\user\service\StudentService;
use app\modules\common\service\XinxiInfoService;

class Word extends Component
{
    public $phpWord;

    public function init()
    {
        parent::init();
    }
		
	// 生成辅导记录
    public function createCounselingRecordWord($recordId, $filePath)
    {
		$recordInfo = (new RecordService)->info([
			'condition' => [
				'status' => 1,
				'id' => $recordId
			],
			'with' => ['teacherInfo', 'tags', 'categorys', 'questions'],
		]);
		$studentInfo = (new StudentService)->info([
			'condition' => [
				'status' => 1,
				'student_id' => $recordInfo['student_id'],
			],
			'with' => ['classInfo', 'gradeInfo', 'collegeInfo', 'instructorInfo'],
		]);

		if(empty($recordInfo) || empty($studentInfo)) {
		    return;
        }
		$information['name'] = $studentInfo['name']??'';
		$information['counselor'] = $recordInfo['teacherInfo']['name']??'';
		$information['instructor'] = $studentInfo['instructorInfo']['name']??'';
		$information['counseling_time'] = date("Y-m-d", $recordInfo['create_timestamp']/1000);
		$information['counseling_time2'] = date("Ymd", $recordInfo['create_timestamp']/1000);
		$information['college'] = $studentInfo['collegeInfo']['name']??'';
		$information['grade'] = $studentInfo['gradeInfo']['name']??'';
		$information['class'] = $studentInfo['classInfo']['name']??'';
		
		$information['major'] = $studentInfo['zym'];//专业
		$information['student_id'] = $recordInfo['student_id']??'';
		$information['sex'] = $studentInfo['xb'];//性别
		$information['nationality'] = $studentInfo['mz'];//民族
		$information['phone'] = $studentInfo['contact_info']??'';
		$information['political_status'] = $studentInfo['political_status']??'';
		$information['e_info'] = $studentInfo['email'];//电子信息
		$information['religious'] = $studentInfo['religious']??'';
		$information['birthplace'] = $studentInfo['jg'];//籍贯
		
		$information['category'] = '';
		$flag = 1;
		foreach($recordInfo['categorys'] as $categoryInfo){
			if($flag == 1){
				$information['category'] = $categoryInfo['categoryInfo']['name'];
				$flag = 0;
			}
			else
				$information['category'] = $information['category'].'，'.$categoryInfo['categoryInfo']['name'];
		}
		
		$information['question'] = [];
		foreach($recordInfo['questions'] as $questionInfo){
			$information['question'][] = '问：'.$questionInfo['questionInfo']['title'];
			$information['question'][] = '答：'.$questionInfo['answer'];
			$information['question'][] = '建议：'.$questionInfo['suggestion'];
		}
		
		$information['tag'] = '';
		$flag = 1;
		foreach($recordInfo['tags'] as $tagInfo){
			if($flag == 1){
				$information['tag'] = $tagInfo['tagInfo']['name'] ?? '';
				$flag = 0;
			}
			else
				$information['tag'] = $information['tag'].'，'.($tagInfo['tagInfo']['name'] ?? '');
		}

        $localPathList = [];
        $imgSrcList = StringMatch::getImgAllSrc($recordInfo['summary']);
		$information['summary'] = $this->extractStringFromHtml($recordInfo['summary']);
		$information['is_red'] = ($recordInfo['is_counsel_red'] == 1)?'标记为重点学生':'未标记为重点学生';
		$information['referral_department'] = '建议转介部门：'.$recordInfo['referral_department'];

        // 定义style
        $style = [
            'basicBreak' => [
                'font' => array('size' => 18),
                'para' => array('spacing' => 50),
            ],
            'header' => [
                'font' => array('size' => 16, 'bold' => true, 'name' => '黑体'),
                'para' => array('align' => 'center'),
            ],
            'header1' => [
                'font' => array('size' => 24, 'bold' => true, 'name' => '黑体'),
                'para' => array('align' => 'center'),
            ],
            'table1' => [
            	'height' => 1200,
            	'table' => array('cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellSpacing' => 50),
            	'cell' => array('valign' => 'center'),
            	'left' => [
            		'width' => 2010,
            		'font' => array('size' => 14, 'name' => '黑体'),
            		'para' => array('spacing' => 100),
            	],
            	'right' => [
            		'width' => 4510,
            		'font' => array('size' => 14, 'name' => '黑体', 'underline' => \PhpOffice\PhpWord\Style\Font::UNDERLINE_SINGLE),
            		'para' => array('spacing' => 100),
            	],
            ],
            'class' => [
                'font' => array('size' => 14, 'name' => '黑体'),
                'para' => array('indent' => 1.5, 'spacing' => 200),
            ],
            'date' => [
                'font' => array('size' => 14, 'name' => '黑体'),
                'para' => array('align' => 'center', 'spacing' => 200),
            ],
            'decDate' => [
                'font' => array('size' => 12, 'name' => '宋体'),
                'para' => array('align' => 'right', 'spacing' => 50),
            ],
            'mainTable' => [
                'table' => array('cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellSpacing' => 50, 'borderSize' => 6, 'borderColor' => '000000'),
                'basicFont' => array('size' => 14, 'name' => '宋体'),
                'basicPara' => array('spacing' => 50),
                'centerStyle' => array('spacing' => 50,'align'=>'center'),
                'eight-layer' => [
                    'height' => 600,
                    'width' => 1125,
                    'cell' => array('gridSpan' => 1),
                ],
                'one-seven-layer' => [
                    'height' => 600,
                    'leftWidth' => 1125,
                    'rightWidth' => 7875,
                    'leftCell' => array('gridSpan' => 1),
                    'rightCell' => array('gridSpan' => 7),
                ],
                'one-seven-big-layer' => [
                    'height' => 1800,
                    'leftWidth' => 1125,
                    'rightWidth' => 7875,
                    'leftCell' => array('gridSpan' => 1),
                    'rightCell' => array('gridSpan' => 7),
                ],
                'complex-layer' => [
                    'height' => 600,
                    'leftWidth' => 1125,
                    'leftCell' => array('vMerge' => 'restart', 'gridSpan' => 1, 'valign' => 'center'),
                    'oneWidth' => 1125,
                    'oneCell' => array('gridSpan' => 1, 'valign' => 'center'),
                    'oneMergeCell' => array('vMerge' => 'continue', 'gridSpan' => 1, 'valign' => 'center'),
                    'twoWidth' => 2250,
                    'twoCell' => array('gridSpan' => 2, 'valign' => 'center'),
                    'cellRowContinue' => array('vMerge' => 'continue', 'gridSpan' => 2),
                ],
                'complex2-layer' => [
                    'height' => 600,
                    'leftWidth' => 1125,
                    'leftCell' => array('vMerge' => 'restart', 'gridSpan' => 1, 'valign' => 'center'),
                    'rightWidth' => 7875,
                    'rightCell' => array('gridSpan' => 7, 'valign' => 'center'),
                    'allWidth' => 9000,
                    'allCell' => array('gridSpan' => 8, 'valign' => 'center'),
                    'mergeCell' => array('vMerge' => 'continue', 'gridSpan' => 1, 'valign' => 'center'),
                    'cellRowContinue' => array('vMerge' => 'continue', 'gridSpan' => 2),
                ],
            ],
        ];
				
		$this->phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $this->phpWord->addSection();

        // 标题
        $section->addText($information['name'].'_'.$information['student_id'].'_'.$information['counseling_time2'].'深度辅导过程记录', $style['header']['font'], $style['header']['para']);
        $section->addTextBreak(1, $style['basicBreak']['font'], $style['basicBreak']['para']);

        // 项目具体信息表格, 将表格划分成几部分循环(相同样式的在同一部分)
        $mainTable = $section->addTable($style['mainTable']['table']);

        $data = [
            '姓名' => $information['name'],
            '辅导人' => $information['counselor'],
            '辅导员' => $information['instructor'],
            '辅导日期' => $information['counseling_time'],
        ];
        $mainTable->addRow($style['mainTable']['eight-layer']['height']);
        foreach ($data as $key => $value) {
            $mainTable->addCell($style['mainTable']['eight-layer']['width'], $style['mainTable']['eight-layer']['cell'])->addText($key, $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
            $mainTable->addCell($style['mainTable']['eight-layer']['width'], $style['mainTable']['eight-layer']['cell'])->addText($value, $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
        }
        
		$mainTable->addRow($style['mainTable']['complex-layer']['height']);
        $complexStyle = $style['mainTable']['complex-layer'];
		$mainTable->addCell($complexStyle['leftWidth'], $complexStyle['leftCell'])->addText('基本信息', $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);

        $school = (new ConfigService())->getConfig('system', 'param')['school'] ?? '';
        if($school == '中央民族大学'){
            $data = [
                [
                    '学院' => $information['college'],
                    '年级' => $information['grade'],
                    '班级' => $information['class'],
                ],
                [
                    '专业' => $information['major'],
                    '学号' => $information['student_id'],
                    '性别' => $information['sex'],
                ],
                [
                    '民族' => $information['nationality'],
                    '联系方式' => $information['phone'],
                    '政治面貌' => $information['political_status'],
                ],
                [
                    '电子邮箱' => $information['e_info'],
                    '籍贯' => $information['birthplace'],
                    '' => '',
                ],
            ];
        }else{
            $data = [
                [
                    '学院' => $information['college'],
                    '年级' => $information['grade'],
                    '班级' => $information['class'],
                ],
                [
                    '专业' => $information['major'],
                    '学号' => $information['student_id'],
                    '性别' => $information['sex'],
                ],
                [
                    '民族' => $information['nationality'],
                    '联系方式' => $information['phone'],
                    '政治面貌' => $information['political_status'],
                ],
                [
                    '电子邮箱' => $information['e_info'],
                    '宗教信仰' => $information['religious'],
                    '籍贯' => $information['birthplace'],
                ],
            ];
        }

		$temp = 0;
		foreach ($data as $array) {
			if($temp == 1){
				$mainTable->addRow($style['mainTable']['complex-layer']['height']);
				$mainTable->addCell($complexStyle['oneWidth'], $complexStyle['oneMergeCell']);
			}
			$temp = 1;
			$flag = 1;
			foreach($array as $key => $value){
				$mainTable->addCell($complexStyle['oneWidth'], $complexStyle['oneCell'])->addText($key, $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
				if($flag == 1){
					$mainTable->addCell($complexStyle['twoWidth'], $complexStyle['twoCell'])->addText($value, $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
					$flag = 0;
				}else
					$mainTable->addCell($complexStyle['oneWidth'], $complexStyle['oneCell'])->addText($value, $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
			}
		}
		//辅导方向
		$mainTable->addRow($style['mainTable']['one-seven-layer']['height']);
		$mainTable->addCell($style['mainTable']['one-seven-layer']['leftWidth'], $style['mainTable']['one-seven-layer']['leftCell'])->addText('辅导方向', $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
        $mainTable->addCell($style['mainTable']['one-seven-layer']['rightWidth'], $style['mainTable']['one-seven-layer']['rightCell'])->addText($information['category'], $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
        //基础提问
		$mainTable->addRow($style['mainTable']['one-seven-big-layer']['height']);
		$mainTable->addCell($style['mainTable']['one-seven-big-layer']['leftWidth'], $style['mainTable']['one-seven-big-layer']['leftCell'])->addText('基础提问', $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);	
		$cell = $mainTable->addCell($style['mainTable']['one-seven-big-layer']['rightWidth'], $style['mainTable']['one-seven-big-layer']['rightCell']);
		foreach($information['question'] as $questionInfo){
			$cell->addText($questionInfo, $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
		}
		//辅导总结
		$mainTable->addRow($style['mainTable']['one-seven-big-layer']['height']);
		$mainTable->addCell($style['mainTable']['one-seven-big-layer']['leftWidth'], $style['mainTable']['one-seven-big-layer']['leftCell'])->addText('辅导总结', $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
        $section = $mainTable->addCell($style['mainTable']['one-seven-big-layer']['rightWidth'], $style['mainTable']['one-seven-big-layer']['rightCell']);
        $section->addText($information['summary'], $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
        $imageStyle = array('align'=>'center');
        foreach($imgSrcList as $imgSrc) {
            // 获取远程文件的内容
            $fileCon = file_get_contents($imgSrc);
            // 把上面获取到的文件内容写入本地文件
            $localPath = Yii::$app->params['downloadTmpPath'] . basename($imgSrc);
            $res = file_put_contents($localPath, $fileCon);

            $compressPath = Yii::$app->params['downloadTmpPath'] . 'compress' . basename($imgSrc);
            $this->compressedImage($localPath, $compressPath);
            $localPathList[] = $localPath;
            $localPathList[] = $compressPath;
            if($res) {
                $section->addImage($compressPath, $imageStyle);
            }
        }

        //人员标签
        $mainTable->addRow($style['mainTable']['one-seven-layer']['height']);
		$mainTable->addCell($style['mainTable']['one-seven-layer']['leftWidth'], $style['mainTable']['one-seven-layer']['leftCell'])->addText('人员标签', $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
        $mainTable->addCell($style['mainTable']['one-seven-layer']['rightWidth'], $style['mainTable']['one-seven-layer']['rightCell'])->addText($information['tag'], $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);

        $mainTable->addRow($style['mainTable']['complex2-layer']['height']);
        $complexStyle = $style['mainTable']['complex2-layer'];
				
//		$mainTable->addCell($complexStyle['leftWidth'], $complexStyle['leftCell'])->addText('转介处理', $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
		$mainTable->addCell($complexStyle['allWidth'], $complexStyle['allCell'])->addText($information['is_red'], $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
		
//		$mainTable->addRow($style['mainTable']['complex2-layer']['height']);
//		$mainTable->addCell($complexStyle['rightWidth'], $complexStyle['mergeCell']);
//		$mainTable->addCell($complexStyle['rightWidth'], $complexStyle['rightCell'])->addText($information['referral_department'], $style['mainTable']['basicFont'], $style['mainTable']['basicPara']);
				
        $objWriter= \PhpOffice\PhpWord\IOFactory::createWriter($this->phpWord, 'Word2007');

        if(file_exists($filePath)) {
            unlink($filePath);
        }
        $objWriter->save($filePath);
        foreach($localPathList as $item) {
            if(file_exists($item)) {
                unlink($item);
            }
        }
    }

    private function extractStringFromHtml($htmlString) {
        $htmlString = str_replace("<br>"," ", $htmlString);
        $htmlString = str_replace("</p>"," ", $htmlString);
        // 把一些预定义的 HTML 实体转换为字符
        // 预定义字符是指:<,>,&等有特殊含义(<,>,用于链接签,&用于转义),不能直接使用
        $htmlString = htmlspecialchars_decode($htmlString);

        // 将空格去除
        // $html_string = str_replace(" ", "", $html_string);

        // 去除字符串中的 HTML 标签
        $contents = strip_tags($htmlString);

        return $contents;
    }

    /**
     * desription 压缩图片
     * @param string $imgsrc 图片路径
     * @param string $imgdst 压缩后保存路径
     */
    public function compressedImage($imgsrc, $imgdst) {
        list($width, $height, $type) = getimagesize($imgsrc);

        $new_width = $width;//压缩后的图片宽
        $new_height = $height;//压缩后的图片高

        if($width >= 350){
            $per = 350 / $width;//计算比例
            $new_width = $width * $per;
            $new_height = $height * $per;
        }

        switch ($type) {
            case 1:
                $giftype = check_gifcartoon($imgsrc);
                if ($giftype) {
                    header('Content-Type:image/gif');
                    $image_wp = imagecreatetruecolor($new_width, $new_height);
                    $image = imagecreatefromgif($imgsrc);
                    imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    //90代表的是质量、压缩图片容量大小
                    imagejpeg($image_wp, $imgdst, 90);
                    imagedestroy($image_wp);
                    imagedestroy($image);
                }
                break;
            case 2:
                header('Content-Type:image/jpeg');
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromjpeg($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                //90代表的是质量、压缩图片容量大小
                imagejpeg($image_wp, $imgdst, 90);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
            case 3:
                header('Content-Type:image/png');
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefrompng($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                //90代表的是质量、压缩图片容量大小
                imagejpeg($image_wp, $imgdst, 90);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
        }
    }
}