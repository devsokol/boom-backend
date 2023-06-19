@extends('apiv1::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('apiv1.name') !!}
    </p>
@endsection
