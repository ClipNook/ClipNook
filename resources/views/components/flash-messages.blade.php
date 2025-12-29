<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6" role="region" aria-live="polite">
    <div class="space-y-3">
        @foreach(['success', 'error', 'warning', 'info'] as $type)
            @if(session($type))
                <div @class([
                    'flex items-start gap-3 p-4 rounded-lg border',
                    'bg-green-50 border-green-200 text-green-900 dark:bg-green-950 dark:border-green-800 dark:text-green-100' => $type === 'success',
                    'bg-red-50 border-red-200 text-red-900 dark:bg-red-950 dark:border-red-800 dark:text-red-100' => $type === 'error',
                    'bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-950 dark:border-amber-800 dark:text-amber-100' => $type === 'warning',
                    'bg-blue-50 border-blue-200 text-blue-900 dark:bg-blue-950 dark:border-blue-800 dark:text-blue-100' => $type === 'info',
                ]) role="{{ $type === 'error' ? 'alert' : 'status' }}">
                    <i @class([
                        'fas text-lg mt-0.5 shrink-0',
                        'fa-check-circle text-green-600 dark:text-green-400' => $type === 'success',
                        'fa-exclamation-circle text-red-600 dark:text-red-400' => $type === 'error',
                        'fa-exclamation-triangle text-amber-600 dark:text-amber-400' => $type === 'warning',
                        'fa-info-circle text-blue-600 dark:text-blue-400' => $type === 'info',
                    ])></i>
                    <p class="text-sm font-semibold flex-1">
                        {{ session($type) }}
                    </p>
                    <button type="button" 
                            @click="this.parentElement.remove()"
                            class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                            aria-label="{{ __('ui.close') }}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
        @endforeach
    </div>
</div>
