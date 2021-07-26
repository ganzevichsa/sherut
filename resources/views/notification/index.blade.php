@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>Notification admin send</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel-body">
            
            <form method="POST">
                @csrf
                <div class="mb-3" style="margin-bottom: 20px;">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" rows="10" name="message" id="message" required=""></textarea>
                    <div id="emailHelp" class="form-text">.Message for notification all users</div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>

        </div>
    </div>
    <script type="text/javascript" src="/js/jobs.js"></script>
@endsection
