<h2>{{ trans('messages.thank_you_for_using') }}</h2>
<p>{{ trans('messages.please_use_the_account_to_access') }}</p><br>
<p><b>{{ trans('messages.home_page') }}:</b> {{ env('APP_URL') }}</p>
<p><b>{{ trans('messages.email') }}:</b> {{ $user->email }}</p>
<p><b>{{ trans('messages.password') }}:</b> {{ $password }}</p>
