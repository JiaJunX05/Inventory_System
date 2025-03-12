@extends("admin.layouts.app")

@section("title", "Admin Panel")
@section("content")

<div class="container text-center mt-5">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h3 class="text-md-start">Product List Management</h3><hr>

    <div class="mb-3">
        <div class="text-md-end">
            <div class="card shadow-sm" style="all: unset;">
                <div class="card-body p-3">
                    <form class="d-flex gap-3 align-items-center" role="search" id="search-form">
                        <div class="input-group flex-grow-1">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input class="form-control border-start-0" type="search" placeholder="Search by Product Name..." aria-label="Search" id="search-input">
                            <button class="btn btn-outline-primary" type="submit" style="width: 150px;">Search</button>
                        </div>

                        <select class="form-select" id="category_id" name="category_id" style="width: 250px;">
                            <option selected value="">Select a Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ strtoupper($category->category_name) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 卡片列表 -->
    <div id="product-card-container" class="row g-4"></div>

    <div id="no-results" class="text-center py-3" style="display: none;">No results found.</div>

    <!-- Pagination -->
    <nav aria-label="Page navigation example" class="d-flex justify-content-center mt-3">
        <ul id="pagination" class="pagination"></ul>
    </nav>
</div>

<script>
    $(document).ready(function() {
        let currentSearch = '';
        let currentCategory = '';

        // 初始加载表格
        loadTable(1);

        // 搜索表单提交事件
        $('#search-form').on('submit', function(e) {
            e.preventDefault();
        });


        // 表单提交事件
        $('#search-form').on('input', function(e) {
            e.preventDefault();
            currentSearch = $('#search-input').val();
            currentCategory = $('#category_id').val();
            loadTable(1);
        });

        // 选择框变化时触发搜索
        $('#category_id').on('change', function() {
            currentSearch = $('#search-input').val();
            currentCategory = $('#category_id').val();
            loadTable(1);
        });

        function loadTable(page) {
            $.ajax({
                url: "{{ route('admin.dashboard') }}",
                type: 'GET',
                data: {
                    page: page,
                    search: currentSearch,
                    category_id: currentCategory,
                    length: 10
                },
                success: function(response) {
                    renderCards(response.data);
                    renderPagination(response.current_page, response.last_page);

                    // 显示/隐藏无结果提示
                    $('#no-results').toggle(response.data.length === 0);
                },
                error: function(xhr) {
                    console.error('Error loading table:', xhr);
                }
            });
        }

        function renderCards(data) {
            let container = $('#product-card-container');
            container.empty();

            data.forEach(product => {
                container.append(`
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                        <div class="card shadow-sm border-0 w-100">
                            <div class="card-header bg-light d-flex flex-column align-items-start p-3" style="border-bottom: 2px solid #007bff;">
                                <h5 class="card-title mb-1" style="font-weight: bold; color: #333;">${product.name}</h5>
                            </div>

                            <div class="position-relative text-center d-flex align-items-center justify-content-center mb-3">
                                <img src="${product.feature ? '{{ asset('assets/') }}/' + product.feature : '{{ asset('assets/default.jpg') }}'}"
                                    alt="Product Feature" class="img-fluid mt-3" style="width: 150px; object-fit: cover;">
                            </div>

                            <div class="card-body">
                                <p class="card-text text-muted" style="font-size: 14px; line-height: 1.6; text-align: justify;">
                                    ${product.description ? product.description.substring(0, 30) + '...' : 'No description'}
                                </p>

                                <div class="card-footer text-body-secondary mt-3 p-0">
                                    <a href="{{ route('product.view', '') }}/${product.id}" class="btn btn-success w-100" style="border-radius: 0 0 8px 8px;">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        function renderPagination(currentPage, lastPage) {
            let pagination = $('#pagination');
            pagination.empty();

            // 上一页
            if (currentPage > 1) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                    </li>
                `);
            }

            // 页码
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
                pagination.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }

            // 下一页
            if (currentPage < lastPage) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                    </li>
                `);
            }

            // 分页点击事件
            $('.page-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                loadTable(page);
            });
        }
    });
</script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endsection
