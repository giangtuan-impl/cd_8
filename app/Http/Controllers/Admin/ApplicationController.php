<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\CreateRequest;
use App\Http\Requests\Application\UpdateRequest;
use App\Http\Requests\ApplicationFormRequest;
use App\Models\Application;
use App\Models\BuildNumber;
use App\Models\InvitedUser;
use App\Services\ApplicationService;
use App\Services\BuildNumberService;
use App\Services\FileService;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Exception;
use Jenssegers\Agent\Agent;
use App\Helpers\CustomAlert;

class ApplicationController extends Controller
{
    protected $applicationService;
    protected $fileService;
    protected $buildNumberService;
    protected $customAlert;

    public function __construct(
        ApplicationService $applicationService, 
        FileService $fileService,
        BuildNumberService $buildNumberService,
        CustomAlert $customAlert)
    {
        $this->middleware('auth');
        $this->middleware('check-device', ['except' => 'show']);
        $this->applicationService = $applicationService;
        $this->fileService = $fileService;
        $this->buildNumberService = $buildNumberService;
        $this->customAlert = $customAlert;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app_page.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $this->applicationService->createApp($request->all());

        return redirect()->route('index')->with($this->customAlert::alert('success', trans('messages.create_app_successfully')));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $buildNumberVersion = null)
    {
        $application = Application::findOrFail($id);
        $this->authorize('application.view', $application);
        $agent = new Agent();

        if ($buildNumberVersion != null && $agent->isMobile())  // if has param build number version (only mobile)
        {
            $buildNumber = $this->buildNumberService->findOrFail($buildNumberVersion);
            
            if ($buildNumber->env == BuildNumber::ENV_ANDROID) {
                $androidBuild = $buildNumber;
                $this->applicationService->asyncUpdateAndroidInfo($androidBuild);
                $iosBuild = null;
            } else {
                $iosBuild = $buildNumber;
                $androidBuild = null;
            }
            
            $members = $application->members;

            // get all records except current record
            $previousAndroidBuilds = $application->buildNumbers()->filterAndroidBuildNumbers()
                                    ->where('id', '!=', $buildNumberVersion)
                                    ->get();

            $previousIosBuilds = $application->buildNumbers()->filterIOSBuildNumbers()
                                ->where('id', '!=', $buildNumberVersion)
                                ->get();
        }
        else
        {
            $androidBuild = $application->buildNumbers()->latestAndroidBuild();
            $this->applicationService->asyncUpdateAndroidInfo($androidBuild);
            $iosBuild = $application->buildNumbers()->latestIOSBuild();
            $members = $application->members;

            // get all records except current record
            $previousAndroidBuilds = $application->buildNumbers()->filterAndroidBuildNumbers()
                                    ->where('id', '!=', $androidBuild->id ?? null)
                                    ->get();

            $previousIosBuilds = $application->buildNumbers()->filterIOSBuildNumbers()
                                ->where('id', '!=', $iosBuild->id ?? null)
                                ->get();
        }

        return view('app_page.show', compact(
            'application', 
            'members', 
            'androidBuild', 
            'iosBuild', 
            'previousAndroidBuilds', 
            'previousIosBuilds'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $app = Application::findOrFail($id);
        $this->authorize('application.update', $app);

        return view('app_page.edit', compact('app'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $app = Application::findOrFail($id);
        $this->authorize('application.update', $app);

        $this->applicationService->updateApp($request->all(), $id);

        return redirect()->route('apps.show', $id)->with($this->customAlert::alert('success', trans('messages.edit_app_successfully')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $app = Application::findOrFail($id);
        $this->authorize('application.delete', $app);

        // delete old directory
        if (isset($app->android_name) && $this->applicationService->checkFolderExist($app->android_name)) {
            $this->fileService->deleteDirectory(env('JENKINS_BUILD') . $app->android_name);
        }
        if (isset($app->ios_name) && $this->applicationService->checkFolderExist($app->ios_name)) {
            $this->fileService->deleteDirectory(env('JENKINS_BUILD') . $app->ios_name);
        }

        $app->delete();

        return redirect()->route('index')->with($this->customAlert::alert('success', trans('messages.delete_app_successfully')));
    }

    public function invite(Request $request)
    {
        $selectedMemberId = $request->selectedMemberId;
        try {
            $apps = Application::find($request->appId);

            $invitedMember = InvitedUser::where('app_id', $apps->id)->whereIn('tester_id', $selectedMemberId)->pluck('tester_id')->toArray();
            $inviteMember = array_values(array_diff($selectedMemberId, $invitedMember));
            $apps->members()->attach($inviteMember, [
                'user_id' => auth()->user()->id,
            ]);
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return response()->json([
                'message' => 'fail',
            ], 500);
        }

        return response()->json([
            'message' => 'success',
            'inviteMember' => $inviteMember
        ], 200);
    }

    public function removeMember(Request $request, $app, $member)
    {
        try {
            $apps = Application::findOrFail($app);
            $removeMember = [$member];
            $apps->members()->detach($removeMember);
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with($this->customAlert::alert('error', trans('messages.remove_member_from_app_failed')));
        }

        return redirect()->back()->with($this->customAlert::alert('success', trans('messages.remove_member_from_app_successfully')));
    }
}
