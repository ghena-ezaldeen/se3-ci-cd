<?php

namespace App\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    protected $message = 'عذراً، الرصيد غير كافٍ لإتمام هذه العملية (بما في ذلك حد السحب على المكشوف).';
    protected $code = 422;
}
