@extends('layouts.main')

@section('content')

    <div id="page-title">
        <h2>משתמשים</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel-body">
            <a class="btn btn-info btn-md" href="{{ route('users.create') }}">
                צור משתמש
            </a>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable"
                   id="categoriesList">
                <thead>
                <tr>
                    <th>אִרגוּן</th>
                    <th>תַפְקִיד</th>
                    <th>שֵׁם</th>
                    <th>אימייל</th>
                    <th>טלפון</th>
                    <th>נוצר ב</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->organization ? $user->organization->name : '' }}</td>
                        <td>{{ $user->role ? $user->role->name : '' }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>
                            {{--<form action="{{ route('users.destroy', $user->id) }}" method="POST">--}}
                                {{--<input type="hidden" name="_method" value="DELETE">--}}
                                {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                                <a class="btn btn-gray" href="{{ route('users.edit',$user->id) }}">לַעֲרוֹך</a>
                                {{--<button class="btn btn-danger">Delete</button>--}}
                            {{--</form>--}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript" src="/js/jobs.js"></script>
    <script src="{ { asset('js/socket.io.js') } }"></script>
    <script>

        var socket = io('https://api.sherutbekalut.co.il:3000');
        
        socket.on("connect_error", (err) => {
            console.log(`connect_error due to ${err.message}`);
        });
        socket.on('chat:message', function (data) {
            console.log(data);
        });
        socket.on('connection', function (data) {
            console.log(data);

        });
        socket.on('laravel_database_notification:send', function (data) {
            console.log(data);
            console.log('laravel_database_notification Recieved2: ');
        });
        socket.on('laravel_database_chat:message', function (data) {
            console.log(data);
            console.log('Message Recieved2: ');
        });


    </script>
    <!-- <script>
    var socket = io.connect('wss://api.sherutbekalut.co.il:8890');
    
    socket.on("connect_error", (err) => {
        console.log(`connect_error due to ${err.message}`);
        });
    socket.on('message', function (data) {
        data = jQuery.parseJSON(data);
        $( "#messages" ).append( "<strong>"+data.user+":</strong><p>"+data.message+"</p>" );
    });
    $("#send-message").click(function(e){
        e.preventDefault();
        var _token = $("input[name='_token']").val();
        var user = $("input[name='user']").val();
        var message = $(".message").val();
        if(message != ''){
            $.ajax({
                type: "POST",
                url: '{!! URL::to("sendmessage") !!}',
                dataType: "json",
                data: {'_token':_token, 'message':message, 'user':user},
                success:function(data) {
                    $(".message").val('');
                }
            });
        }
    })
</script> -->
@endsection
