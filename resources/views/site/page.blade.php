@extends('layouts.site')

@section('title', $page->title.' – '.setting('site_name'))

@section('content')
<div class="wrap">
  <header class="list-head stagger"><span class="eyebrow">Redaksi</span><h1 data-testid="page-title">{{ $page->title }}</h1></header>
  <div class="prose page-body reveal" data-testid="page-body">{!! $page->body !!}</div>
</div>
@endsection
