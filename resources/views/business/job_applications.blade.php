@extends('business.layouts.app')

@section('content')
@section('title', env('APP_NAME') . ' | Job Applications')


<!-- Container fluid -->
<section class="container-fluid p-4">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <!-- Page header -->
            <div class="border-bottom pb-3 mb-3 d-lg-flex align-items-center justify-content-between">
                <div class="mb-2 mb-lg-0">
                    <h1 class="mb-0 h2 fw-bold">Job Applications</h1>
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('business.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Job Applications</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <!-- Card -->
            <div class="card rounded-3">
                <!-- Card Header -->
                <form id="form" name="form" method="GET">
                    <div class="p-4 row gx-3">
                        <!-- Form -->
                        <div class="col-12 col-lg-9 mb-3 mb-lg-0">
                            <!-- search -->

                            <div class="d-flex align-items-center">
                                <span class="position-absolute ps-3 search-icon">
                                    <i class="fe fe-search"></i>
                                </span>
                                <!-- input -->
                                <input name="search" type="search" class="form-control ps-6"
                                    placeholder="Search for Applications......" value="{{ $search }}">
                            </div>

                        </div>
                        <div class="col-6 col-lg-3">
                            <!-- form select -->
                            <select id="status" name="status" class="form-select" onChange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Pending" @if ($status == 'draft') selected @endif>Pending
                                </option>
                                <option value="Hired" @if ($status == 'published') selected @endif>Hired
                                </option>
                                <option value="Archived" @if ($status == 'open') selected @endif>Archived
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
                <div>
                    <div class="tab-content" id="tabContent">
                        <!-- Tab -->
                        <div class="tab-pane fade show active" id="all-orders" role="tabpanel"
                            aria-labelledby="all-orders-tab">
                            <div class="table-responsive">
                                <!-- Table -->
                                <table class="table mb-0 table-hover table-centered table-with-checkbox">
                                    <!-- Table Head -->
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th class="text-nowrap">Job Title</th>
                                            <th class="text-nowrap">Artisan Name</th>
                                            <th class="text-nowrap">Status</th>
                                            <th class="text-nowrap">Application Date</th>
                                            <th class="text-nowrap"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Table body -->
                                        @foreach ($applications as $job)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                 <td>{{ $job->jobListing->job_title }}</td>
                                                <td>{{ $job->artisan->customer->first_name." ".$job->artisan->customer->last_name }}</td>
                                                <td class="text-nowrap">
                                                    @if ($job->hiring_status == 'Hired')
                                                        <span class="badge text-success bg-light-success">
                                                            {{ ucwords($job->hiring_status) }}</span>
                                                    @elseif($job->hiring_status == 'Pending')
                                                        <span class="badge text-primary bg-light-primary">
                                                            {{ ucwords($job->hiring_status) }}</span>
                                                    @else
                                                        <span class="badge text-danger bg-light-danger">
                                                            {{ ucwords($job->hiring_status) }}</span>
                                                    @endif
                                                </td>

                                                <td class="text-nowrap">{{ date_format($job->created_at, 'd M, Y') }}
                                                </td>


                                                <td>
                                                    <div class="dropdown dropstart">
                                                        <a class="btn-icon btn btn-ghost btn-sm rounded-circle"
                                                            href="#" role="button" id="Dropdown1"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="fe fe-more-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="Dropdown1">
                                                            <span class="dropdown-header">Settings</span>
                                                            <a class="dropdown-item"
                                                                href="{{ route('business.jobDetails', [$job->id]) }}">
                                                                <i class="fe fe-eye dropdown-item-icon"></i>
                                                                View Details
                                                            </a>
                                                            <a class="dropdown-item"
                                                                href="{{ route('business.jobDetails', [$job->id]) }}">
                                                                <i class="fe fe-eye dropdown-item-icon"></i>
                                                                Archive Application
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if (count($applications) < 1)
                                            <tr>
                                                <td colspan="9">
                                                    <center>No Record Found</center>
                                                </td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>

                            @if (count($applications) > 0 && $marker != null)
                                <div class="card-footer">
                                    <div class="row g-2 pt-3 ms-4 me-4">
                                        <div class="col-md-9">Showing {{ $marker['begin'] }} to {{ $marker['end'] }}
                                            of
                                            {{ number_format($lastRecord) }} Records</div>

                                        <div class="col-md-3">{{ $applications->appends(request()->input())->links() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>
                </div>
                <!-- Card Footer -->

            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    document.getElementById("applications").classList.add('active');
</script>

@endsection