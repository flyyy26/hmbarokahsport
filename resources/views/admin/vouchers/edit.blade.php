@extends('layouts.admin')

@section('title', 'Edit Voucher Promo')
@section('page-title', 'Edit Voucher')

@section('content')

<div class="w-full max-w-5xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.vouchers.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Voucher
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:ticket-percent-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Edit Voucher
            </h1>
            <p class="text-sm mt-1.5 ml-12 font-mono" style="color: var(--text-5)">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded
                             bg-amber-500/10 border border-amber-500/30">
                    <iconify-icon icon="mdi:code-tags" class="text-[#ecbc42]"></iconify-icon>
                    <span class="font-bold text-[#ecbc42] tracking-wider">{{ $voucher->code }}</span>
                </span>
            </p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.vouchers._form', ['voucher' => $voucher])
    </form>

</div>

@endsection