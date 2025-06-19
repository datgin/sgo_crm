@props(['row' => $row, 'view' => false, 'edit' => true, 'delete' => true])

@if ($edit)

    @php
        $module = request()->segment(1);
    @endphp

    <a class="btn btn-sm btn-warning btn-edit" data-id="{{ $row->id }}" href="/{{ $module }}/save/{{ $row->id }}">
        <i class="fas fa-edit"></i>
    </a>
@endif

@if ($view)
    <a class="btn btn-sm btn-primary" href="#" data-id="{{ $row->id }}">
        <i class="fas fa-eye"></i>
    </a>
@endif

@if ($delete)
    <a class="btn btn-sm btn-danger btn-delete" data-id="{{ $row->id }}">
        <i class="fas fa-trash"></i>
    </a>
@endif
