{{-- reCAPTCHA v3 компонент --}}
{{-- Использование: <x-recaptcha action="login" /> --}}
@props(['action' => 'submit'])

@if(config('recaptcha.enabled') && config('recaptcha.site_key'))
<input type="hidden" name="recaptcha_token" id="recaptcha_token_{{ $action }}">

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('recaptcha_token_{{ $action }}')?.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = form.querySelector('[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.7';
                }
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('recaptcha.site_key') }}', {action: '{{ $action }}'}).then(function(token) {
                        document.getElementById('recaptcha_token_{{ $action }}').value = token;
                        form.submit();
                    }).catch(function() {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.style.opacity = '1';
                        }
                        form.submit();
                    });
                });
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    .grecaptcha-badge { visibility: hidden; }
</style>
@endpush
@endif
