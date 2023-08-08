@extends("app")

@section('title', 'Welcome')


@section("content")
    <div class="flex flex-row items-center justify-center">
        <a class="p-5 bg-slate-200" href="{{ route("users.index") }}">
            Go to users
        </a>
    </div>
@endsection

@section("footer_scripts")
    <script> console.log("hello guys") </script>
@endsection
