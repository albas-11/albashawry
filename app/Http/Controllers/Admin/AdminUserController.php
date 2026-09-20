<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AdminUserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:Admin|User');
    // }

    public function __construct()
    {
        $this->middleware(['role:Admin|User', 'permission:manage_users']);
    }


    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions'])
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Admin', 'User']);
            });


        if ($request->search) {
            $query->where(
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
            );
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::whereIn('name', ['admin', 'user'])->get();
        $permissions = Permission::all();
        $groupedPermissions = $this->groupPermissions($permissions);

        return view('admin.users.index', compact('users', 'roles', 'permissions', 'groupedPermissions'));
    }

    public function clients(Request $request)
    {
        $search = $request->input('search');

        $clients = User::role('Client')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('num', 'like', "%{$search}%");
                });
            })
            ->paginate(20)
            ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }


    public function create()
    {
        $roles = Role::whereIn('name', ['admin', 'user'])->get();
        $permissions = Permission::all();

        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'role_id' => 'required|exists:roles,id',
            'num' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role_id' => $validated['role_id'],
            'num' => $validated['num'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $roleName = strtolower($role->name);
        $user->syncRoles([$roleName]);

        // Assigner les permissions
        if ($roleName === 'admin') {
            // Admin = tous les droits
            $allPermissions = Permission::all();
            $user->syncPermissions($allPermissions);
        } elseif ($roleName === 'user') {
            // User = seulement les permissions choisies
            $permissionIds = $validated['permissions'] ?? [];
            if (!empty($permissionIds)) {
                $permissions = Permission::whereIn('id', $permissionIds)->get();
                $user->syncPermissions($permissions);
            }
        } else {
            $user->syncPermissions([]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.User successfully created'));
    }


    public function edit(User $user)
    {
        $roles = Role::whereIn('name', ['Admin', 'User'])->get();
        $permissions = Permission::all();
        $userPermissions = $user->permissions()->pluck('name')->toArray();
        $permissionGroups = $this->groupPermissions($permissions);

        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'userPermissions', 'permissionGroups'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|confirmed|min:6',
            'role_id' => 'required|exists:roles,id',
            'num' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            'role_id' => $validated['role_id'],
            'num' => $validated['num'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $roleName = strtolower($role->name);
        $user->syncRoles([$roleName]);

        if ($roleName === 'admin') {
            $allPermissionIds = Permission::pluck('id')->toArray();
            $user->syncPermissions($allPermissionIds);
        } elseif ($roleName === 'user') {
            $permissionIds = $validated['permissions'] ?? [];
            $user->syncPermissions($permissionIds);
        } else {
            $user->syncPermissions([]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.User successfully updated'));
    }


    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }


    public function destroy(User $user)
    {
        if ($user->orders()->count() > 0) {
            return back()->with('error', __('admin.It is impossible to delete a user who has commands.'));
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', __('admin.User successfully deleted'));
    }

    private function groupPermissions($permissions)
    {
        $groups = [
            'products' => ['title' => __('admin.Products Management'), 'permissions' => []],
            'categories' => ['title' => __('admin.Categories Management'), 'permissions' => []],
            'tryons' => ['title' => __('admin.Try-Ons Management'), 'permissions' => []],
            'orders' => ['title' => __('admin.Orders Management'), 'permissions' => []],
            'clients' => ['title' => __('admin.Clients Management'), 'permissions' => []],
            'users' => ['title' => __('admin.Users Management'), 'permissions' => []],
            'settings' => ['title' => __('admin.Others'), 'permissions' => []],
        ];


        foreach ($permissions as $permission) {
            $name = $permission->name;
            if (str_contains($name, 'product')) $groups['products']['permissions'][] = $permission;
            elseif (str_contains($name, 'categor')) $groups['categories']['permissions'][] = $permission;
            elseif (str_contains($name, 'tryon') || str_contains($name, 'virtual')) $groups['tryons']['permissions'][] = $permission;
            elseif (str_contains($name, 'order')) $groups['orders']['permissions'][] = $permission;
            elseif (str_contains($name, 'client')) $groups['clients']['permissions'][] = $permission;
            elseif (str_contains($name, 'user') && !str_contains($name, 'customer')) $groups['users']['permissions'][] = $permission;
            else $groups['settings']['permissions'][] = $permission;
        }

        return $groups;
    }
}
