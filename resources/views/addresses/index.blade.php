@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>כתובות</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel-body">
            <button class="btn btn-info btn-md"
                    onclick="createRow('#addressModal','{{ route('addresses.store') }}', '#addressList', true, 'addresses', false, null)">
                צור כתובת
            </button>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable"
                   id="categoriesList">
                <thead>
                <tr>
                    <th>שֵׁם</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($addresses as $address)
                    <tr>
                        <td>{{ $address->name }}</td>
                        <td>
                            <form action="{{ route('addresses.destroy', $address->id) }}" method="POST">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <a class="btn btn-gray"
                                   onclick="createRow('#addressModal','{{ route('addresses.store') }}', '#addressList', true, 'addresses', '{{$address->id}}', '')">לַעֲרוֹך</a>
                                <button class="btn btn-danger">לִמְחוֹק</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript" src="/js/jobs.js"></script>
@endsection
