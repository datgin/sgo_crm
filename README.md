@extends('backend.layouts.app')

@section('content')
    <x-table fileName="employee" />
@endsection

<div id="{{ $tableId }}" class="mb-5">
    <div class="table-wrapper">
        <div class="table-loading-overlay d-none">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead>
                    <tr id="{{ $tableId }}-thead">

                    </tr>
                </thead>

                <tbody class="table-data">
                    <tr>
                        {{-- <td colspan="{{ count($columns) + 3 }}" class="text-center">Đang tải dữ liệu...</td> --}}
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end align-items-center mt-3 pagination-wrapper">
        <ul class="pagination mb-0"></ul>
        <select class="form-select form-select-sm ms-3 rows-per-page" style="width: auto;">
            <option value="10" selected>10 / page</option>
            <option value="20">20 / page</option>
            <option value="50">50 / page</option>
            <option value="100">100 / page</option>
        </select>
    </div>
</div>

@push('scripts')
    <script src="{{ asset("assets/backend/js/columns/$fileName.js") }}"></script>
    <script>
        $(document).ready(function() {
            const tableId = "#{{ $tableId }}";
            const apiUrl = "{{ $apiUrl }}";
            let currentPage = 1;

            const fields = columns.map(col => ({
                dataIndex: col.dataIndex,
                render: col.render ?? null,
                class: col.class ?? '',
            }));

            const colCount = fields.length + 3;

            function renderHeader() {
                const $thead = $(`${tableId}-thead`);
                let html = `
                <th style="width: 3%" class="text-center"><input type="checkbox" class="check-all"></th>
                <th style="width: 5%">STT</th>
            `;
                columns.forEach(col => {
                    html +=
                        `<th style="width: ${col.width ?? 'auto'}" class="${col.class ?? ''}">${col.title}</th>`;
                });
                html += `<th class="text-center" style="width: 100px">Hành động</th>`;
                $thead.html(html);
            }

            function loadData(page = 1, perPage = 10) {
                const loadingRow = `<tr><td colspan="${colCount}" class="text-center">Đang tải...</td></tr>`;
                $(tableId + ' .table-data').html(loadingRow);

                $.ajax({
                    url: `${apiUrl}?page=${page}&per_page=${perPage}`,
                    type: 'GET',
                    beforeSend: function() {
                        $(tableId + ' .table-loading-overlay').removeClass('d-none');
                    },
                    success: function(response) {
                        const rows = response.data.map((item, index) => {
                            const stt = (response.current_page - 1) * response.per_page +
                                index + 1;

                            const cells = fields.map(field => {
                                const value = item[field.dataIndex] ?? '';
                                if (field.render) {
                                    try {
                                        return `<td class="${field.class}">${field.render(item)}</td>`;
                                    } catch (e) {
                                        console.error('Lỗi render:', e);
                                        return `<td class="${field.class}">${value}</td>`;
                                    }
                                }
                                return `<td class="${field.class}">${value}</td>`;
                            }).join('');

                            return `
                            <tr>
                                <td class="text-center"><input type="checkbox" class="row-checkbox" value="${item.key}"></td>
                                <td>${stt}</td>
                                ${cells}
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary btn-edit me-1" data-id="${item.key}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="${item.key}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>`;
                        }).join('');

                        $(tableId + ' .table-data').html(
                            rows ||
                            `<tr><td colspan="${colCount}" class="text-center">Không có dữ liệu</td></tr>`
                        );

                        renderPagination(response.current_page, response.last_page);
                        bindCheckAll();
                    },
                    complete: function() {
                        $(tableId + ' .table-loading-overlay').addClass('d-none');
                    },
                    error: function() {
                        $(tableId + ' .table-data').html(
                            `<tr><td colspan="${colCount}" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>`
                        );
                    }
                });
            }

            function renderPagination(current, last) {
                const $pagination = $(tableId + ' .pagination');
                let pages = '';
                const maxVisible = 5;
                const half = Math.floor(maxVisible / 2);
                const start = Math.max(2, current - half);
                const end = Math.min(last - 1, current + half);

                pages += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${current - 1}"><i class="fas fa-angle-left"></i></a>
                    </li>`;

                pages += `<li class="page-item ${current === 1 ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="1">1</a>
                    </li>`;

                if (start > 2) pages += `<li class="page-item disabled"><span class="page-link">...</span></li>`;

                for (let i = start; i <= end; i++) {
                    pages += `<li class="page-item ${i === current ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
                }

                if (end < last - 1) pages +=
                    `<li class="page-item disabled"><span class="page-link">...</span></li>`;

                if (last > 1) {
                    pages += `<li class="page-item ${current === last ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${last}">${last}</a>
                        </li>`;
                }

                pages += `<li class="page-item ${current === last ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${current + 1}"><i class="fas fa-angle-right"></i></a>
                    </li>`;

                $pagination.html(pages);

                $pagination.find('.page-link').off('click').on('click', function(e) {
                    e.preventDefault();
                    const page = $(this).data('page');
                    const perPage = $(tableId + ' .rows-per-page').val();
                    if (page && page !== currentPage) {
                        currentPage = page;
                        loadData(page, perPage);
                    }
                });
            }

            function bindCheckAll() {
                const $table = $(tableId);
                $table.find('.check-all').off('change').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    $table.find('.row-checkbox').prop('checked', isChecked);
                });

                $table.find('.row-checkbox').off('change').on('change', function() {
                    const total = $table.find('.row-checkbox').length;
                    const checked = $table.find('.row-checkbox:checked').length;
                    $table.find('.check-all').prop('checked', total > 0 && total === checked);
                });
            }

            $(tableId + ' .rows-per-page').on('change', function() {
                const perPage = $(this).val();
                loadData(1, perPage);
            });

            renderHeader();
            loadData();
        });
    </script>
@endpush


public function __construct(
        public ?string $tableId = null,
        public ?string $apiUrl = null,
        public ?string $fileName = null,
    ) {
        // Nếu không truyền apiUrl thì tự dùng URL hiện tại
        $this->tableId = 'table_' . uniqid();
        $this->apiUrl = $apiUrl ?? '/' . request()->path();
    }
