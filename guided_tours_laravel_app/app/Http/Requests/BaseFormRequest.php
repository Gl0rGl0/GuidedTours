<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * By default allow all requests.
     * Concrete requests only need to define rules().
     */
    public function authorize(): bool
    {
        return true;
    }
}
