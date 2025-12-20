<?php

namespace App\Exceptions;

use Exception;

class AccountNotActiveException extends Exception
{
    protected $message = 'لا يمكن إجراء عمليات على هذا الحساب لأنه غير نشط (مجمد أو مغلق).';
    protected $code = 403;
}
