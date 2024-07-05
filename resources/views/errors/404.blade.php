@extends('dashboard')

@section('title', '404 Not Found')

@section('heading')
@stop

@section('content')
    <script>
        Swal.fire({
            icon: 'error',
            text: `Trang bạn truy cập không tồn tại`,
            showConfirmButton: false,
            timer: 3000
        })
    </script>
@stop

@section('css')
@stop

@section('js')
@stop
