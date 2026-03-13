<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 10px;
    color: #1a1a2e;
    background: #ffffff;
  }

  /* ── Header ── */
  .header {
    background: #1e40af;
    color: white;
    padding: 14px 20px;
    margin-bottom: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .header-brand { display: flex; align-items: center; gap: 10px; }
  .header-brand .logo-box {
    width: 36px; height: 36px;
    background: rgba(255,255,255,0.15);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 900; color: white;
    border: 1px solid rgba(255,255,255,0.25);
  }
  .header-brand .brand-name { font-size: 14px; font-weight: 800; letter-spacing: -0.3px; }
  .header-brand .brand-sub  { font-size: 9px; opacity: 0.75; margin-top: 1px; }
  .header-right { text-align: right; font-size: 9px; opacity: 0.8; line-height: 1.6; }
  .header-right strong { font-size: 10px; opacity: 1; display: block; margin-bottom: 1px; }

  /* ── Room Info ── */
  .room-section {
    margin: 0 20px 12px;
    background: #f0f4ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    gap: 20px;
    align-items: flex-start;
  }
  .room-label { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
  .room-value { font-size: 13px; font-weight: 800; color: #1e40af; margin-top: 2px; line-height: 1.2; }
  .room-desc  { font-size: 9px; color: #64748b; margin-top: 3px; }
  .room-divider { width: 1px; background: #bfdbfe; align-self: stretch; }

  /* ── Date filter badge ── */
  .filter-badge {
    margin: 0 20px 12px;
    display: inline-block;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 9px;
    color: #92400e;
  }

  /* ── Stats Row ── */
  .stats-row {
    display: flex;
    gap: 8px;
    margin: 0 20px 14px;
  }
  .stat-card {
    flex: 1;
    border-radius: 8px;
    padding: 10px 12px;
    text-align: center;
  }
  .stat-card .stat-num  { font-size: 22px; font-weight: 900; line-height: 1; }
  .stat-card .stat-lbl  { font-size: 8.5px; color: #64748b; margin-top: 3px; font-weight: 500; }
  .stat-total  { background: #dbeafe; border: 1px solid #93c5fd; }
  .stat-total  .stat-num { color: #1d4ed8; }
  .stat-baik   { background: #dcfce7; border: 1px solid #86efac; }
  .stat-baik   .stat-num { color: #15803d; }
  .stat-cukup  { background: #dbeafe; border: 1px solid #93c5fd; }
  .stat-cukup  .stat-num { color: #1d4ed8; }
  .stat-ringan { background: #fef9c3; border: 1px solid #fde047; }
  .stat-ringan .stat-num { color: #a16207; }
  .stat-berat  { background: #fee2e2; border: 1px solid #fca5a5; }
  .stat-berat  .stat-num { color: #b91c1c; }

  /* ── Section title ── */
  .section-title {
    margin: 0 20px 8px;
    font-size: 10px;
    font-weight: 800;
    color: #1e40af;
    border-left: 3px solid #1e40af;
    padding-left: 8px;
    letter-spacing: 0.3px;
  }

  /* ── Table ── */
  table {
    width: calc(100% - 40px);
    margin: 0 20px;
    border-collapse: collapse;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
  }
  thead tr {
    background: #1e40af;
    color: white;
  }
  thead th {
    padding: 8px 9px;
    text-align: left;
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
  }
  thead th.center { text-align: center; }
  tbody tr:nth-child(even) { background: #f8fafc; }
  tbody tr:nth-child(odd)  { background: #ffffff; }
  tbody td {
    padding: 7px 9px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 9.5px;
    vertical-align: middle;
    color: #1e293b;
  }
  tbody td.center { text-align: center; }
  .td-serial { font-family: monospace; font-size: 9px; color: #475569; }
  .td-name   { font-weight: 700; font-size: 10px; }
  .td-spec   { font-size: 8px; color: #94a3b8; margin-top: 1px; }
  .td-price  { font-weight: 700; text-align: right; }
  .td-date   { color: #475569; white-space: nowrap; }
  .no-cell   { color: #94a3b8; font-weight: 700; font-size: 9px; text-align: center; }

  /* ── Condition / Status badges ── */
  .badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 99px;
    font-size: 8px;
    font-weight: 700;
    white-space: nowrap;
  }
  .badge-baik        { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
  .badge-cukup_baik  { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
  .badge-rusak_ringan{ background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
  .badge-rusak_berat { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
  .badge-aktif           { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
  .badge-tidak_aktif     { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
  .badge-dipinjam        { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
  .badge-dalam_perbaikan { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

  /* ── Empty state ── */
  .empty-row td {
    text-align: center;
    padding: 30px;
    color: #94a3b8;
    font-style: italic;
  }

  /* ── Footer ── */
  .footer {
    margin-top: 16px;
    padding: 8px 20px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    font-size: 8px;
    color: #94a3b8;
  }
</style>
</head>
<body>

{{-- ── HEADER ── --}}
<div class="header">
  <div class="header-brand">
    <div class="logo-box">B</div>
    <div>
      <div class="brand-name">BAS-LPKIA</div>
      <div class="brand-sub">Sistem Pengadaan Aset &amp; Barang</div>
    </div>
  </div>
  <div class="header-right">
    <strong>Laporan Inventaris Ruangan</strong>
    Dicetak: {{ $exportedAt }}
  </div>
</div>

{{-- ── ROOM INFO ── --}}
<div class="room-section">
  <div>
    <div class="room-label">Kode Ruangan</div>
    <div class="room-value">{{ $room->code }}</div>
  </div>
  <div class="room-divider"></div>
  <div>
    <div class="room-label">Nama Ruangan</div>
    <div class="room-value">{{ $room->name }}</div>
    @if($room->description)
      <div class="room-desc">{{ $room->description }}</div>
    @endif
  </div>
</div>

{{-- ── FILTER BADGE (jika ada filter tanggal) ── --}}
@if($dateFrom || $dateTo)
<div style="margin: 0 20px 12px;">
  <span class="filter-badge">
    Filter Tanggal Masuk:
    @if($dateFrom) dari {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d M Y') }} @endif
    @if($dateTo)   s/d {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d M Y') }} @endif
  </span>
</div>
@endif

{{-- ── STATS ── --}}
<div class="stats-row">
  <div class="stat-card stat-total">
    <div class="stat-num">{{ $stats['total'] }}</div>
    <div class="stat-lbl">Total Barang</div>
  </div>
  <div class="stat-card stat-baik">
    <div class="stat-num">{{ $stats['baik'] }}</div>
    <div class="stat-lbl">Kondisi Baik</div>
  </div>
  <div class="stat-card stat-cukup">
    <div class="stat-num">{{ $stats['cukup_baik'] }}</div>
    <div class="stat-lbl">Cukup Baik</div>
  </div>
  <div class="stat-card stat-ringan">
    <div class="stat-num">{{ $stats['rusak_ringan'] }}</div>
    <div class="stat-lbl">Rusak Ringan</div>
  </div>
  <div class="stat-card stat-berat">
    <div class="stat-num">{{ $stats['rusak_berat'] }}</div>
    <div class="stat-lbl">Rusak Berat</div>
  </div>
</div>

{{-- ── TABLE TITLE ── --}}
<div class="section-title">Daftar Barang</div>

{{-- ── TABLE ── --}}
<table>
  <thead>
    <tr>
      <th style="width:4%"  class="center">NO</th>
      <th style="width:14%">SERIAL NUMBER</th>
      <th style="width:26%">NAMA BARANG</th>
      <th style="width:6%"  class="center">QTY</th>
      <th style="width:12%">KONDISI</th>
      <th style="width:12%">STATUS</th>
      <th style="width:13%">TGL. MASUK</th>
      <th style="width:13%">HARGA</th>
    </tr>
  </thead>
  <tbody>
    @forelse($items as $index => $item)
    @php
      $movedAt     = $item->latestMovement?->moved_at ?? $item->purchase_date;
      $displayDate = $movedAt
        ? \Carbon\Carbon::parse($movedAt)->translatedFormat('d M Y')
        : '—';

      $conditionLabel = match($item->condition) {
        'baik'         => 'Baik',
        'cukup_baik'   => 'Cukup Baik',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat'  => 'Rusak Berat',
        default        => ucfirst(str_replace('_', ' ', $item->condition ?? '—')),
      };
      $statusLabel = match($item->status) {
        'aktif'           => 'Aktif',
        'tidak_aktif'     => 'Tidak Aktif',
        'dipinjam'        => 'Dipinjam',
        'dalam_perbaikan' => 'Dalam Perbaikan',
        default           => ucfirst(str_replace('_', ' ', $item->status ?? '—')),
      };
    @endphp
    <tr>
      <td class="no-cell">{{ $index + 1 }}</td>
      <td class="td-serial">{{ $item->serial_number ?: '—' }}</td>
      <td>
        <div class="td-name">{{ $item->name }}</div>
        @if($item->specification)
          <div class="td-spec">{{ \Illuminate\Support\Str::limit($item->specification, 60) }}</div>
        @endif
      </td>
      <td class="center" style="font-weight:700;">{{ $item->quantity ?? 1 }}</td>
      <td>
        <span class="badge badge-{{ $item->condition }}">{{ $conditionLabel }}</span>
      </td>
      <td>
        <span class="badge badge-{{ $item->status }}">{{ $statusLabel }}</span>
      </td>
      <td class="td-date">{{ $displayDate }}</td>
      <td class="td-price">
        @if($item->purchase_price)
          Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
        @else
          —
        @endif
      </td>
    </tr>
    @empty
    <tr class="empty-row">
      <td colspan="8">Tidak ada barang di ruangan ini</td>
    </tr>
    @endforelse
  </tbody>
</table>

{{-- ── FOOTER ── --}}
<div class="footer">
  <span>BAS-LPKIA — Sistem Pengadaan Aset &amp; Barang</span>
  <span>Dokumen ini digenerate otomatis oleh sistem &bull; {{ $exportedAt }}</span>
</div>

</body>
</html>
