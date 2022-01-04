<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $validators = $validator->errors()->toArray();
        $errors = [];
        foreach ($validators as $key => $value) {
            $errors[$key] = $value[0];
        }
        $message = [
            "message" => trans('messages.response.validate_errors'),
            "errors" => $errors,
        ];
        throw new HttpResponseException(response()->json($message, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
