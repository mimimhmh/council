@extends('layouts.app')

@section('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Create a New Thread</div>

                    <div class="panel-body">
                        <form method="POST" action="/threads">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="channel_id">Channel:</label>
                                <select class="form-control" id="channel_id"
                                       name="channel_id" >
                                    <option>Choose One...</option>
                                    @foreach($channels as $channel)
                                        <option value="{{ $channel->id }}"
                                                {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                            {{ $channel->name }}</option>
                                    @endforeach
                                </select>

                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title"
                                       value="{{ old('title') }}"
                                       name="title" />
                            </div>

                            <div class="form-group">
                                <label for="body">Body:</label>
                                {{--<textarea class="form-control" id="body" name="body" rows="8">{{ old('body') }}</textarea>--}}
                                <wysiwyg name="body"></wysiwyg>
                            </div>

                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="6LcQkj0UAAAAAEOJc_YF34CuA2eeMEPymNnfoAt3"></div>
                            </div>

                            <button type="submit" class="btn btn-primary">Publish</button>

                        </form>
                        <br />
                        @include('layouts.errors')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
