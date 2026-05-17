<?php
namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use App\Models\Dokumen;
use App\Models\Sekolah;
use App\Models\Jurusan;
use App\Models\TahunAkademik;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PendaftaranController extends Controller
{
    public function create()
    {
        $sekolahs = Sekolah::with(['jurusans' => fn($q) => $q->where('is_active', 1)->orderBy('nama_jurusan')])
            ->where('is_active', 1)->orderBy('urutan')->get();

        return view('client.daftar', compact('sekolahs'));
    }

    public function getJurusan(Request $request)
    {
        $jurusans = Jurusan::where('sekolah_id', $request->sekolah_id)
            ->where('is_active', 1)->orderBy('nama_jurusan')->get(['id','nama_jurusan','kode_jurusan']);
        return response()->json($jurusans);
    }

    public function store(Request $request)
    {
        $sekolah      = Sekolah::find($request->sekolah_id);
        $needsJurusan = $sekolah && in_array(strtoupper((string) $sekolah->tingkatan), ['SMA', 'SMK']);

        $validated = $request->validate([
            'sekolah_id'        => ['required', 'exists:sekolahs,id'],
            'jurusan_id'        => array_filter([
                $needsJurusan ? 'required' : 'nullable',
                'exists:jurusans,id',
            ]),
            'nama_lengkap'      => 'required|string|max:100',
            'nisn'              => ['required', 'digits:10'],
            'jenis_kelamin'     => ['required', Rule::in(['laki_laki', 'perempuan'])],
            'tempat_lahir'      => 'required|string|max:60',
            'tanggal_lahir'     => 'required|date',
            'agama'             => ['required', Rule::in(['islam','protestan','katolik','hindu','budha','khonghucu'])],
            'alamat'            => 'required|string|max:1000',
            'phone'             => ['required', 'regex:/^[0-9]{9,15}$/'],
            'email'             => 'required|email|max:100',
            'asal_sekolah'      => 'required|string|max:100',
            'tahun_lulus'       => ['required', 'digits:4', 'integer', 'min:' . (date('Y') - 10), 'max:' . date('Y')],
            'nomor_ijazah'      => 'required|string|max:100',
            'jalur_pendaftaran' => ['required', Rule::in(['reguler','prestasi','afirmasi','pindahan'])],
            'ket_jalur'         => [
                in_array($request->jalur_pendaftaran, ['prestasi','afirmasi','pindahan']) ? 'required' : 'nullable',
                'nullable', 'string', 'max:1000'
            ],
            'file_lampiran'     => [
                in_array($request->jalur_pendaftaran, ['prestasi','afirmasi']) ? 'required' : 'nullable',
                'nullable', 'file', 'max:5120',
            ],
            'wali'                  => 'required|array|min:1',
            'wali.*.nama_wali'      => 'required|string|max:100',
            'wali.*.jenis_wali'     => ['required', Rule::in(['ayah','ibu','wali'])],
            'wali.*.pekerjaan'      => 'required|string|max:60',
            'wali.*.notelp_wali'    => ['nullable', 'regex:/^[0-9]{9,15}$/'],
            'wali.*.email_wali'     => 'nullable|email|max:100',
            // FIX: Hapus validasi 'mimes:' karena butuh ext fileinfo yang tidak aktif di hosting
            // Cukup validasi 'file' dan 'max' saja
            'pas_foto' => 'required|file|max:5120',
            'kk'       => 'required|file|max:5120',
            'akta'     => 'required|file|max:5120',
            'ijazah'   => 'nullable|file|max:5120',
            'skhun'    => 'nullable|file|max:5120',
            'stl'      => 'nullable|file|max:5120',
        ], [
            'wali.required'            => 'Data orang tua/wali wajib diisi minimal 1.',
            'nisn.digits'              => 'NISN harus 10 digit angka.',
            'phone.regex'              => 'No. HP hanya boleh angka (9-15 digit).',
            'wali.*.notelp_wali.regex' => 'No. HP wali hanya boleh angka (9-15 digit).',
            'pas_foto.required'        => 'Pas foto wajib diupload.',
            'kk.required'             => 'Kartu Keluarga wajib diupload.',
            'akta.required'           => 'Akta Kelahiran wajib diupload.',
            'tahun_lulus.min'         => 'Tahun lulus tidak valid.',
            'tahun_lulus.max'         => 'Tahun lulus tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            $tahunAkademik = TahunAkademik::where('is_active', 1)->first()
                ?? TahunAkademik::latest()->first();

            $kodeRegis = $this->generateKode();
            $isAdmin   = $request->has('_admin_submit');

            $pendaftaran = Pendaftaran::create([
                'kode_regis'            => $kodeRegis,
                'tahun_akademik_id'     => $tahunAkademik?->id,
                'sekolah_id'            => $validated['sekolah_id'],
                'jurusan_id'            => $validated['jurusan_id'] ?? null,
                'jalur_pendaftaran'     => $validated['jalur_pendaftaran'],
                'ket_jalur_pendaftaran' => $validated['ket_jalur'] ?? null,
                'status'                => 'diproses',
                'tanggal_submit'        => now(),
                'dibuat_oleh'           => $isAdmin ? 'admin' : 'publik',
            ]);

            Siswa::create([
                'pendaftaran_id' => $pendaftaran->id,
                'nisn'           => $validated['nisn'],
                'nama_siswa'     => $validated['nama_lengkap'],
                'jk'             => $validated['jenis_kelamin'],
                'phone'          => $validated['phone'],
                'email'          => $validated['email'],
                'agama'          => $validated['agama'],
                'tempat_lahir'   => $validated['tempat_lahir'],
                'tanggal_lahir'  => $validated['tanggal_lahir'],
                'alamat'         => $validated['alamat'],
                'asal_sekolah'   => $validated['asal_sekolah'],
                'tahun_lulus'    => $validated['tahun_lulus'],
                'nomor_ijazah'   => $validated['nomor_ijazah'],
            ]);

            foreach ($validated['wali'] as $waliData) {
                WaliSiswa::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'nama_wali'      => $waliData['nama_wali'],
                    'hubungan'       => 'orang_tua',
                    'jenis_wali'     => $waliData['jenis_wali'],
                    'pekerjaan'      => $waliData['pekerjaan'],
                    'notelp_wali'    => $waliData['notelp_wali'] ?? null,
                    'email'          => $waliData['email_wali'] ?? null,
                ]);
            }

            // FIX UTAMA: Pakai storeFileManually() tanpa finfo/MIME detection
            $dokumenFields = ['pas_foto', 'kk', 'akta', 'ijazah', 'skhun', 'stl'];
            foreach ($dokumenFields as $field) {
                if ($request->hasFile($field) && $request->file($field)->isValid()) {
                    $path = $this->storeFileManually($request->file($field), 'ppdb/' . $kodeRegis);
                    if ($path) {
                        Dokumen::create([
                            'pendaftaran_id' => $pendaftaran->id,
                            'jenis_dokumen'  => $field,
                            'file_path'      => $path,
                            'original_name'  => $request->file($field)->getClientOriginalName(),
                        ]);
                    }
                }
            }

            if ($request->hasFile('file_lampiran') && $request->file('file_lampiran')->isValid()) {
                $path = $this->storeFileManually($request->file('file_lampiran'), 'ppdb/' . $kodeRegis);
                if ($path) {
                    Dokumen::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'jenis_dokumen'  => 'lampiran_jalur',
                        'file_path'      => $path,
                        'original_name'  => $request->file('file_lampiran')->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();

            try {
                app(MailService::class)->sendPendaftaranBerhasil(
                    $pendaftaran->load(['siswa','sekolah','jurusan'])
                );
            } catch (\Exception $e) {
                Log::error('Email pendaftaran gagal: ' . $e->getMessage());
            }

            if ($isAdmin) {
                return redirect()->route('admin.pendaftaran.show', $pendaftaran->id)
                    ->with('success', "Pendaftaran {$kodeRegis} berhasil dibuat!");
            }

            return redirect()->route('pendaftaran.finish', ['kode' => $kodeRegis])
                ->with('success', 'Pendaftaran berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pendaftaran store error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return back()->withInput()->withErrors([
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * FIX: Store file tanpa MIME detection (bypass finfo extension)
     * Pakai moveTo langsung ke storage path
     */
    private function storeFileManually(\Illuminate\Http\UploadedFile $file, string $directory): ?string
    {
        try {
            $ext      = $file->getClientOriginalExtension() ?: 'bin';
            $filename = uniqid('doc_', true) . '.' . strtolower($ext);
            $fullDir  = storage_path('app/public/' . $directory);

            if (!is_dir($fullDir)) {
                mkdir($fullDir, 0755, true);
            }

            $file->move($fullDir, $filename);

            return $directory . '/' . $filename;
        } catch (\Exception $e) {
            Log::error('storeFileManually error: ' . $e->getMessage());
            return null;
        }
    }

    public function finish(Request $request)
    {
        $kode        = $request->query('kode');
        $pendaftaran = Pendaftaran::with(['siswa','sekolah','jurusan'])->where('kode_regis', $kode)->first();
        if (!$pendaftaran) return redirect()->route('home');
        return view('client.pendaftaran-selesai', compact('pendaftaran'));
    }

    public function adminCreate()
    {
        $sekolahs = Sekolah::with(['jurusans' => fn($q) => $q->where('is_active', 1)->orderBy('nama_jurusan')])
            ->where('is_active', 1)->orderBy('urutan')->get();
        return view('admin.pendaftaran.create', compact('sekolahs'));
    }

    public function adminStore(Request $request)
    {
        $request->merge(['_admin_submit' => 1]);
        return $this->store($request);
    }

    private function generateKode(): string
    {
        $year = date('y');
        do {
            $random = strtoupper(substr(md5(uniqid('', true)), 0, 8));
            $kode   = "PPDB{$year}-{$random}";
        } while (Pendaftaran::where('kode_regis', $kode)->exists());
        return $kode;
    }
}
