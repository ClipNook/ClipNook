<x-layouts.app :title="__('ui.auth.sign_in_with_twitch')">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <!-- Info / Privacy -->
            <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm overflow-hidden p-6">
                <header class="flex items-start gap-4">
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ __('twitch.login_title') }}</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ __('twitch.login_subtitle') }}</p>
                        <div class="mt-3 inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                            <i class="fas fa-shield-alt text-gray-400" aria-hidden="true"></i>
                            <span>{{ __('twitch.privacy.short_intro', ['days' => $privacy['data_retention'] ?? config('services.twitch.privacy.data_retention')]) }}</span>
                        </div>
                    </div>
                </header>

                {{-- Status / Flash --}}
                @if(session('status'))
                    <div class="mt-4 p-4 rounded bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 text-sm" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-4 p-4 rounded bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-sm" role="alert">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6 text-sm text-gray-600 dark:text-gray-300 space-y-4">
                    <p>{{ __('twitch.login_privacy_intro') }}</p>

                    <ul class="space-y-2 list-none pl-0">
                        <li class="flex gap-3 items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5" aria-hidden="true"></i>
                            <span>
                                {{ __('twitch.privacy_item_tokens') }}
                            </span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5" aria-hidden="true"></i>
                            <span>
                                {{ __('twitch.privacy_item_ip') }}:
                                <strong class="{{ ($privacy['anonymize_ip'] ?? config('services.twitch.privacy.anonymize_ip')) ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                    {{ ($privacy['anonymize_ip'] ?? config('services.twitch.privacy.anonymize_ip')) ? __('twitch.privacy_yes') : __('twitch.privacy_no') }}
                                </strong>
                            </span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5" aria-hidden="true"></i>
                            <span>
                                {{ __('twitch.privacy_item_logging') }}:
                                <strong class="{{ ($privacy['log_requests'] ?? config('services.twitch.privacy.log_requests')) ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                    {{ ($privacy['log_requests'] ?? config('services.twitch.privacy.log_requests')) ? __('twitch.privacy_yes') : __('twitch.privacy_no') }}
                                </strong>
                            </span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5" aria-hidden="true"></i>
                            <span>
                                {{ __('twitch.privacy.avatar_download') }}
                            </span>
                        </li>
                    </ul>

                    <p class="mt-2 text-sm">{{ __('twitch.login_privacy_more') }} <a href="{{ Route::has('privacy') ? route('privacy') : '#privacy' }}" class="underline font-semibold" id="privacy-link" data-accent="text" rel="noopener">{{ __('ui.footer.privacy') }}</a></p>

                    <p class="mt-2 text-xs text-gray-500">{{ __('twitch.login_privacy_note') }}</p>
                </div>

                <footer class="mt-6 text-xs text-gray-500">
                    <p class="flex items-center gap-2"><i class="fas fa-shield-alt"></i> {{ __('twitch.privacy.data_usage') ?? __('twitch.privacy.data_usage') }}</p>
                </footer>
            </section>

            <!-- Auth / CTA -->
            <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm overflow-hidden p-6">
                <div class="flex flex-col h-full">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fab fa-twitch text-indigo-600 dark:text-indigo-400"></i>
                            {{ __('twitch.login_cta') }}
                        </h2>
                        @if($twitch_scopes)
                            <div class="mt-2 text-xs text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900 rounded px-3 py-1 inline-block">
                                {{ $twitch_scopes }}
                            </div>
                        @endif
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ __('twitch.login_cta_sub') }}</p>
                    </div>

                    @if(!empty($twitch_missing))
                        <div class="p-4 rounded bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1" aria-hidden="true"></i>
                            <div>
                                <p class="font-semibold text-yellow-900 dark:text-yellow-200">{{ __('twitch.login_need_config') }}</p>
                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ __('twitch.login_config_doc') }}</p>
                            </div>
                        </div>
                    @else
                        <form method="GET" action="{{ route('auth.twitch.redirect') }}" id="twitch-login-form" class="mt-2" x-data="{ consent: false, loading: false, showConsentError: false }" @submit="if(!consent){ showConsentError = true; $refs.consent.focus(); $event.preventDefault(); } else { showConsentError = false; loading = true }">
                            <div class="space-y-5">
                                <div class="flex items-center gap-3" x-bind:class="showConsentError ? 'border border-red-300 rounded p-2 bg-red-50 dark:bg-red-950' : ''" x-ref="consentWrapper">
                                    <input id="consent" name="consent" type="checkbox" x-model="consent" x-ref="consent" aria-describedby="consent-error" aria-required="true" class="h-4 w-4 text-indigo-600 focus-visible:border-indigo-500 border-gray-300 rounded">
                                    <label for="consent" class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ __('twitch.privacy.consent_required') }}
                                    </label>
                                </div>
                                <p id="consent-error" x-show="showConsentError" x-cloak class="text-sm text-red-700 dark:text-red-300 mt-2" role="alert" aria-live="polite">
                                    {{ __('twitch.privacy.consent_error') }}
                                </p>
                                <div class="flex items-center gap-3">
                                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus-visible:border-indigo-500 border-gray-300 rounded">
                                    <label for="remember" class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ __('ui.auth.remember_me') }}
                                    </label>
                                </div>
                                <button type="submit" id="twitch-submit" data-accent="bg" class="w-full inline-flex items-center justify-center gap-3 px-6 py-3 rounded-md text-white font-semibold bg-linear-to-r from-indigo-700 via-purple-700 to-gray-900 hover:opacity-95 shadow focus:outline-none focus-visible:border focus-visible:border-indigo-500" x-bind:aria-busy="loading" x-bind:disabled="loading">
                                    <i class="fab fa-twitch text-xl"></i>
                                    <span id="twitch-submit-text" x-text="loading ? '{{ __('twitch.oauth.authorizing') }}' : '{{ __('twitch.login_cta') }}'"></span>
                                    <i class="fas fa-spinner fa-spin ml-2" x-show="loading" x-cloak aria-hidden="true"></i>
                                </button>
                            </div>
                        </form>
                    @endif

                    <p class="mt-6 text-xs text-gray-500">{{ __('twitch.login_privacy_note') }}</p>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
