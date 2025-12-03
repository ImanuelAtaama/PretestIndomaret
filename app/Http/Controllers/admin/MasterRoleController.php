<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;

class MasterRoleController extends Controller
{
    public function index()
    {
        $roles = Role::paginate(5);
        return view('admin.master_role', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|unique:roles,role_name',
            'Deskripsi' => 'required',
        ]);

        Role::create([
            'role_name' => $request->role_name,
            'Deskripsi' => $request->Deskripsi,
        ]);

        return redirect()->route('admin.master_role.index')->with('success', 'Role berhasil ditambahkan');
    }


    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Batasan: Admin (id_role=1) hanya bisa update Deskripsi, bukan nama
        if ($id == 1) {
            $request->validate(['Deskripsi' => 'required']);
            $role->update(['Deskripsi' => $request->Deskripsi]);
        } else {
            $request->validate([
                'role_name' => 'required|unique:roles,role_name,' . $id . ',id_role',
                'Deskripsi' => 'required',
            ]);
            $role->update($request->only(['role_name', 'Deskripsi']));

        }

        return redirect()->route('admin.master_role.index')->with('success', 'Role berhasil diupdate');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Batasan: Role yang digunakan oleh user tidak bisa dihapus
        if (User::where('id_role', $id)->exists()) {
            return redirect()->route('admin.master_role.index')->with('error', 'Role ini sedang digunakan dan tidak bisa dihapus');
        }

        // Batasan: Admin tidak bisa dihapus
        if ($id == 1) {
            return redirect()->route('admin.master_role.index')->with('error', 'Role admin tidak bisa dihapus');
        }

        $role->delete();
        return redirect()->route('admin.master_role.index')->with('success', 'Role berhasil dihapus');
    }
}
