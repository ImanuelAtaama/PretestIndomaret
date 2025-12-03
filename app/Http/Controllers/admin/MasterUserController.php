<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewUserMail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;


class MasterUserController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = $request->get('username', '');
        $users = User::where('username', 'like', '%' . $query . '%')
                    ->select('id_user', 'username')
                    ->limit(10)
                    ->get();
        return response()->json($users);
    }

    public function index(Request $request)
    {
        $users = User::query();
        $roles = Role::all();

        // Filter username (autocomplete memilih username exact)
        if ($request->username) {
            $users->where('username', $request->username);
        }

        // Filter tanggal tanpa jam
        if ($request->date) {
        $dates = explode(' to ', $request->date);
            // Jika pilih range tanggal
            if (count($dates) === 2) {
                $start = $dates[0];
                $end   = $dates[1];

                // HANYA bandingkan tanggalnya saja
                $users->whereDate('created_at', '>=', $start)
                        ->whereDate('created_at', '<=', $end);

            } else {
                // Single date
                $users->whereDate('created_at', $dates[0]);
            }
        }

        $users = $users->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();

        return view('admin.master_user', compact('users','roles'));
    }

    public function export(Request $request, $type)
    {
        $users = User::query();

        // Filter username
        if ($request->username) {
            $users->where('username', $request->username);
        }

        // Filter tanggal
        if ($request->date) {
            $dates = explode(' to ', $request->date);
            if (count($dates) === 2) {
                $users->whereDate('created_at', '>=', $dates[0])
                    ->whereDate('created_at', '<=', $dates[1]);
            } else {
                $users->whereDate('created_at', $dates[0]);
            }
        }

        $users = $users->get();

        // Nama file berdasarkan input user
        $filename = $request->filename . '.' . $type;


        /* =========================
        EXPORT CSV
        ============================*/
        if ($type === 'csv') {
            $delimiter = '|';
            // MODE OPEN — TAMPILKAN CSV TANPA DOWNLOAD
            if ($request->open) {
                $csvData = "ID{$delimiter}Username{$delimiter}Email{$delimiter}Role{$delimiter}Created At\n";
                foreach ($users as $u) {
                    $csvData .=
                        $u->id_user . $delimiter .
                        $u->username . $delimiter .
                        $u->email . $delimiter .
                        $u->role->role_name . $delimiter .
                        $u->created_at . "\n";
                }

                return response($csvData, 200)
                    ->header('Content-Type', 'text/plain; charset=utf-8')
                    ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
            }
            // MODE DOWNLOAD
            return response()->streamDownload(function() use ($users, $delimiter) {

                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Username', 'Email', 'Role', 'Created At'], $delimiter);

                foreach ($users as $u) {
                    fputcsv($file, [
                        $u->id_user,
                        $u->username,
                        $u->email,
                        $u->role->role_name,
                        $u->created_at
                    ], $delimiter);
                }

                fclose($file);

            }, $filename);

        }
        /* =========================
        EXPORT PDF
        ============================*/
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('exports.users_pdf', [
                'users' => $users,
                'date'  => $request->date
            ]);
            // MODE OPEN — TAMPILKAN TANPA DOWNLOAD
            if ($request->open) {
                return response($pdf->output(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
            }

            // MODE DOWNLOAD
            return $pdf->download($filename);
        }
        return abort(404);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file, 'r');

        // Ambil header
        $header = fgetcsv($handle, 0, '|');

        $expectedHeader = ['id_role', 'username', 'email', 'password'];

        // Jika kolom kurang → delimiter salah
        if (count($header) < count($expectedHeader)) {
            return back()->with('error', "Delimiter file salah. Gunakan tanda pemisah: |");
        }

        if ($header !== $expectedHeader) {
            return back()->with('error', 'Format header CSV tidak sesuai. Harus: id_role|username|email|password');
        }

        $countInsert = 0;

        while (($row = fgetcsv($handle, 0, '|')) !== false) {

            // Skip jika data kurang kolom
            if (count($row) < 4) continue;

            // Cek duplikasi username/email (opsional)
            $exists = User::where('username', $row[1])
                        ->orWhere('email', $row[2])
                        ->first();

            if ($exists) continue; // skip duplikat

            User::create([
                'id_role'  => $row[0],
                'username' => $row[1],
                'email'    => $row[2],
                'password' => bcrypt($row[3]),
            ]);

            $countInsert++;
        }

        fclose($handle);

        return back()->with('success', "$countInsert user berhasil ditambahkan.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'id_role' => 'required|exists:roles,id_role',
        ]);


        // Constraint 1: Hanya 1 admin (id_role=1)
        if ($request->id_role == 1 && User::where('id_role', 1)->exists()) {
            return back()->withErrors(['id_role' => 'Admin sudah ada. Hanya boleh 1 admin.'])->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => $request->id_role,
        ]);
        // Simpan password asli sebelum di-hash
        $plainPassword = $request->password;
        //kirim email
        Mail::to($user->email)->send(new NewUserMail($user, $plainPassword));

        return redirect()->route('admin.master_user.index')->with('success', 'User berhasil ditambahkan dan email dikirim');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:users,username,' . $id . ',id_user',
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
            'id_role' => 'required|exists:roles,id_role',
        ]);

        // Constraint 2: Jika diubah menjadi admin, cek apakah sudah ada admin lain
        if ($request->id_role == 1 && User::where('id_role', 1)->where('id_user', '!=', $id)->exists()) {
            return redirect()->back()->withErrors(['id_role' => 'Admin sudah ada. Hanya boleh 1 admin.'])->withInput();
        }

        $user->update($request->only(['username', 'email', 'id_role']));
        return redirect()->route('admin.master_user.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Constraint 1: Admin tidak bisa dihapus
        if ($user->id_role == 1) {
            return redirect()->route('admin.master_user.index')
                            ->with('error', 'Admin tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('admin.master_user.index')
                        ->with('success', 'User berhasil dihapus.');
    }
}
