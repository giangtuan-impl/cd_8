<?php

namespace App\Http\Requests\Member;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
        $roles = User::ROLES;
        $languages = User::LANGUAGES;
        
        return [
            'email' => 'required|email|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',
            'name' => 'required',
            'role' => 'required|in:' . implode(",", $roles),
            'language' => 'required|in:' . implode(",", $languages),
        ];
    }
}
