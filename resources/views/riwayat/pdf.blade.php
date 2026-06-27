<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Dokumen Waris — {{ $kasus->nama_mayit }}</title>
<style>
  @page {
    margin-top: 2.5cm;
    margin-bottom: 2.5cm;
    margin-left: 2.5cm;
    margin-right: 2.5cm;
  }
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 11pt;
    color: #111111;
    line-height: 1.5;
    padding: 1cm;
    margin: 0;
  }

  /* ── HEADER ── */
  .header-table {
    width: 100%;
    border-bottom: 2.5pt solid #111111;
    padding-bottom: 8pt;
    margin-bottom: 4pt;
  }
  .header-table td {
    vertical-align: bottom;
    padding: 0;
  }
  .header-left .org-name {
    font-size: 15pt;
    font-weight: bold;
    letter-spacing: 0.02em;
  }
  .header-left .org-sub {
    font-size: 9pt;
    color: #555555;
    margin-top: 2pt;
    letter-spacing: 0.04em;
  }
  .header-right {
    text-align: right;
  }
  .header-right .doc-date {
    font-size: 10pt;
    color: #333333;
  }

  /* ── JUDUL DOKUMEN ── */
  .doc-title {
    text-align: center;
    font-size: 11pt;
    font-weight: bold;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    border-bottom: 0.5pt solid #aaaaaa;
    padding: 5pt 0 8pt;
    margin-bottom: 14pt;
  }

  /* ── INFO BLOCK ── */
  .info-table {
    width: 100%;
    border-top: 0.5pt solid #aaaaaa;
    border-bottom: 0.5pt solid #aaaaaa;
    margin-bottom: 16pt;
  }
  .info-table td {
    padding: 3pt 6pt;
    font-size: 10.5pt;
    vertical-align: top;
  }
  .info-table .label {
    width: 38%;
    color: #555555;
  }
  .info-table .sep {
    width: 4%;
    color: #555555;
  }
  .info-table .val {
    font-weight: bold;
    color: #111111;
  }

  /* ── SECTION HEADING ── */
  .section-heading {
    font-size: 10pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    border-bottom: 1pt solid #111111;
    padding-bottom: 3pt;
    margin-top: 18pt;
    margin-bottom: 8pt;
  }

  /* ── DATA TABLE ── */
  .data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10pt;
    margin-bottom: 6pt;
  }
  .data-table thead tr {
    border-top: 1.5pt solid #111111;
    border-bottom: 1.5pt solid #111111;
  }
  .data-table thead th {
    padding: 5pt 6pt;
    font-size: 9pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    text-align: left;
    background-color: #f3f4f6;
  }
  .data-table thead th.center { text-align: center; }
  .data-table thead th.right  { text-align: right; }

  .data-table tbody tr {
    border-bottom: 0.5pt solid #dddddd;
  }
  .data-table tbody td {
    padding: 6pt 6pt;
    color: #222222;
  }
  .data-table tbody td.center { text-align: center; }
  .data-table tbody td.right  { text-align: right; }
  .data-table tbody td.bold   { font-weight: bold; }

  .data-table tfoot tr {
    border-top: 1.5pt solid #111111;
    border-bottom: 1.5pt solid #111111;
  }
  .data-table tfoot td {
    padding: 5pt 6pt;
    font-weight: bold;
    font-size: 10.5pt;
    background-color: #f3f4f6;
  }
  .data-table tfoot td.right { text-align: right; }

  /* ── TANDA TANGAN ── */
  .sig-section {
    margin-top: 30pt;
  }
  .sig-date {
    font-size: 10.5pt;
    margin-bottom: 8pt;
  }
  .sig-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 4pt;
  }
  .sig-table td {
    width: 33.33%;
    text-align: center;
    padding: 0 10pt;
    vertical-align: top;
  }
  .sig-label {
    font-size: 10.5pt;
    margin-bottom: 56pt;
  }
  .sig-line {
    border-top: 0.5pt solid #111111;
    padding-top: 4pt;
    font-size: 10pt;
    font-weight: bold;
  }
  .sig-role {
    font-size: 9.5pt;
    color: #555555;
    margin-top: 2pt;
  }

  /* ── FOOTER ── */
  .footer-note {
    border-top: 0.5pt solid #aaaaaa;
    margin-top: 20pt;
    padding-top: 6pt;
    font-size: 8.5pt;
    color: #888888;
    font-style: italic;
  }
</style>
</head>
<body>

  {{-- ── HEADER ── --}}
  <table class="header-table">
    <tr>
      <td class="header-left" style="width:60%">
        <div class="org-name">SISTEM FARAIDH</div>
        <div class="org-sub">Kalkulator Pembagian Waris Islam</div>
      </td>
      <td class="header-right" style="width:40%">
        <div class="doc-date">{{ $kasus->created_at->translatedFormat('d F Y') }}</div>
      </td>
    </tr>
  </table>

  {{-- ── JUDUL ── --}}
  <div class="doc-title">Dokumen Hasil Perhitungan Waris</div>

  {{-- ── INFO BLOCK ── --}}
  <table class="info-table">
    <tr>
      <td class="label">Nama Mayit</td>
      <td class="sep">:</td>
      <td class="val">{{ $kasus->nama_mayit }}</td>
    </tr>
    <tr>
      <td class="label">Harta Bersih</td>
      <td class="sep">:</td>
      <td class="val">Rp {{ number_format($kasus->harta_bersih, 0, ',', '.') }}</td>
    </tr>
    <tr>
      <td class="label">Tanggal Perhitungan</td>
      <td class="sep">:</td>
      <td class="val">{{ $kasus->created_at->translatedFormat('d F Y') }}</td>
    </tr>
  </table>

  {{-- ── TABEL FARAIDH ── --}}
  <div class="section-heading">I. Hasil Perhitungan Faraidh</div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Ahli Waris</th>
        <th class="center">Porsi</th>
        <th class="center">Jumlah</th>
        <th class="right">Per Orang</th>
        <th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($hasilFaraidh as $item)
      <tr>
        <td class="bold">{{ ucfirst($item['hubungan']) }}</td>
        <td class="center">{{ $item['bagian'] }}</td>
        <td class="center">{{ $item['jumlah'] }} orang</td>
        <td class="right">Rp {{ number_format($item['nominal'] / $item['jumlah'], 0, ',', '.') }}</td>
        <td class="right">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4">Total Harta Terdistribusi</td>
        <td class="right">Rp {{ number_format($hasilFaraidh->sum('nominal'), 0, ',', '.') }}</td>
      </tr>
    </tfoot>
  </table>

  {{-- ── TABEL ISLAH (opsional) ── --}}
  @if($hasilIslah && $hasilIslah->count())
  <div class="section-heading">II. Hasil Islah Ekonomi</div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Ahli Waris</th>
        <th class="center">Bobot</th>
        <th class="right">Faraidh Murni</th>
        <th class="right">Hasil Islah</th>
        <th class="right">Selisih</th>
      </tr>
    </thead>
    <tbody>
      @foreach($hasilIslah as $item)
      @php
        $selisih    = $item['total_islah'] - $item['faraidh'];
        $pct        = $kasus->harta_bersih > 0
                        ? ($selisih / $kasus->harta_bersih) * 100
                        : 0;
        $bobot      = $item['bobot'] ?? 0;
        $selisihStr = ($selisih >= 0 ? '+' : '') . number_format($pct, 1) . '%';
      @endphp
      <tr>
        <td class="bold">{{ ucfirst($item['hubungan']) }}</td>
        <td class="center">{{ number_format($bobot, 2) }}</td>
        <td class="right">Rp {{ number_format($item['faraidh'], 0, ',', '.') }}</td>
        <td class="right">Rp {{ number_format($item['total_islah'], 0, ',', '.') }}</td>
        <td class="right">{{ $selisihStr }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4">Total Islah</td>
        <td class="right">Rp {{ number_format($hasilIslah->sum('total_islah'), 0, ',', '.') }}</td>
      </tr>
    </tfoot>
  </table>
  @endif

  {{-- ── TANDA TANGAN ── --}}
  <div class="sig-section">
    <div class="sig-date">{{ $kasus->created_at->translatedFormat('d F Y') }}</div>
    <table class="sig-table">
      <tr>
        <td>
          <div class="sig-label">Ahli Waris (Perwakilan)</div>
          <div class="sig-line">( ................................ )</div>
          <div class="sig-role">Nama &amp; Tanda Tangan</div>
        </td>
        <td>
          <div class="sig-label">Saksi</div>
          <div class="sig-line">( ................................ )</div>
          <div class="sig-role">Nama &amp; Tanda Tangan</div>
        </td>
        <td>
          <div class="sig-label">Pakar</div>
          <div class="sig-line">( ................................ )</div>
          <div class="sig-role">Nama &amp; Tanda Tangan</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>