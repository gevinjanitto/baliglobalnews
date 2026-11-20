@extends('layouts.admin')
@section('title', 'Halaman')
@section('content')
<div class="topline">
  <div><h1>Halaman</h1><p>Halaman statis seperti Box Redaksi dan Pedoman Media Siber.</p></div>
  <a href="{{ route('admin.pages.create') }}" class="btn btn-red" data-testid="new-page-btn"><i data-lucide="plus"></i> Tambah Halaman</a>
</div>
<div class="panel" style="padding:6px 8px">
  <table data-testid="pages-table">
    <thead><tr><th>Judul</th><th>URL</th><th>Footer</th><th></th></tr></thead>
    <tbody>
    @forelse($pages as $pg)
      <tr data-testid="page-row-{{ $pg->id }}">
        <td class="ttl">{{ $pg->title }}</td>
        <td class="sub"><a href="{{ route('page.show', $pg->slug) }}" target="_blank">/halaman/{{ $pg->slug }}</a></td>
        <td>{!! $pg->show_in_footer ? '<span class="tag ok">Ya</span>' : '<span class="tag off">Tidak</span>' !!}</td>
        <td><div class="actions">
          <a href="{{ route('admin.pages.edit', $pg) }}" class="btn btn-sm btn-ghost" data-testid="edit-page-{{ $pg->id }}"><i data-lucide="pencil"></i></a>
          <form method="post" action="{{ route('admin.pages.destroy', $pg) }}" data-confirm="Hapus halaman ini?">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" data-testid="delete-page-{{ $pg->id }}"><i data-lucide="trash-2"></i></button></form>
        </div></td>
      </tr>
    @empty
      <tr><td colspan="4" class="empty-row">Belum ada halaman.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
@endsection
