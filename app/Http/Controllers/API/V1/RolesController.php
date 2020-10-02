<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RolesController extends BaseController
{
    protected $role;

    public function __construct(Role $role)
    {
        $this->middleware('auth:api');

        $this->role = $role;
    }

    public function index(Request $request)
    {
        if (!Gate::allows('isMod')) {
            return $this->unauthorizedResponse();
        }

        $userType = $request->get('user_type');
        if ($userType) {
            if ($userType == User::PARTNER_USER) {
                $roles = $this->role->where('name', 'Partner User')->get();
            } else {
                $roles = $this->role->where('name', '!=', 'Partner User')->get();
            }
            return $this->sendResponse($roles, "Role list");
        }

        $roles = [];
        return $this->sendResponse($roles, "Role list");
    }

}
