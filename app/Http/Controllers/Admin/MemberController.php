<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\CreateRequest;
use App\Services\UserService;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Helpers\CustomAlert;

class MemberController extends Controller
{
    protected $userService;
    protected $customAlert;

    public function __construct(
        UserService $userService,
        CustomAlert $customAlert)
    {
        $this->middleware('auth');
        $this->middleware('check-device');
        $this->userService = $userService;
        $this->customAlert = $customAlert;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::anotherUser()->get();

        return view('users.member_manager', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create_member');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $user = $this->userService->findByEmailWithTrashed($request->email);

        if (isset($user) && !$user->trashed()) {
            return redirect()->back()->with($this->customAlert::alert('error', trans('messages.same_email_when_create_member')))->withInput();
        }

        DB::beginTransaction();
        try {
            if ($user) {
                $this->userService->destroy($user->id);
            }
            $this->userService->create($request);
            DB::commit();

            return redirect()->route('members.index')->with($this->customAlert::alert('success', trans('messages.create_member_successfully')));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return redirect()->back()->with($this->customAlert::alert('error', trans('messages.create_member_failed')))->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = [
            'role' => $request->get('role')
        ];
        $user->update($data);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();

        return redirect()->back()->with($this->customAlert::alert('success', trans('messages.delete_member_successfully')));
    }

    public function search(Request $request)
    {
        $key = $request->key;
        $appId = $request->app_id;

        $users = User::anotherUser()
            ->where(function ($query) use ($key) {
                $query->where('email', 'like', "%$key%")
                    ->orWhere('name', 'like', "%$key%");
            })
            ->whereDoesntHave('applications', function ($query) use ($appId) {
                $query->where('applications.id', $appId);
            })
            ->get();

        return $users;
    }
}
