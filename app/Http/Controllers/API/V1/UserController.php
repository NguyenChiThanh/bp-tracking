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

        $users = User::latest()->paginate(10);

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
            'company' => $request['company']['id'],
//            'brands' => $request['brands'],
            'status' => $request['status'],
            'password' => Hash::make($request['password']),
            'type' => $request['type'],
        ]);

        $brandIds = [];
        foreach($request['brands'] as $brand) {
            array_push($brandIds, $brand['id']);
        }
        $user->brands()->attach($brandIds);

        $partnerUSerRole = Role::where('name', 'Partner User')->first();
        $user->roles()->attach([$partnerUSerRole->id]);

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

        $brandIds = [];
        foreach($request['brands'] as $brand) {
            array_push($brandIds, $brand['id']);
        }
        $user->brands()->sync($brandIds);

//        $partnerUSerRole = Role::where('name', 'Partner User')->first();
//        $user->roles()->sync([$partnerUSerRole->id]);

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
