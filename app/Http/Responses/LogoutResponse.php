<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        return redirect('/')
            ->with('logout_message', 'Вы успешно вышли из системы. До скорой встречи!');
    }
}
