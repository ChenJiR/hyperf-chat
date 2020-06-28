<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller\HttpController;

use App\Constants\StatusCode;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

abstract class HttpAbstractController
{
    /**
     * @Inject
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * success
     * 成功返回请求结果
     * @param array|string|int|bool $data
     * @param string|null $msg
     * @return PsrResponseInterface
     */
    protected function success($data = [], string $msg = null)
    {
        $msg = $msg ?? StatusCode::getMessage(StatusCode::SUCCESS);;
        $data = [
            'qid' => $this->request->getHeaderLine('qid'),
            'code' => StatusCode::SUCCESS,
            'msg' => $msg,
            'data' => $data
        ];
        return $this->response->json($data);
    }

    /**
     * error
     * 业务相关错误结果返回
     * @param int $code
     * @param string|null $msg
     * @return PsrResponseInterface
     */
    protected function error(int $code, string $msg = null)
    {
        $msg = $msg ?? StatusCode::getMessage($code);;
        $data = [
            'qid' => $this->request->getHeaderLine('qid'),
            'code' => $code,
            'msg' => $msg,
        ];
        return $this->response->json($data);
    }

    /**
     * json
     * 直接返回数据
     * @param $data
     * @return PsrResponseInterface
     */
    protected function json(array $data)
    {
        return $this->response->json($data);
    }

    /**
     * xml
     * 返回xml数据
     * @param $data
     * @return PsrResponseInterface
     */
    protected function xml(array $data)
    {
        return $this->response->xml($data);
    }

    /**
     * redirect
     * 重定向
     * @param string $url
     * @param string $schema
     * @param int $status
     * @return PsrResponseInterface
     */
    protected function redirect(string $url, string $schema = 'http', int $status = 302)
    {
        return $this->response->redirect($url, $status, $schema);
    }

    /**
     * download
     * 下载文件
     * @param string $file
     * @param string $name
     * @return PsrResponseInterface
     */
    protected function download(string $file, string $name = '')
    {
        return $this->response->download($file, $name);
    }

    /**
     * cookie
     * 设置cookie
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @param bool $raw
     * @param null|string $sameSite
     */
    protected function cookie(string $name, string $value = '', $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true, bool $raw = false, ?string $sameSite = null)
    {
        // convert expiration time to a Unix timestamp
        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);
            if ($expire === false) {
                throw new \RuntimeException('The cookie expiration time is not valid.');
            }
        }

        $cookie = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
        $response = $this->response->withCookie($cookie);
        Context::set(PsrResponseInterface::class, $response);
        return;
    }
}
