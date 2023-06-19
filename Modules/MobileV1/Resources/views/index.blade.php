@extends('mobilev1::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('mobilev1.name') !!}
    </p>
@endsection
