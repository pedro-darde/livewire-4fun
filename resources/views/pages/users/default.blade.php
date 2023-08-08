@extends("app")

@section('title', 'Users')


@section("content")
    <div>
        @livewire('crud', ['modelClass' =>  \App\Models\User::class, 'routineTitle' => 'Users'], key(Str::random()))
    </div>
@endsection

@section("footer_scripts")
    <script> console.log("hello guys")</script>
@endsection
