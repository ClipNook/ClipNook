<x-layouts.app :title="__('ui.auth.sign_in_with_twitch')">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <!-- Info / Privacy -->
            <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm overflow-hidden p-6">
                <header class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-md bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-2xl" aria-hidden="true">
                        <i class="fab fa-twitch text-[#6441A5]"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('twitch.login_title') }}</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ __('twitch.login_subtitle') }}</p>
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

                    <ul class="grid grid-cols-1 gap-2 list-none pl-0">
                        <li class="flex gap-3 items-start">
                            <span class="mt-0.5 text-green-600 dark:text-green-400">✓</span>
                            <span>{{ __('twitch.privacy_item_tokens', ['days' => $privacy['data_retention'] ?? config('services.twitch.privacy.data_retention')]) }}</span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="mt-0.5 text-green-600 dark:text-green-400">✓</span>
                            <span>{{ __('twitch.privacy_item_ip') }}: <strong>{{ ($privacy['anonymize_ip'] ?? config('services.twitch.privacy.anonymize_ip')) ? __('twitch.privacy_yes') : __('twitch.privacy_no') }}</strong></span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="mt-0.5 text-green-600 dark:text-green-400">✓</span>
                            <span>{{ __('twitch.privacy_item_logging') }}: <strong>{{ ($privacy['log_requests'] ?? config('services.twitch.privacy.log_requests')) ? __('twitch.privacy_yes') : __('twitch.privacy_no') }}</strong></span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="mt-0.5 text-green-600 dark:text-green-400">✓</span>
                            <span>{{ __('twitch.privacy.avatar_download') }}</span>
                        </li>
                    </ul>

                    <p class="mt-2 text-sm">{{ __('twitch.login_privacy_more') }} <button type="button" class="underline font-semibold text-gray-900 dark:text-white" id="open-privacy">{{ __('ui.footer.privacy') }}</button></p>

                    <p class="mt-2 text-xs text-gray-500">{{ __('twitch.login_privacy_note') }}</p>
                </div>

                <footer class="mt-6 text-xs text-gray-500">
                    <p class="flex items-center gap-2"><i class="fas fa-shield-alt"></i> {{ __('twitch.privacy.data_usage') ?? __('twitch.privacy.data_usage') }}</p>
                </footer>
            </section>

            <!-- Auth / CTA -->
            <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm overflow-hidden p-6">
                <div class="flex flex-col h-full">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('twitch.login_cta') }}</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $twitch_scopes ? $twitch_scopes : '' }}</p>
                    </div>

                    @if(!empty($twitch_missing))
                        <div class="p-4 rounded bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800">
                            <p class="font-semibold text-yellow-900 dark:text-yellow-200">{{ __('twitch.login_need_config') }}</p>
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">{{ __('twitch.login_config_doc') }}</p>
                        </div>
                    @else
                        <form method="GET" action="{{ route('auth.twitch.redirect') }}" id="twitch-login-form" class="mt-2">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <input id="consent" name="consent" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" required>
                                    <label for="consent" class="text-sm text-gray-700 dark:text-gray-300">{{ __('twitch.privacy.consent_required') }}</label>
                                </div>

                                <div class="flex items-center gap-3">
                                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="remember" class="text-sm text-gray-700 dark:text-gray-300">{{ __('ui.auth.remember_me') }}</label>
                                </div>

                                <button type="submit" id="twitch-submit" class="w-full inline-flex items-center justify-center gap-3 px-4 py-3 rounded-md text-white font-semibold bg-[#6441A5] hover:bg-[#4b2f86] transition" aria-live="polite">
                                    <i class="fab fa-twitch"></i>
                                    <span id="twitch-submit-text">{{ __('twitch.login_cta') }}</span>
                                    <svg id="twitch-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 11-8 8z"></path></svg>
                                </button>
                            </div>
                        </form>
                    @endif

                    <p class="mt-6 text-xs text-gray-500">{{ __('twitch.login_privacy_note') }}</p>
                </div>
            </section>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div id="privacy-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" aria-hidden="true">
        <div class="absolute inset-0 bg-black opacity-50" id="modal-overlay"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-2xl w-full p-6 z-10">
            <div class="flex justify-between items-start">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.footer.privacy') }}</h3>
                <button id="close-privacy" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">✕</button>
            </div>
            <div class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                <p>{{ __('twitch.privacy.data_usage') }}</p>
                <p class="mt-2">{{ __('twitch.privacy.data_retention', ['days' => $privacy['data_retention'] ?? config('services.twitch.privacy.data_retention')]) ?? __('twitch.privacy.data_retention') }}</p>
                <p class="mt-2">{{ __('twitch.privacy.avatar_download') }}</p>
                <p class="mt-2">{{ __('twitch.privacy.avatar_storage', ['days' => $privacy['data_retention'] ?? config('services.twitch.privacy.data_retention')]) }}</p>
                <p class="mt-2 text-xs text-gray-500">{{ __('twitch.login_privacy_note') }}</p>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const form = document.getElementById('twitch-login-form');
            const consent = document.getElementById('consent');
            const submit = document.getElementById('twitch-submit');
            const spinner = document.getElementById('twitch-spinner');
            const submitText = document.getElementById('twitch-submit-text');

            if (form) {
                form.addEventListener('submit', function(e){
                    if (!consent.checked) {
                        e.preventDefault();
                        consent.focus();
                        return;
                    }
                    // show spinner
                    spinner.classList.remove('hidden');
                    submitText.textContent = "{{ __('twitch.oauth.authorizing') }}";
                });
            }

            // Privacy modal
            const openBtn = document.getElementById('open-privacy');
            const modal = document.getElementById('privacy-modal');
            const overlay = document.getElementById('modal-overlay');
            const closeBtn = document.getElementById('close-privacy');

            function openModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); modal.setAttribute('aria-hidden', 'false'); }
            function closeModal(){ modal.classList.remove('flex'); modal.classList.add('hidden'); modal.setAttribute('aria-hidden', 'true'); }

            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (overlay) overlay.addEventListener('click', closeModal);
            document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
        })();
    </script>
</x-layouts.app>
