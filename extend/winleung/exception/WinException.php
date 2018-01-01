<?php
//+----------------------------------------------------------------------
// | Author: winleung <448901948@qq.com>
// +----------------------------------------------------------------------

namespace winleung\exception;

class WinException extends \Exception
{
    protected $statusCode;
    protected $message;

    public function __construct($message = null, $statusCode)
    {
        parent::__construct();
        $this->statusCode = $statusCode;
        $this->message    = $message;

    }

    public function false()
    {
        return ['err_code' => $this->statusCode, 'err_msg' => $this->message];
    }
}
