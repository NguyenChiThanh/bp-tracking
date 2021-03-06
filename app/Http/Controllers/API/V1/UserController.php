<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Users\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('isMod')) {
            return $this->unauthorizedResponse();
        }

        $users = [];

        if (auth()->user()->isMod()) {
            $partnerRole = 'Partner User';
            $users = User::whereHas('roles', function($q) use ($partnerRole) {
                $q->where('name', $partnerRole);
            })->latest()->with(['brands', 'company', 'roles'])->latest()->paginate(10);
        }

        if (auth()->user()->isAdmin()) {
            $users = User::latest()->with(['brands', 'company', 'roles'])->latest()->paginate(10);
        }

        return $this->sendResponse($users, 'Users list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Users\UserRequest  $request
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'cellphone' => $request['cellphone'],
            'status' => $request['status'],
            'password' => Hash::make($request['password']),
            'type' => $request['type'],
        ]);

        if ($request->get('company')) {
            $user['company_id'] = $request['company']['id'];
        }
        $user->save();

        $brandIds = [];
        foreach($request['brands'] as $brand) {
            array_push($brandIds, $brand['id']);
        }
        $user->brands()->attach($brandIds);

        $roles = Role::whereIn('id', array_column($request->roles, 'id'))->get(['id']);

        $user->roles()->attach($roles);

        return $this->sendResponse($user, 'User Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param  \App\Http\Requests\Users\UserRequest  $request
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $user->update($request->all());
        if ($request->get('company')) {
            $user['company_id'] = $request['company']['id'];
        }
        $user->save();

        $brandIds = [];
        foreach($request['brands'] as $brand) {
            array_push($brandIds, $brand['id']);
        }
        $user->brands()->sync($brandIds);

        $roles = Role::whereIn('id', array_column($request->roles, 'id'))->get(['id']);
        $user->roles()->sync($roles);

        $user->update($request->all());

        return $this->sendResponse($user, 'User Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $this->authorize('isMod');

        $user = User::findOrFail($id);
        // delete the user

        $user->delete();

        return $this->sendResponse([$user], 'User has been Deleted');
    }
}
