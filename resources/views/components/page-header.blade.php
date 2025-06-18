<div class="d-flex justify-content-between align-items-center mb-4 p-3 border shadow-sm bg-white rounded">
    <div class="d-flex align-items-center">
        <a href="{{ url()->previous() }}" class="btn btn-light me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0">{{ $title }}</h1>
    </div>
    <div class="d-flex gap-2">
        {{ $slot }}
    </div>
</div>
