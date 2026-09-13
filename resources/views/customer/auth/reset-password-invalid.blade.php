@extends('layouts.customer')

@section('title', 'Link Tidak Valid - Barokah Sport')

@section('content')
<div style="max-width:32vw;margin:6vw auto;padding:3vw;background:#fff;border-radius:1vw;border:0.1vw solid #e2e8f0;box-shadow:0 0.5vw 2vw rgba(0,0,0,0.05);text-align:center;">
    <div style="width:5vw;height:5vw;margin:0 auto 1.5vw;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;">
        <iconify-icon icon="mdi:link-variant-off" style="font-size:3vw;color:#dc2626;"></iconify-icon>
    </div>
    <h2 style="font-size:1.5vw;font-weight:800;color:#0f172a;margin-bottom:0.5vw;">Link Tidak Valid</h2>
    <p style="font-size:0.85vw;color:#64748b;line-height:1.6;margin-bottom:2vw;">
        {{ $reason ?? 'Link reset password tidak valid atau sudah kadaluarsa.' }}
    </p>
    <a href="{{ route('customer.forgot-password') }}"
       style="display:inline-flex;align-items:center;gap:0.4vw;padding:0.75vw 1.5vw;background:linear-gradient(90deg,#FDDD57,#ecbc42,#FDDD57);color:rgb(102,72,9);text-decoration:none;border-radius:0.5vw;font-size:0.82vw;font-weight:700;">
        <iconify-icon icon="mdi:refresh"></iconify-icon>
        Ajukan Permintaan Baru
    </a>
</div>
@endsection