<?php
/**
 * JWT实用组件类
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-22 17:26:27
 */

namespace app\common\components;

use Yii;
use yii\base\Component;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;

use RuntimeException;
use InvalidArgumentException;

class Jwt extends Component
{
    // token 相关的信息
    public $issuer       = '';
    public $audience     = '';
    public $jwtId        = '';
    public $signature    = '';
    public $payloadName  = '';
    public $jwtHeaderKey = '';
    public $expireTime   = 0;
    public $signer;

    // token 承载的数据
    public $payloadData = [];

    // 参与 token 验证的控制器和动作
    public $noTokenActions     = [];
    public $noTokenControllers = [];

    public function init()
    {
        parent::init();
        $this->signer = new Sha256();
    }

    /**
     * 创建一个token
     *
     * @param array $payload
     */
    public function createToken($payload = [])
    {
        $token = (new Builder())->setIssuer($this->issuer)
            ->setAudience($this->audience)
            ->setId($this->jwtId, true)
            ->setIssuedAt(time())
            ->setExpiration(time() + $this->expireTime)
            ->set($this->payloadName, $payload)
            ->sign($this->signer, $this->signature)
            ->getToken();
        return (string) $token;
    }

    /**
     * 是否是一个有效的token
     *
     * @param string $token
     *
     * @return boolean
     */
    private function __isValidToken($token) {
        if ($token === null) {
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_TOKEN_NOT_EXISTS'],
                'Token not exists.'
            );
            return false;
        }
        // 解析 token
        try {
            $token = (new Parser())->parse((string) $token);
        } catch (InvalidArgumentException $e) {
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_TOKEN_FORMAT'],
                '自动登录已过期，请重新输入密码登录'
            );
            return false;
        } catch (RuntimeException $e) {
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_TOKEN_PARSE_FAILED'],
                'Token parses failed.'
            );
            return false;
        }


        // 校验签名
        if (!$token->verify($this->signer, $this->signature)) {
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_TOKEN_SIGNATURE'],
                'Error signature.'
            );
        }

        // token 是否过期
        if ($token->isExpired()) {
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_TOKEN_EXPIRED'],
                '自动登录已过期，请重新输入密码登录'
            );
        }
        // 校验基本信息
        $validator = new ValidationData();
        $validator->setIssuer($this->issuer);
        $validator->setAudience($this->audience);
        $validator->setId($this->jwtId);
        $this->payloadData = $token->getClaim('payload');
        return $token->validate($validator);
    }

    /**
     * 校验请求接口的token
     *
     * @param string $controllerId 接口所属控制器ID
     * @param string $actionId 接口对应的动作ID
     *
     * @return null
     */
    public function checkToken($controllerId, $actionId)
    {
        if ($this->__needValidate($controllerId, $actionId)) {
            // 兼容通过get 方式传递token
            $token = Yii::$app->request->getTokenFromHeader($this->jwtHeaderKey)
                ? : (Yii::$app->request->get()['token'] ?? '');
            if (!$this->__isValidToken($token)) {
                Yii::$app->response->error(
                    Yii::$app->response->resCode['ERROR_TOKEN_VERIFY_FAILED'],
                    'Token was invalid.'
                );
            }
        }
        return;
    }

    /**
     * 满足系统一定条件下需要验证请求接口的token
     *
     * @return null
     */
    public function checkTokenInSomeCondition()
    {
        $token = Yii::$app->request->get()['token']; // 通过get获取的token
        if (!$this->__isValidToken($token)) {
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_TOKEN_VERIFY_FAILED'],
                'Token was invalid.'
            );
        }
    }

    /**
     * 接口是否需要验证
     *
     * @param $controllerId string 接口所属控制器ID
     * @param $actionId string 接口对应的动作ID
     *
     * @return boolean(true or false)
     */
    private function __needValidate($controllerId, $actionId)
    {
        $controller = $controllerId;
        $action = $controllerId . '/' . $actionId;
        if (in_array($controller, $this->noTokenControllers) ||
            in_array($action, $this->noTokenActions)) {
            return false;
        }
        return true;
    }
}
