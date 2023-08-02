@extends("app")

@section('title', 'Users')


@section("content")
    @livewire('crud', ['modelClass' =>  \App\Models\User:: class])
@endsection

@section("footer_scripts")
    <script> console.log("hello guys")</script>
@endsection
