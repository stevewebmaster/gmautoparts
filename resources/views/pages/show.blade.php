@extends('layouts.app')

@section('title', $page->title)
@section('meta_description', $page->meta_description)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="h2 mb-4">{{ $page->title }}</h1>
                <div class="content-body">{!! $page->content !!}</div>
            </div>
        </div>
    </div>
@endsection
