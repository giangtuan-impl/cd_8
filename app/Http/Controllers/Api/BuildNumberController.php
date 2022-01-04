<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BuildNumber\ApiCreateRequest;
use App\Models\BuildNumber;
use App\Services\ApplicationService;
use App\Services\BuildNumberService;
use App\Traits\ApiResponserTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class BuildNumberController extends Controller
{
    use ApiResponserTrait;

    protected $buildNumberService;
    protected $appService;

    public function __construct(
        BuildNumberService $buildNumberService,
        ApplicationService $appService
    ) {
        $this->buildNumberService = $buildNumberService;
        $this->appService = $appService;
    }

    public function store(ApiCreateRequest $request)
    {
        $app = $this->appService->findByName($request->appName);
        if (!$app) {
            return $this->errorResponse(trans('messages.not_found', ['attribute' => trans('messages.application')]), Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'app_id' => $app->id,
            'app_name' => $app->app_name,
            'build_name' => $request->file('file')->getClientOriginalExtension() == BuildNumber::ENV_IOS_EXTENSION ? $app->ios_name : $app->android_name,
            'build_number' => $request->buildNumber,
            'env' => $request->file('file')->getClientOriginalExtension() == BuildNumber::ENV_IOS_EXTENSION ? 0 : 1,
            'file' => $request->file('file')
        ];
        try {
            // create directory for files
            if (isset($data['build_name'])) {
                $this->buildNumberService->storeUploadFile($data);

                $build = $this->buildNumberService->create($data);

                return $this->successResponse($build, trans('messages.success'), Response::HTTP_OK);
            } else {
                return response()->json([trans('messages.fail') => trans('messages.directory_does_not_exist')]);
            }
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return $this->errorResponse(trans('messages.fail'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
