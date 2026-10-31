{{--
    reCAPTCHA v3 for one form. Include inside the <form>, just before its
    submit button, with the action its route's recaptcha:<action> middleware
    checks for:

        @include('partials.recaptcha', ['action' => 'contact'])

    Until both reCAPTCHA keys are set this renders nothing but the error line.
--}}
@if(app(\App\Services\Recaptcha::class)->enabled())
    <input type="hidden" name="g-recaptcha-response" data-recaptcha-action="{{ $action }}">

    @once
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
            <script>
                (function () {
                    var siteKey = @json(config('services.recaptcha.site_key'));

                    // A token lasts two minutes, so each form fetches one as it
                    // is sent rather than when the page loads. Listening on the
                    // document's capture phase runs this before any other submit
                    // handler on the form.
                    document.addEventListener('submit', function (event) {
                        var form = event.target;
                        var field = form.querySelector('input[name="g-recaptcha-response"]');
                        if (!field) return;

                        event.preventDefault();
                        event.stopPropagation();
                        if (form.dataset.recaptchaPending) return;
                        form.dataset.recaptchaPending = '1';

                        var sent = false;
                        function send() {
                            if (sent) return;
                            sent = true;
                            delete form.dataset.recaptchaPending;
                            form.submit();
                        }

                        // If Google's script is blocked or slow, send anyway: the
                        // server then explains, rather than the button doing nothing.
                        setTimeout(send, 8000);
                        if (!window.grecaptcha) return send();

                        grecaptcha.ready(function () {
                            grecaptcha.execute(siteKey, { action: field.dataset.recaptchaAction })
                                .then(function (token) { field.value = token; send(); }, send);
                        });
                    }, true);
                })();
            </script>
        @endpush
    @endonce
@endif
@error('recaptcha_' . $action)
    <p class="text-danger small mt-2 mb-2">{{ $message }}</p>
@enderror
