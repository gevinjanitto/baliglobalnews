@extends('layouts.admin')
@section('title', $post->exists ? 'Edit Berita' : 'Tulis Berita')
@section('content')
@php
  $chosenCats = array_map('intval', old('categories', $post->exists ? $post->categories->pluck('id')->all() : []));
  $tagCsv = old('tags', $post->exists ? $post->tags->pluck('name')->implode(',') : '');
  $viewsMode = old('views_mode', $post->views_manual !== null ? 'manual' : 'asli');
@endphp
<div class="topline">
  <div><h1>{{ $post->exists ? 'Edit Berita' : 'Tulis Berita' }}</h1><p>Isi berita, pilih kategori wilayah, kategori, tag, dan iklan yang tampil.</p></div>
  <div style="display:flex;gap:8px">
    @if($post->exists && $post->status === 'published')<a href="{{ $post->url }}" target="_blank" class="btn btn-ghost"><i data-lucide="eye"></i> Lihat</a>@endif
    <a href="{{ route('admin.posts.index') }}" class="btn btn-ghost"><i data-lucide="arrow-left"></i> Kembali</a>
  </div>
</div>

<form method="post" enctype="multipart/form-data" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}" id="postForm" data-testid="post-form" data-preview-url="{{ route('admin.posts.preview') }}">
  @csrf @if($post->exists) @method('PUT') <input type="hidden" name="post_id" value="{{ $post->id }}"> @endif
  <div class="form-grid">
    <div>
      <div class="panel">
        <div class="field"><label>Judul berita</label><input type="text" name="title" value="{{ old('title', $post->title) }}" required style="font-family:var(--serif);font-size:22px" data-testid="post-title-input"></div>
        <div class="field"><label>Slug URL <span class="hint">(kosongkan untuk otomatis)</span></label><input type="text" name="slug" value="{{ old('slug', $post->slug) }}" data-testid="post-slug-input"></div>
        <div class="field"><label>Ringkasan <span class="hint">(tampil di kartu & lead artikel)</span></label><textarea name="excerpt" maxlength="500" data-testid="post-excerpt-input">{{ old('excerpt', $post->excerpt) }}</textarea></div>
        <div class="field"><label>Isi berita <span class="hint">(foto bisa ditempel langsung, klik foto untuk atur ukuran/posisi, seret untuk memindahkan)</span></label>
          <div data-testid="post-body-editor"><textarea name="body" id="bodyInput">{{ old('body', $post->body) }}</textarea></div>
        </div>
      </div>

      <div class="panel" data-testid="post-ads-panel">
        <h3>Iklan di artikel ini<small>Centang iklan yang ingin ditampilkan pada posisi tertentu. Iklan nonaktif/kedaluwarsa tidak akan tampil.</small></h3>
        @if($ads->isEmpty())
          <p class="sub">Belum ada iklan. <a href="{{ route('admin.ads.create') }}" style="color:var(--red)">Tambah di Master Iklan</a>.</p>
        @endif
        @foreach(\App\Models\Post::PLACEMENTS as $key => $label)
        @php $chosen = old('ads.'.$key, $selected[$key] ?? []); @endphp
        <div class="place">
          <h4>{{ $label }} <span class="hint">{{ $key === 'sidebar' ? 'disarankan format Kotak' : 'disarankan format Banner' }}</span></h4>
          <div class="ad-pick">
            @foreach($ads as $ad)
            <label class="check" data-testid="post-ad-{{ $key }}-{{ $ad->id }}">
              <input type="checkbox" name="ads[{{ $key }}][]" value="{{ $ad->id }}" @checked(in_array($ad->id, $chosen))>
              <img src="{{ $ad->image_url }}" alt="">
              <span>{{ $ad->name }}<small>{{ $ad->format === 'kotak' ? 'Kotak' : 'Banner' }} · {{ $ad->is_live ? 'Aktif' : 'Nonaktif' }}</small></span>
            </label>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="sticky">
      <div class="panel">
        <div class="row">
          <div class="field"><label>Status</label><select name="status" id="statusSelect" data-testid="post-status-select"><option value="published" @selected(old('status', $post->status) === 'published')>Terbit</option><option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option></select></div>
          <div class="field"><label>Penulis</label><input type="text" name="author_name" value="{{ old('author_name', $post->author_name) }}" required data-testid="post-author-input"></div>
        </div>
        <div class="field"><label>Waktu terbit</label><input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" data-testid="post-published-input"></div>
        <div class="field" data-testid="post-views-field"><label>Jumlah dibaca <span class="hint">(angka "dibaca" di website)</span></label>
          <div class="seg">
            <label><input type="radio" name="views_mode" value="asli" @checked($viewsMode === 'asli') data-testid="post-views-mode-asli"><span>Data asli</span></label>
            <label><input type="radio" name="views_mode" value="manual" @checked($viewsMode === 'manual') data-testid="post-views-mode-manual"><span>Atur manual</span></label>
          </div>
          <div class="views-input" id="viewsManualBox" @if($viewsMode !== 'manual') hidden @endif><i data-lucide="eye"></i><input type="number" name="views_manual" min="0" step="1" value="{{ old('views_manual', $post->views_manual ?? $post->views ?? 0) }}" data-testid="post-views-input"></div>
          <div class="hint" style="margin-top:6px" data-testid="post-views-real">Dibaca asli (tercatat otomatis): <b>{{ number_format($post->views ?? 0, 0, ',', '.') }}</b>. Pilih "Data asli" agar website menampilkan angka sebenarnya.</div>
        </div>
        <div class="save-row">
          <button type="button" class="btn btn-ghost" id="previewBtn" data-testid="post-preview-btn"><i data-lucide="monitor-play"></i> Pratinjau</button>
          <button class="btn btn-red" id="saveBtn" data-testid="post-save-btn"><i data-lucide="save"></i> Simpan Berita</button>
        </div>
        <p class="hint" style="margin:8px 0 0;text-align:center">Berita berstatus Terbit akan ditampilkan pratinjaunya dulu sebelum dipublikasikan.</p>
      </div>
      <div class="panel">
        <div class="field"><label>Kategori Wilayah</label>
          <select name="region_id" data-testid="post-region-select"><option value="">— Pilih wilayah —</option>
            @foreach($regions as $r)<option value="{{ $r->id }}" @selected(old('region_id', $post->region_id) == $r->id)>{{ $r->full_name }}</option>@endforeach
          </select></div>
        <div class="field"><label>Kategori <span class="hint">(bisa pilih lebih dari satu)</span></label>
          <div class="cat-list" data-testid="post-category-list">
            @foreach($categories as $c)
            <label class="check plain-check" data-testid="post-category-{{ $c->id }}"><input type="checkbox" name="categories[]" value="{{ $c->id }}" @checked(in_array($c->id, $chosenCats))><span>{{ $c->name }}</span></label>
            @endforeach
          </div>
        </div>
        <div class="field"><label>Tag <span class="hint">(ketik lalu Enter / koma)</span></label>
          <div class="tag-input" id="tagBox" data-testid="post-tags-box">
            <input type="text" id="tagEntry" list="tagSuggest" placeholder="mis. Pemilu 2026" data-testid="post-tag-entry">
          </div>
          <datalist id="tagSuggest">@foreach($allTags as $t)<option value="{{ $t }}">@endforeach</datalist>
          <input type="hidden" name="tags" id="tagsInput" value="{{ $tagCsv }}" data-testid="post-tags-hidden">
        </div>
        <label>Penanda tampilan</label>
        <label class="check"><input type="checkbox" name="is_headline" value="1" @checked(old('is_headline', $post->is_headline)) data-testid="post-headline-check"><span>Berita Utama<small>Masuk blok besar di landing</small></span></label>
        <label class="check"><input type="checkbox" name="is_editor_choice" value="1" @checked(old('is_editor_choice', $post->is_editor_choice)) data-testid="post-editor-check"><span>Pilihan Redaksi<small>Masuk carousel Pilihan Redaksi</small></span></label>
        <label class="check"><input type="checkbox" name="is_breaking" value="1" @checked(old('is_breaking', $post->is_breaking)) data-testid="post-breaking-check"><span>Berita Terkini<small>Tampil di ticker berjalan atas</small></span></label>
      </div>
      <div class="panel">
        <div class="field"><label>Gambar utama <span class="hint">(JPG/PNG/WebP, maks 5MB)</span></label>
          <div class="preview" id="imgPreview">@if($post->image_url)<img src="{{ $post->image_url }}" alt="">@else Belum ada gambar @endif</div>
          <input type="file" name="image" accept="image/*" data-preview="#imgPreview" style="margin-top:10px" data-testid="post-image-input">
        </div>
        <div class="field" style="margin:0"><label>Keterangan foto</label><input type="text" name="image_caption" value="{{ old('image_caption', $post->image_caption) }}" data-testid="post-caption-input"></div>
      </div>
    </div>
  </div>
</form>

<div class="pv-modal" id="pvModal" hidden data-testid="preview-modal">
  <div class="pv-box">
    <div class="pv-head">
      <div><b>Pratinjau Berita</b><span class="hint" id="pvNote">Begini tampilan berita di website. Periksa sebelum dipublikasikan.</span></div>
      <div class="pv-devices">
        <button type="button" class="on" data-w="100%" data-testid="preview-desktop-btn"><i data-lucide="monitor"></i></button>
        <button type="button" data-w="400px" data-testid="preview-mobile-btn"><i data-lucide="smartphone"></i></button>
      </div>
    </div>
    <div class="pv-frame"><div class="pv-loading" id="pvLoading">Menyiapkan pratinjau…</div><iframe id="pvFrame" title="Pratinjau" data-testid="preview-iframe"></iframe></div>
    <div class="pv-foot">
      <button type="button" class="btn btn-ghost" id="pvBack" data-testid="preview-back-btn"><i data-lucide="pencil"></i> Kembali Edit</button>
      <button type="button" class="btn btn-red" id="pvConfirm" data-testid="preview-publish-btn"><i data-lucide="send"></i> <span>Terbitkan Sekarang</span></button>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js" referrerpolicy="origin"></script>
<script>
const csrf = document.querySelector('input[name=_token]').value;
tinymce.init({
  selector: '#bodyInput', license_key: 'gpl', promotion: false, branding: false, height: 620, menubar: 'file edit view insert format table tools',
  language: 'id', language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@24.12.9/langs7/id.js',
  plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks visualchars code fullscreen insertdatetime table help wordcount emoticons quickbars pagebreak nonbreaking directionality autoresize',
  toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough subscript superscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | lineheight bullist numlist outdent indent | link image table blockquote hr | charmap emoticons | removeformat | searchreplace code fullscreen',
  toolbar_mode: 'wrap', min_height: 520, autoresize_bottom_margin: 40,
  block_formats: 'Paragraf=p; Judul 2=h2; Judul 3=h3; Judul 4=h4; Kutipan=blockquote; Preformatted=pre',
  font_size_formats: '12px 14px 16px 18px 20px 24px 28px 32px 40px',
  quickbars_selection_toolbar: 'bold italic underline | forecolor | alignleft aligncenter alignright alignjustify | quicklink blockquote',
  quickbars_insert_toolbar: 'image quicktable hr',
  quickbars_image_toolbar: 'alignleft aligncenter alignright | image',
  image_advtab: true, image_caption: true, image_title: true, object_resizing: 'img', resize_img_proportional: true,
  paste_data_images: true, automatic_uploads: true, images_reuse_filename: false, convert_urls: false,
  images_file_types: 'jpeg,jpg,jpe,png,gif,webp',
  images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
    const fd = new FormData(); fd.append('file', blobInfo.blob(), blobInfo.filename()); fd.append('_token', csrf);
    fetch(@json(route('admin.posts.upload-image')), { method: 'POST', body: fd, headers: { Accept: 'application/json' } })
      .then(r => r.json().then(j => r.ok ? resolve(j.location) : reject({ message: j.message || 'Gagal mengunggah gambar', remove: true })))
      .catch(() => reject({ message: 'Gagal mengunggah gambar', remove: true }));
  }),
  content_style: "body{font-family:Manrope,system-ui,sans-serif;font-size:17px;line-height:1.75;color:#0F172A;max-width:820px;margin:16px auto;padding:0 12px}img{max-width:100%;height:auto;border-radius:12px}img[style*='float: left']{margin:4px 18px 10px 0}img[style*='float: right']{margin:4px 0 10px 18px}figure.image{display:table;margin:1em auto}figure.image figcaption{font-size:13px;color:#64748B;text-align:center}blockquote{border-left:4px solid #C8161D;margin:1.2em 0;padding:.4em 1.2em;font-style:italic;color:#334155}table{border-collapse:collapse}td,th{border:1px solid #CBD5E1;padding:6px 10px}",
  content_css: 'https://fonts.googleapis.com/css2?family=Manrope:wght@400..800&display=swap',
});

const form = document.getElementById('postForm');
// views mode
form.querySelectorAll('input[name=views_mode]').forEach(r => r.addEventListener('change', () => {
  document.getElementById('viewsManualBox').hidden = form.views_mode.value !== 'manual';
}));

// tags chips
const tagsInput = document.getElementById('tagsInput'), tagBox = document.getElementById('tagBox'), tagEntry = document.getElementById('tagEntry');
let tags = tagsInput.value.split(',').map(t => t.trim()).filter(Boolean);
const renderTags = () => {
  tagBox.querySelectorAll('.chipx').forEach(c => c.remove());
  tags.forEach((t, i) => {
    const c = document.createElement('span'); c.className = 'chipx'; c.dataset.testid = 'post-tag-chip-' + i; c.textContent = t;
    const x = document.createElement('button'); x.type = 'button'; x.innerHTML = '&times;'; x.setAttribute('aria-label', 'Hapus tag');
    x.onclick = () => { tags.splice(i, 1); renderTags(); }; c.appendChild(x); tagBox.insertBefore(c, tagEntry);
  });
  tagsInput.value = tags.join(',');
};
const addTag = () => {
  tagEntry.value.split(',').map(t => t.trim()).filter(Boolean).forEach(t => { if (!tags.some(x => x.toLowerCase() === t.toLowerCase())) tags.push(t); });
  tagEntry.value = ''; renderTags();
};
tagEntry.addEventListener('keydown', e => {
  if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); addTag(); }
  if (e.key === 'Backspace' && !tagEntry.value && tags.length) { tags.pop(); renderTags(); }
});
tagEntry.addEventListener('change', addTag);
tagEntry.addEventListener('blur', addTag);
tagBox.addEventListener('click', () => tagEntry.focus());
renderTags();

// preview modal
const modal = document.getElementById('pvModal'), frame = document.getElementById('pvFrame'), loading = document.getElementById('pvLoading'), confirmBtn = document.getElementById('pvConfirm');
document.body.appendChild(modal);
let confirmed = false;
const openPreview = async () => {
  addTag(); tinymce.triggerSave();
  if (!form.title.value.trim()) { form.title.reportValidity(); return; }
  const publish = form.status.value === 'published';
  confirmBtn.querySelector('span').textContent = publish ? 'Terbitkan Sekarang' : 'Simpan sebagai Draft';
  modal.hidden = false; document.body.style.overflow = 'hidden'; loading.hidden = false; loading.textContent = 'Menyiapkan pratinjau…'; frame.srcdoc = '';
  const fd = new FormData(form); fd.delete('_method');
  try {
    const r = await fetch(form.dataset.previewUrl, { method: 'POST', body: fd, headers: { Accept: 'text/html' } });
    if (!r.ok) throw new Error();
    frame.srcdoc = await r.text(); frame.onload = () => { loading.hidden = true; };
  } catch (e) { loading.textContent = 'Pratinjau gagal dimuat. Periksa isian lalu coba lagi.'; }
};
const closePreview = () => { modal.hidden = true; document.body.style.overflow = ''; };
document.getElementById('previewBtn').addEventListener('click', openPreview);
document.getElementById('pvBack').addEventListener('click', closePreview);
modal.addEventListener('click', e => { if (e.target === modal) closePreview(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closePreview(); });
confirmBtn.addEventListener('click', () => { confirmed = true; tinymce.triggerSave(); form.requestSubmit(document.getElementById('saveBtn')); });
modal.querySelectorAll('.pv-devices button').forEach(b => b.addEventListener('click', () => {
  modal.querySelectorAll('.pv-devices button').forEach(x => x.classList.toggle('on', x === b)); frame.style.width = b.dataset.w;
}));
form.addEventListener('submit', e => {
  tinymce.triggerSave(); addTag();
  if (form.status.value === 'published' && !confirmed) { e.preventDefault(); openPreview(); }
});
</script>
@endpush
