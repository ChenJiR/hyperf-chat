<?php
declare(strict_types=1);

namespace App\Constants;


use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class StatusCode extends AbstractConstants
{
    /**
     * @Message('success');
     */
    const SUCCESS = 200;

    /**
     * @Message("Server Error！")
     */
    const SERVER_ERROR = 500;

    /**
     * @Message("Params is invalid.")
     */
    const PARAMS_INVALID = 1000;

}