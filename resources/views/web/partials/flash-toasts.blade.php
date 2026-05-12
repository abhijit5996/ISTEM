<noscript>
    <div class="pointer-events-none fixed right-4 top-20 z-60 flex w-full max-w-sm flex-col gap-2" id="toast-root-noscript">
        @if (session('success'))
            <div class="toast toast-success" data-toast>{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="toast toast-error" data-toast>{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="toast toast-error" data-toast>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</noscript>

<script>
    // Dispatch push-toast events for client-side rendering (works with app.js push-toast listener)
    document.addEventListener('DOMContentLoaded', function() {
        try {
            @if (session('success'))
                window.dispatchEvent(new CustomEvent('push-toast', { detail: { message: {!! json_encode(session('success')) !!}, type: 'success' } }));
            @endif

            @if (session('error'))
                window.dispatchEvent(new CustomEvent('push-toast', { detail: { message: {!! json_encode(session('error')) !!}, type: 'error' } }));
            @endif

            @if ($errors->any())
                const errs = {!! json_encode($errors->all()) !!};
                // join messages for a single toast; keep short
                window.dispatchEvent(new CustomEvent('push-toast', { detail: { message: errs.join('\n'), type: 'error' } }));
            @endif
        } catch (e) {
            // no-op; if JS disabled, noscript fallback shows toasts
        }
    });
</script>
