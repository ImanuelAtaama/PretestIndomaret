<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FtpUploadController extends Controller
{
    public function index()
    {
        // Ambil list file dari FTP storage
        $files = Storage::disk('ftp')->files();

        return view('admin.ftp', compact('files'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,pdf', // sesuaikan validasi
        ],[
            'file.required' => 'File harus diunggah.',
            'file.file' => 'Yang diunggah harus berupa file.',
            'file.mimes' => 'File harus berformat CSV atau TXT atau PDF.',
        ]);

        $file = $request->file('file');

        // Upload ke FTP dengan nama aslinya
        Storage::disk('ftp')->putFileAs('', $file, $file->getClientOriginalName());

        return redirect()->route('admin.ftp.index')->with('success', 'File berhasil diupload');
    }

    public function delete($file)
    {
        Storage::disk('ftp')->delete($file);

        return redirect()->route('admin.ftp.index')->with('success', 'File berhasil dihapus');
    }
}
