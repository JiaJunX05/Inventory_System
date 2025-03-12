@extends("layouts.app")

@section("title", "View Product")
@section("content")

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="row g-0">

                    <div class="col-md-5 d-flex align-items-center justify-content-center p-3 bg-light">
                        <img src="{{ asset('assets/' . $product->feature) }}"
                            alt="{{ $product->name }}" class="img-fluid" id="preview-image" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                    </div>

                    <div class="col-md-7">
                        <div class="card-body p-4">
                            <!-- Form Title -->
                            <h2 class="text-primary text-center mb-3">View Product</h2>
                            <p class="text-muted text-center">View and manage your product here.</p><hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-bold">Product Name:</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_name" class="form-label fw-bold">Product Category:</label>
                                        <input type="text" class="form-control" id="category_name" name="category_name"
                                            value="{{ strtoupper($product->category->category_name ?? 'No Category') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Product Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="1" readonly>{{ $product->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label fw-bold">Product Price:</label>
                                <input type="text" class="form-control" id="price" name="price" value="RM {{ $product->price }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-bold">Product Quantity:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="quantity" name="quantity" min="1" value="{{ $product->quantity }} Units" readonly>
                                </div>
                            </div>

                            @if ($product->images)
                                <div class="row mt-3 d-flex flex-wrap justify-content-center">
                                    @foreach ($product->images as $image)
                                        <div class="col-sm-12 col-md-6 col-lg-2 m-2 d-flex justify-content-center align-items-center">
                                            <div class="position-relative">
                                                <img src="{{ asset('assets/' . $image->image) }}" alt="Image" class="img-fluid" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
