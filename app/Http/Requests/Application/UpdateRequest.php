<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $id = $request->route('app');
        $rules = [
            'app_name' => "required|unique:applications,app_name,$id,id,deleted_at,NULL",
            'ios_name' => "required_without_all:android_name|different:android_name",
            'android_name' => "required_without_all:ios_name|different:ios_name"
        ];

        if ($request->ios_name) {
            $rules['ios_name'] .= "|unique:applications,ios_name,$id,id,deleted_at,NULL";
        }
        if ($request->android_name) {
            $rules['android_name'] .= "|unique:applications,android_name,$id,id,deleted_at,NULL";

        }

        // return [
        //     'app_name' => "required|unique:applications,app_name,$id,id",
        //     'ios_name' => "required_without_all:android_name|unique:applications,ios_name,$id,id,deleted_at,NULL",
        //     'android_name' => "required_without_all:ios_name|unique:applications,android_name,$id,id,deleted_at,NULL"
        // ];

        return $rules;
    }
}
