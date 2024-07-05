@extends('dashboard')

@section('title', '403 No Permission')

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
