<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Inventaris Barang - BAS LPKIA</title>

<style>
body{
    font-family:sans-serif;
    font-size:11px;
    color:#333;
}

.header{
    text-align:center;
    margin-bottom:16px;
}

.header h2{
    margin:0;
    font-size:16px;
}

.header p{
    margin:4px 0 0;
    font-size:11px;
    color:#666;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

th{
    background:#1e40af;
    color:white;
    padding:7px 6px;
    text-align:left;
    font-size:11px;
}

td{
    padding:6px;
    border-bottom:1px solid #ddd;
    font-size:10px;
}

tr:nth-child(even) td{
    background:#f5f7ff;
}

.badge{
    display:inline-block;
    padding:2px 7px;
    border-radius:10px;
    font-size:9px;
    font-weight:bold;
}

.badge-baik{background:#d1fae5;color:#065f46;}
.badge-cukup_baik{background:#fef9c3;color:#854d0e;}
.badge-rusak_ringan{background:#ffedd5;color:#9a3412;}
.badge-rusak_berat{background:#fee2e2;color:#991b1b;}

.badge-aktif{background:#d1fae5;color:#065f46;}
.badge-tidak_aktif{background:#f3f4f6;color:#374151;}
.badge-dipinjam{background:#dbeafe;color:#1e40af;}
.badge-dalam_perbaikan{background:#ffedd5;color:#9a3412;}

.footer{
    margin-top:20px;
    font-size:10px;
    color:#999;
    text-align:right;
}
</style>

</head>

<body>

<div class="header">
<h2>Inventaris Barang - BAS LPKIA</h2>
<p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
<p>Total Barang: {{ $items->count() }} item</p>
</div>

<table>

<thead>
<tr>
<th style="width:30px">No</th>
<th>Serial Number</th>
<th>Nama Barang</th>
<th style="width:40px">QTY</th>
<th>Kondisi</th>
<th>Status</th>
<th>Tgl. Masuk</th>
<th>Ruangan</th>
</tr>
</thead>

<tbody>

@forelse($items as $i => $item)

<tr>

<td>{{ $i+1 }}</td>

<td>{{ $item->serial_number }}</td>

<td>{{ $item->name }}</td>

<td style="text-align:center">{{ $item->quantity }}</td>

<td>
<span class="badge badge-{{ $item->condition }}">
{{ ucfirst(str_replace('_',' ',$item->condition)) }}
</span>
</td>

<td>
<span class="badge badge-{{ $item->status }}">
{{ ucfirst(str_replace('_',' ',$item->status)) }}
</span>
</td>

<td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>

<td>{{ $item->room->name ?? '-' }}</td>

</tr>

@empty

<tr>
<td colspan="8" style="text-align:center;color:#999">
Tidak ada data barang
</td>
</tr>

@endforelse

</tbody>
</table>

<div class="footer">
BAS-LPKIA — Sistem Pengadaan © {{ now()->year }}
</div>

</body>
</html>
