<?php
/**
 * 用户帐号控制器
 *
 */

namespace app\controllers\user;

use Yii;
use yii\helpers\ArrayHelper;
use app\common\base\ApiController;
use app\modules\user\service\IdentityService;
class XiaoHuaController extends ApiController
{
    public function actionGetXiaoHua(){
    
        $info = self::callModuleService('user', 'XiaoHuaService', 'lists', [
            'condition' => [
                'status' => 1,
            ],
        ]);

        return $info;

    }

    public function actionGetJson(){
        $datajson = '
                [
                    
                ]';
        
        $jsons = json_decode($datajson, true);
        foreach($jsons as $item){
            // print_r($item['content']);
            // print_r($item['unixtime']);
            self::callModuleService('user', 'CarStudyService', 'add', [
                'question' => $item['question'],
                'answer' => $item['answer'],
                'explains' => $item['explains'],
                'url' => $item['url'],
                'item1' => $item['item1'],
                'item2' => $item['item2'],
                'item3' => $item['item3'],
                'item4' => $item['item4'],
                'type' => 1,
                'type_car' => 1,
            ]);
        }
    
        return[
            'status' => true
        ];
    }
}