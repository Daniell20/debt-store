@extends('layouts.main_layout')
@section('content')
    @include('layouts.users.sidebar')
    <div class="body-wrapper">
        @include('layouts.navbar')

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <span class="ti ti-send"></span> {{ session('success') }}
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title fw-semibold mb-4"> Report </h5>
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{ route('report.post') }}" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="mb-3 col-lg-4">
                                            <label for="merchant" class="form-label">Merchant</label>
                                            <select name="merchant_id" id="merchant_id" class="form-control" required>
                                                <option value="0" selected hidden>-- --</option>
                                                @foreach ($merchants as $merchant)
                                                    <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea name="description" id="description" cols="30" rows="5" class="form-control" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-success"><span class="ti ti-send"></span>
                                                Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
