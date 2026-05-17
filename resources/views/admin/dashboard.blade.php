@extends('admin.layouts.app')
@section('title','Dashboard')
@section('breadcrumb','Dashboard')

@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-gauge text-blue-600"></i> Dashboard</h1>
  <div class="flex items-center gap-3">
    <span class="text-xs text-gray-400">{{ now()->format('l, d F Y') }}</span>
    <a href="{{ route('admin.pendaftaran.export.pdf') }}" target="_blank" class="btn btn-sm btn-danger" title="Export Laporan PDF">
      <i class="fa fa-file-pdf"></i> Export PDF
    </a>
    <a href="{{ route('admin.pendaftaran.export.excel') }}" class="btn btn-sm btn-success" title="Export Excel/CSV">
      <i class="fa fa-file-excel"></i> Export Excel
    </a>
  </div>
</div>

<!-- STATS -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
  @php $cards=[
    ['Total Daftar',$stats['total_daftar'],'fa-users','#1e40af','#eff6ff'],
    ['Diproses',$stats['diproses'],'fa-hourglass-half','#d97706','#fef3c7'],
    ['Diterima',$stats['diterima'],'fa-check-circle','#059669','#d1fae5'],
    ['Tunggu Bayar',$stats['menunggu_bayar'],'fa-credit-card','#7c3aed','#ede9fe'],
    ['Lunas',$stats['lunas'],'fa-trophy','#0891b2','#cffafe'],
    ['Verif Bayar',$pendingVerif,'fa-bell','#dc2626','#fee2e2'],
  ]; @endphp
  @foreach($cards as $c)
  <div class="stat-card">
    <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:{{ $c[4] }};"><i class="fa {{ $c[2] }} text-sm" style="color:{{ $c[3] }};"></i></div>
    <div class="text-2xl font-extrabold text-gray-900">{{ number_format($c[1]) }}</div>
    <div class="text-xs text-gray-500 mt-0.5">{{ $c[0] }}</div>
  </div>
  @endforeach
</div>

<!-- REVENUE + CHART -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">
  <div class="stat-card p-5">
    <div class="text-xs font-bold text-gray-500 uppercase mb-4">Revenue Pendaftaran</div>
    <div class="mb-4"><div class="text-xs text-gray-400">Total Keseluruhan</div><div class="text-2xl font-extrabold text-green-600">Rp {{ number_format($revenueTotal,0,',','.') }}</div></div>
    <div><div class="text-xs text-gray-400">Bulan Ini</div><div class="text-xl font-bold text-blue-600">Rp {{ number_format($revenueMonth,0,',','.') }}</div></div>
    <div class="mt-4 pt-4 border-t border-gray-100">
      <div class="text-xs text-gray-400 mb-2">Top 3 Sekolah</div>
      @foreach($perSekolah->sortByDesc('total')->take(3) as $p)
      <div class="flex items-center justify-between text-xs mb-1.5">
        <span class="text-gray-600 truncate max-w-[130px]">{{ $p->sekolah?->nama_sekolah }}</span>
        <span class="font-bold text-gray-800">{{ $p->total }}</span>
      </div>
      @endforeach
    </div>
  </div>

  <div class="stat-card p-5 xl:col-span-2">
    <div class="text-xs font-bold text-gray-500 uppercase mb-4">Pendaftaran per Bulan ({{ date('Y') }})</div>
    <div style="position:relative;height:150px;"><canvas id="chartBulan"></canvas></div>
  </div>
</div>

<!-- TABLES -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
  <div class="card overflow-hidden">
    <div class="page-header border-b border-gray-100 py-3">
      <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2"><i class="fa fa-clock-rotate-left text-blue-500"></i> Pendaftaran Terbaru</h2>
      <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-sm btn-secondary">Semua</a>
    </div>
    <table class="table">
      <thead><tr><th>Kode</th><th>Nama</th><th>Sekolah</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($recentPendaftaran as $p)
        <tr>
          <td><a href="{{ route('admin.pendaftaran.show',$p) }}" class="text-blue-600 font-mono text-xs hover:underline">{{ $p->kode_regis }}</a></td>
          <td class="font-medium text-sm">{{ $p->siswa?->nama_siswa??'—' }}</td>
          <td class="text-xs text-gray-500">{{ $p->sekolah?->singkatan }}</td>
          <td><span class="badge {{ $p->badge_color }}">{{ $p->label_status }}</span></td>
        </tr>
        @empty<tr><td colspan="4" class="text-center text-gray-400 py-6 text-xs">Belum ada pendaftaran</td></tr>@endforelse
      </tbody>
    </table>
  </div>

  <div class="card overflow-hidden">
    <div class="page-header border-b border-gray-100 py-3">
      <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2"><i class="fa fa-bell text-yellow-500"></i> Perlu Verifikasi Bayar
        @if($bayarPending->count()>0)<span class="badge bg-red-100 text-red-700 ml-1">{{ $bayarPending->count() }}</span>@endif
      </h2>
    </div>
    <table class="table">
      <thead><tr><th>Siswa</th><th>Metode</th><th>Nominal</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($bayarPending as $b)
        <tr>
          <td><div class="font-medium text-xs">{{ $b->pendaftaran?->siswa?->nama_siswa }}</div><div class="text-gray-400 text-xs font-mono">{{ $b->pendaftaran?->kode_regis }}</div></td>
          <td class="text-xs text-gray-500">{{ $b->metodePembayaran?->nama_metode }}</td>
          <td class="text-xs font-bold text-green-700">{{ $b->nominal_formatted }}</td>
          <td><a href="{{ route('admin.pendaftaran.show',$b->pendaftaran_id) }}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a></td>
        </tr>
        @empty<tr><td colspan="4" class="text-center text-gray-400 py-6 text-xs">Tidak ada yang perlu diverifikasi</td></tr>@endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const ctx = document.getElementById('chartBulan');
  if(!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
      datasets: [{
        label: 'Pendaftaran',
        data: @json($chartData),
        backgroundColor: 'rgba(59,130,246,.75)',
        borderColor: '#1d4ed8',
        borderWidth: 1,
        borderRadius: 5,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
        x: { ticks: { font: { size: 11 } }, grid: { display: false } }
      }
    }
  });
});
</script>
@endpush
