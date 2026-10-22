<div class="adventure-page">
    <style>
        .adventure-page{font-family:system-ui;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;padding:30px;color:#333}
        .adv-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 20px 50px rgba(0,0,0,.18);max-width:900px;margin:0 auto}
        .adv-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:18px;margin:20px 0}
        .adv-avatar{border:3px solid transparent;border-radius:16px;padding:14px;text-align:center;cursor:pointer;transition:.2s;background:#f6f7fb}
        .adv-avatar:hover{transform:translateY(-4px);box-shadow:0 8px 20px rgba(0,0,0,.1)}
        .adv-avatar.active{border-color:#667eea;background:#eef0ff}
        .adv-avatar svg{display:block;margin:0 auto 8px}
        .adv-avatar span{font-size:13px;font-weight:700}
        .adv-btn{padding:12px 26px;border-radius:10px;border:none;font-weight:800;font-size:15px;cursor:pointer;background:#667eea;color:#fff}
        .adv-btn:hover{background:#5566d8}
    </style>

    <div class="adv-card">
        <h1 style="margin:0 0 6px">🎭 Pilih Avatar</h1>
        <p style="color:#777;margin:0 0 8px">Tentukan tampilan karaktermu di ruang permainan.</p>

        <div class="adv-grid">
            @foreach($avatars as $key => $avatar)
                <div class="adv-avatar {{ $selected === $key ? 'active' : '' }}"
                     wire:click="$set('selected', '{{ $key }}')"
                     role="button" tabindex="0"
                     aria-label="Pilih avatar {{ $avatar['name'] }}">
                    <x-adventure.avatar :key="$key" :size="72" />
                    <span>{{ $avatar['name'] }}</span>
                </div>
            @endforeach
        </div>

        <button class="adv-btn" wire:click="save" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">Simpan Avatar</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>

        @if(session('success'))
            <p style="color:#0f5132;background:#d1e7dd;padding:10px;border-radius:8px;margin-top:16px">{{ session('success') }}</p>
        @endif
    </div>
</div>
