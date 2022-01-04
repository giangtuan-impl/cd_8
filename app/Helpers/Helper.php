<?php

namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


function upload(Request $request, $fieldName, $folder) {
    $file = $request->file($fieldName)->getClientOriginalName();
    $filename = pathinfo($file, PATHINFO_FILENAME);
    $extension = $request->file($fieldName)->getClientOriginalExtension();
    $fileNameToStore = Str::random(12).'_'.time().'.'.$extension;
    $request->file($fieldName)->move($folder, $fileNameToStore);

    return $fileNameToStore;
}
