@extends('dashboard')

@section('title', '404 Not Found')

@section('heading')
@stop

@section('content')
    <script>
        Swal.fire({
            icon: 'error',
            text: `Bạn không có thẩm quyền xem trang này`,
            showConfirmButton: false,
            timer: 3000
        })
    </script>
@stop

@section('css')
@stop

@section('js')
@stop
