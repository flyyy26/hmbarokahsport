 <div class="footer">
    <div class="footer_first">
        <div class="footer_identity">
            <img src="{{ $setting?->logo ? Storage::url($setting->logo) : asset('images/logo.png') }}" alt="Barokah Sport"class="logo-image">
            <p>Toko online terpercaya untuk jaket dan celana olahraga berkualitas dengan harga bersahabat. Nikmati produk olahraga terbaik dengan material unggulan, desain modern, dan promo menarik setiap bulannya.</p>
            <div class="social_media_footer">
                @forelse($marketplaces ?? [] as $marketplace)
                    <a href="{{ $marketplace->url }}" target="_blank" title="{{ $marketplace->name }}">
                        <div class="social_media_box">
                            <iconify-icon icon="{{ $marketplace->icon }}"></iconify-icon>
                        </div>
                    </a>
                @empty
                    {{-- Default fallback jika belum ada data marketplace --}}
                    <a href="https://api.whatsapp.com/send?phone=6287866291056" target="_blank">
                        <div class="social_media_box">
                            <iconify-icon icon="ic:round-whatsapp"></iconify-icon>
                        </div>
                    </a>
                    <a href="#" target="_blank">
                        <div class="social_media_box">
                            <iconify-icon icon="basil:instagram-outline"></iconify-icon>
                        </div>
                    </a>
                    <a href="#" target="_blank">
                        <div class="social_media_box">
                            <iconify-icon icon="ic:baseline-tiktok"></iconify-icon>
                        </div>
                    </a>
                    <a href="#" target="_blank">
                        <div class="social_media_box">
                            <iconify-icon icon="simple-icons:shopee"></iconify-icon>
                        </div>
                    </a>
                    <a href="#" target="_blank">
                        <div class="social_media_box">
                            <iconify-icon icon="arcticons:lazada"></iconify-icon>
                        </div>
                    </a>
                @endforelse
            </div>
        </div>
        <div class="menu_footer">
            <div class="menu_footer_box">
                <h3>Navigation</h3>
                <ul>
                    <li><a href="{{ route('customer.contact') }}">Kontak Kami</a></li>
                    <li><a href="{{ route('customer.about') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('customer.contact') }}#faq-section">Faq</a></li>
                    <li><a href="{{ route('customer.articles.index') }}">Artikel</a></li>
                </ul>
            </div>
            <div class="menu_footer_box">
                <h3>Bantuan</h3>
                <ul>
                    <li><a href="{{ route('customer.contact', ['category' => 'pengiriman']) }}#faq-section">Pengiriman</a></li>
                    <li><a href="{{ route('customer.contact', ['category' => 'pembayaran']) }}#faq-section">Pembayaran</a></li>
                    <li><a href="{{ route('customer.size-guide') }}">Panduan Ukuran</a></li>
                    <li><a href="/testimoni">Testimoni</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer_second">
        <ul>
            <li><a href="{{ route('customer.terms') }}">Syarat & Ketentuan</a></li>
            <li><a href="{{ route('customer.privacy') }}">Kebijakan Privasi</a></li>
        </ul>
        <span>© 2026 Barokah Sport | ID</span>
    </div>
    <div class="footer_third">
        <p>
            <span>Disclaimer:</span> Kami tidak bertanggung jawab atas konten situs pihak ketiga. Verifikasi informasi dan konsultasikan dengan profesional sebelum mengambil keputusan.
        </p>
        <div class="company_data">
            <div class="company_data_box">
                <iconify-icon icon="carbon:location-filled"></iconify-icon>
                <div class="company_data_content">
                    <h5>{{ $setting?->store_name ?? 'Barokah Sport' }}</h5>
                    <p>{{ $setting?->address ?? 'Mugarsari, Tamansari, Tasikmalaya, Jawa Barat 46196' }}</p>
                </div>
            </div>
            <div class="company_data_box">
                <iconify-icon icon="mdi:envelope"></iconify-icon>
                <div class="company_data_content">
                    <h5>Email</h5>
                    <p>{{ $setting?->email ?? 'barokahsport@gmail.com' }}</p>
                </div>
            </div>
            <div class="company_data_box">
                <iconify-icon icon="ic:baseline-whatsapp"></iconify-icon>
                <div class="company_data_content">
                    <h5>WhatsApp</h5>
                    <p>{{ $setting?->whatsapp ?? '08123516518' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>