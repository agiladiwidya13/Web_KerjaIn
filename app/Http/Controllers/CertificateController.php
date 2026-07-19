<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Tampilkan halaman sertifikat (publik)
     * GET /sertifikat/{id}
     */
    public function show($id)
    {
        $certificate = Certificate::with(['enrollment.pelajar.user', 'enrollment.program.mitra.user', 'enrollment.submissions'])
            ->where('id', $id)
            ->firstOrFail();

        return view('pages.pelajar.certificate', compact('certificate'));
    }
}
