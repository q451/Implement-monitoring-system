<?php
/**
 * Dompdf 组件类
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-07-07 11:13:58
 */

namespace app\common\components;

use Yii;
use yii\base\Component;
use Dompdf\Dompdf;

class Pdf extends Component
{
    public $dompdf;

    public function init()
    {
        parent::init();
        $this->dompdf = new Dompdf();
        $this->dompdf->set_option('defaultFont', 'arial');
        $this->dompdf->set_option('isFontSubsettingEnabled', true);
        $this->dompdf->set_option('isRemoteEnabled', true);
        $this->dompdf->setPaper('A4', 'portrait');
    }

    /**
     * 从 html 生成 pdf
     *
     * @param string $html 要生成 PDF 的 HTML 字符串
     * @param string $name 生成的 PDF 文件名
     * @param boolean $isAttachment 是否弹出文件提示下载（默认为 false，直接在浏览器显示 PDF 文件）
     *
     * @return null
     */
    public function generateFromHtml($html, $name = 'default', $isAttachment = false)
    {
        header('Access-Control-Allow-Origin: ' . Yii::$app->response->accessOrigin);
        header('Access-Control-Allow-Headers: ' . Yii::$app->response->allowHeaders);
        header('Access-Control-Allow-Credentials: ' . Yii::$app->response->allowCredentials);

        $this->dompdf->loadHtml($html, 'UTF-8');
        $this->dompdf->render();
        $this->dompdf->stream($name, array('Attachment' => $isAttachment));
    }
}
