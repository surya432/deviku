@extends('parts.default')
@section('title-page')
Dahsboard
@endsection 
@section('content')
hallo {{ Sentinel::getUser()->first_name}}
@endsection