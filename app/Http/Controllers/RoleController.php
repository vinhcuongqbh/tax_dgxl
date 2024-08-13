<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:xem danh sách role', ['only' => ['index']]);
        $this->middleware('permission:tạo role', ['only' => ['create', 'store']]);
        $this->middleware('permission:cập nhật role', ['only' => ['update', 'edit']]);
        $this->middleware('permission:xem role', ['only' => ['show']]);
        $this->middleware('permission:xóa role', ['only' => ['destroy']]);        
        $this->middleware('permission:cập nhật permission cho role', ['only' => ['addPermissionToRole','givePermissionToRole']]);
    }

    
    public function index()
    {
        $roles = Role::get();
        return view('role-permission.role.index', ['roles' => $roles]);
    }

    
    public function create()
    {
        return view('role-permission.role.create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name'
            ]
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        return redirect()->route('roles.edit', $role->id)->with('msg_success', 'Đã tạo Role mới thành công');
    }

    
    public function show(string $id)
    {
        //
    }

    
    public function edit(Role $role)
    {
        return view('role-permission.role.edit', [
            'role' => $role
        ]);
    }

    
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name,' . $role->id
            ]
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return redirect()->route('roles.edit', $role->id)->with('msg_success', 'Đã cập nhật role thành công');
    }

    
    public function destroy($roleId)
    {
        $role = Role::find($roleId);
        $role->delete();
        return redirect('roles')->with('status', 'Đã xóa role thành công');
    }


    public function addPermissionToRole($roleId)
    {
        $permissions = Permission::get();
        $permission_groups = PermissionGroup::get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $role->id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('role-permission.role.add-permissions', [
            'role' => $role,
            'permissions' => $permissions,
            'permission_groups' => $permission_groups,
            'rolePermissions' => $rolePermissions
        ]);
    }


    public function givePermissionToRole(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->permission);

        return redirect()->back()->with('msg_success', 'Cập nhật permission thành công');
    }
}
