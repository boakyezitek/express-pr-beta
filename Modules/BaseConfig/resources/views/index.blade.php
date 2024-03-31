@extends('baseconfig::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('baseconfig.name') !!}</p>
@endsection
