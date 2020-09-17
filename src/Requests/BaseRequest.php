<?php

namespace HessamDev\Hessam\Requests;

use Illuminate\Foundation\Http\FormRequest;
use HessamDev\Hessam\Interfaces\BaseRequestInterface;

/**
 * Class BaseRequest
 * @package HessamDev\Hessam\Requests
 */
abstract class BaseRequest extends FormRequest implements BaseRequestInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::user()->canManageHessamPosts();
    }
}
