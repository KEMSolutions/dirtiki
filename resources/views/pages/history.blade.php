@extends('layouts.app') @section('content')
<div class="section">
    <section class="container">
        {{ Breadcrumbs::render('page-history', $page) }}
    </section>
</div>

@endsection