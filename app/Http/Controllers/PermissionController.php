<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:xem danh sách permission', ['only' => ['index']]);
        $this->middleware('permission:tạo permission', ['only' => ['create', 'store']]);
        $this->middleware('permission:cập nhật permission', ['only' => ['update', 'edit']]);
        $this->middleware('permission:xem permission', ['only' => ['show']]);
        $this->middleware('permission:xóa permission', ['only' => ['destroy']]);        
    }


    public function index()
    {
        $permission_groups = PermissionGroup::get();
        $permissions = Permission::orderby('permission_group', 'asc')
            ->orderby('id', 'asc')
            ->get();

        return view(
            'role-permission.permission.index',
            [
                'permissions' => $permissions,
                'permission_groups' => $permission_groups
            ]
        );
    }

    public function create()
    {
        $permission_groups = PermissionGroup::get();
        return view('role-permission.permission.create', ['permission_groups' => $permission_groups]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name'
            ],
            'permission_group' => 'required'
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'permission_group' => $request->permission_group
        ]);

        return redirect()->route('permissions.edit', $permission->id)->with('msg_success', 'Tạo mới permission thành công');
    }

    public function edit(Permission $permission)
    {
        $permission_groups = PermissionGroup::get();
        return view(
            'role-permission.permission.edit',
            [
                'permission' => $permission,
                'permission_groups' => $permission_groups
            ]
        );
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name,' . $permission->id
            ],
            'permission_group' => 'required'
        ]);

        $permission->name = $request->name;
        $permission->permission_group = $request->permission_group;
        $permission->save();       

        return redirect()->route('permissions.edit', $permission->id)->with('msg_success', 'Cập nhật permission thành công');
    }

    public function destroy($permissionId)
    {
        $permission = Permission::find($permissionId);
        $permission->delete();
        return redirect()->route('permissions.index')->with('msg_success', 'Xóa Permission thành công');
    }
}
