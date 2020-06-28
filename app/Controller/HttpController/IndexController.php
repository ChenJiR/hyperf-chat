<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller\HttpController;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Class IndexController
 * @package App\Controller
 * @Controller()
 */
class IndexController extends HttpAbstractController
{
    /**
     * @RequestMapping(path="/",method="get,post")
     */
    public function index()
    {
        return $this->success([
            'method' => $this->request->getMethod(),
            'result' => 'asd'
        ], 's');
    }

    /**
     * @RequestMapping(path="/test",method="get,post")
     */
    public function test()
    {
        throw new BusinessException(StatusCode::SERVER_ERROR);
    }
}
