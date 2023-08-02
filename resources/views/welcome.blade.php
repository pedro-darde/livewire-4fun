@extends("app")

@section('title', 'Welcome')


@section("content")
    <div>
        <a class="p-5 bg-slate-200" href="{{ route("users.index") }}">
            Go to users
        </a>
    </div>
@endsection

@section("footer_scripts")
    <script> console.log("hello guys")</script>
@endsection
