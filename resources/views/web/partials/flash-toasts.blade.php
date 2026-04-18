<div class="pointer-events-none fixed right-4 top-20 z-60 flex w-full max-w-sm flex-col gap-2" id="toast-root">
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
