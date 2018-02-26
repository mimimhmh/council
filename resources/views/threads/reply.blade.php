<reply inline-template :attributes="{{ $reply }}" v-cloak>

    <div class="panel panel-default">
        <div id="reply-{{ $reply->id }}" class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a href="{{ route('profiles', $reply->owner) }}">
                        {{ $reply->owner->name }}
                    </a> said
                    {{ $reply->created_at->diffForHumans() }}
                </h5>
                @if (Auth::check())
                    <div>
                        <favorite :reply="{{ $reply }}">

                        </favorite>
                    </div>
                @endif
            </div>
        </div>
    </div>

</reply>
