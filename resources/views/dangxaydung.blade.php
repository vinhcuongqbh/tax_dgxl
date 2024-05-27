@extends('dashboard')

@section('title', 'Under Contruction')

@section('heading')
@stop

@section('content')
    <script>
        Swal.fire({
            icon: 'error',
            text: `Chức năng đang được xây dựng. Vui lòng quay lại sau.`,
            showConfirmButton: false,
            timer: 3000
        })
    </script>
@stop

@section('css')
@stop

@section('js')
@stop
