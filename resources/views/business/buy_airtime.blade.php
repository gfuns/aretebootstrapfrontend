@extends('business.layouts.app')

@section('content')
@section('title', env('APP_NAME') . ' | Buy Airtime')
<style>
    /* Style your cards as needed */
    .selected-card {
        border: 1px solid #21a184;
    }
</style>

<!-- Content -->
<section class="container-fluid p-4">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <!-- Page header -->
            <div class="border-bottom pb-3 mb-3">
                <div class="mb-2 mb-lg-0">
                    <h1 class="mb-1 h2 fw-bold">Buy Airtime</h1>
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('business.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">Utilities</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Buy Airtime</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <h4>Please select your preferred provider to get started</h4>
        @foreach ($airtimeProviders as $index => $ap)
            <div class="col-lg-3 col-md-12 col-6 mb-3" style="cursor: pointer">
                <div id="{{ $ap->biller }}" class="card card-hover {{ $index === 0 ? 'selected-card' : '' }}" onclick="handleCardClick(this)">
                    <div class="d-flex justify-content-between align-items-center p-4 text-truncate">
                        <div class="d-flex">
                            <img src="{{ $ap->biller_logo }}" alt="" class="avatar-md">
                            <div class="ms-3 pt-1">
                                <h4 class="mb-1">
                                    {{ $ap->biller }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <p>&nbsp;</p>
        <h4>Amount & Phone Number to recharge</h4>
        <p>Enter the phone number to recharge and the amount you would like to recharge.</p>
    </div>
    <div class="">
        <!-- card body -->
        <div class="card-body">
            <!-- form -->
            <form method="post" action="{{ route('business.airtimePurchasePreview') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- form group -->
                    <div class="mb-3 col-md-12 col-12">
                        <div class="col-md-6 col-12">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                                class="form-control @error('phone_number') is-invalid @enderror"
                                placeholder="Enter Phone Number" required>
                            @error('phone_number')
                                <span class="" role="alert">
                                    <strong style="color: #b02a37; font-size:12px">{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 col-md-12 col-12">
                        <div class="col-md-6 col-12">
                            <label class="form-label">Recharge Amount <span class="text-danger">*</span></label>
                            <input type="text" name="recharge_amount" value="{{ old('recharge_amount') }}"
                                class="form-control @error('recharge_amount') is-invalid @enderror"
                                placeholder="Enter Recharge Amount" required>
                            @error('recharge_amount')
                                <span class="" role="alert">
                                    <strong style="color: #b02a37; font-size:12px">{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <input id="biller" name="biller" class="form-control" type="hidden" value="MTN" required/>

                    <div class="col-12">
                        <button class="btn btn-primary" type="button"
                            onClick="this.disabled=true; this.innerHTML='Submiting request, please wait...';this.form.submit();">Proceed &nbsp;<i class="bi bi-arrow-right"></i></button>

                    </div>
                </div>
            </form>
        </div>
    </div>


</section>

<script type="text/javascript">
    document.getElementById("navBillPayments").classList.add('show');
    document.getElementById("airtime").classList.add('active');
</script>
@endsection


@section('customjs')

<script type="text/javascript">
   function handleCardClick(card){
    var allCards = document.querySelectorAll('.card');
        allCards.forEach(function (c) {
            c.classList.remove('selected-card');
        });

    card.classList.add('selected-card');
    var biller = card.id;
    document.getElementById('biller').value=biller;
   }
</script>

@endsection
