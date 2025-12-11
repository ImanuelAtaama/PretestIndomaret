<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FtpUploadController extends Controller
{
    private function connect()
    {
        $host = env('FTP_HOST');
        $user = env('FTP_USERNAME');
        $pass = env('FTP_PASSWORD');
        $port = env('FTP_PORT');

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

    public function index()
    {
        $conn = $this->connect();

        $rawList = ftp_rawlist($conn, ".");
        ftp_close($conn);

        $files = [];

        foreach ($rawList as $line) {
            // Contoh line: "-rw-r--r-- 1 user group 2048 Jan 3 12:30 filename.txt"
            $parts = preg_split("/\s+/", $line, 9);

            if (count($parts) < 9) {
                continue; // skip jika format tidak cocok
            }

            $isDir = $parts[0][0] === 'd';  // Kalau diawali 'd' = directory

            $files[] = [
                'name' => $parts[8],
                'size' => $isDir ? '-' : $parts[4], // size hanya untuk file
                'date' => $parts[5] . ' ' . $parts[6] . ' ' . $parts[7],
                'is_dir' => $isDir
            ];
        }

        return view('admin.ftp', compact('files'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,pdf',
        ]);

        $file = $request->file('file');
        $conn = $this->connect();

        $originalName = $file->getClientOriginalName();
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // Ambil semua file yang ada di FTP
        $existing = ftp_nlist($conn, ".");

        // Jika folder kosong, ftp_nlist bisa mengembalikan false → handle agar tetap array
        if ($existing === false) {
            $existing = [];
        }

        // Mulai cek duplikasi
        $remote = $originalName;
        $counter = 1;

        while (in_array($remote, $existing)) {
            $remote = $filename . " (" . $counter . ")." . $extension;
            $counter++;
        }

        // Upload file dengan nama baru
        $local = $file->getRealPath();
        if (!ftp_put($conn, $remote, $local, FTP_BINARY)) {
            ftp_close($conn);
            return back()->with('error', 'Gagal mengunggah file.');
        }

        ftp_close($conn);

        return redirect()
            ->route('admin.ftp.index')
            ->with('success', "File berhasil diupload sebagai: $remote");
    }

    public function view($file)
    {
        $conn = $this->connect();

        // Ambil isi file sebagai string (stream)
        $temp = fopen('php://temp', 'r+');

        if (!ftp_fget($conn, $temp, $file, FTP_ASCII)) {
            ftp_close($conn);
            return back()->with('error', 'Gagal membaca isi file.');
        }

        rewind($temp);
        $content = stream_get_contents($temp);

        ftp_close($conn);

        // Deteksi jenis file
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        return view('admin.ftp_view', [
            'filename' => $file,
            'content'  => $content,
            'ext'      => $ext,
        ]);
    }


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
