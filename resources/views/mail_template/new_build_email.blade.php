<div class="container">
    <div>
        <h1>{{ trans('messages.application') }} {{ $appName }} #{{ $buildNumber }} {{ trans('messages.was_updated_into_cd') }}</h1>
    </div>
    <div>
        <h3>{{ trans('messages.please_check_link_to_install') }} : <a href="{{ $link }}">{{ trans('messages.link') }}</a></p>
    </div>
</div>
