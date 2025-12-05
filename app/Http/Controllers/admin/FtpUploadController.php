<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FtpUploadController extends Controller
{
    // ============================
    // CONNECT TO FTP (Plain, Port 21)
    // ============================
    private function connect()
    {
        $host = config('filesystems.disks.ftp_custom.host','172.20.22.30');
        $user = config('filesystems.disks.ftp_custom.username', 'ftpprepopigrdev');
        $pass = config('filesystems.disks.ftp_custom.password', 'xxFTPPREPOPIGRDEVxx');
        $port = config('filesystems.disks.ftp_custom.port', 21);

        $conn = ftp_connect($host, $port, 30); // 30 detik timeout
        if (!$conn) {
            throw new \Exception("Tidak dapat terhubung ke server FTP: $host");
        }

        ftp_pasv($conn, true); // Passive mode

        if (!ftp_login($conn, $user, $pass)) {
            ftp_close($conn);
            throw new \Exception("Login FTP gagal. Periksa username & password.");
        }

        return $conn;
    }

    // ============================
    // LIST FILES
    // ============================
    public function index()
    {
        $conn = $this->connect();
        $files = ftp_nlist($conn, '.');
        ftp_close($conn);

        return view('admin.ftp', compact('files'));
    }

    // ============================
    // UPLOAD FILE
    // ============================
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,pdf',
        ]);

        $file = $request->file('file');
        $conn = $this->connect();

        $remote = $file->getClientOriginalName();
        $local = $file->getRealPath();

        if (!ftp_put($conn, $remote, $local, FTP_BINARY)) {
            ftp_close($conn);
            return back()->with('error', 'Gagal mengunggah file.');
        }

        ftp_close($conn);

        return redirect()->route('admin.ftp.index')->with('success', 'File berhasil diupload.');
    }

    // ============================
    // DELETE FILE
    // ============================
    public function delete($file)
    {
        $conn = $this->connect();

        if (!ftp_delete($conn, $file)) {
            ftp_close($conn);
            return back()->with('error', 'Gagal menghapus file.');
        }

        ftp_close($conn);

        return redirect()->route('admin.ftp.index')->with('success', 'File berhasil dihapus.');
    }
}
