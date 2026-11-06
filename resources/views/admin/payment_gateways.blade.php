@extends('admin.layouts.layout')
@section('css')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<style>
    .toggle.btn.btn-default.off {
        border: 1px solid #cec8c8;
    }
    #profile_card {
        background-color: #f3f7f96b;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/"><i class="feather icon-home"></i></a>
                    </li>
                    <li class="breadcrumb-item">Payment Gateways</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="admin-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="card">

                        {{-- Laravel session message --}}
                        @if($message = Session::get('success'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif

                        {{-- AJAX success alert --}}
                        <div id="ajaxAlert" class="alert alert-success" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <span id="ajaxAlertMsg"></span>
                        </div>

                        <div class="card-header">
                            <h3>Payment Gateways</h3>
                            <!-- <a class="btn btn-primary float-right add-gateway"
                               data-toggle="modal" data-target="#gateway_modal">
                                <i class="feather icon-plus-circle"></i> Add
                            </a> -->
                        </div>

                        <div class="card-block" id="profile_card">
                            <table class="table table-default" id="gateway_table">
                                <thead>
                                    <th>No.</th>
                                    <th>Gateway Name</th>
                                    <th>Status</th>
                                </thead>

                                <tbody>
                                @php $count = 0; @endphp
                                @foreach($paymentGateways as $gateway)
                                <tr>
                                    <td>{{ ++$count }}</td>
                                    <td>{{ $gateway->gateway_name }}</td>

                                    <td>
                                        <input type="checkbox"
                                               class="gateway-toggle"
                                               data-id="{{ $gateway->id }}"
                                               data-toggle="toggle"
                                               data-on="Active"
                                               data-off="Inactive"
                                               data-onstyle="success"
                                               @if($gateway->is_active) checked @endif>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ADD/EDIT MODAL --}}
<!-- <div id="gateway_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Add Gateway</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form method="post" action="" id="gateway_form">
                    @csrf
                    <label>Gateway Name</label>
                    <input class="form-control" type="text" name="gateway_name" id="gateway_name" required>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-info">Save</button>
                        <button type="button" class="btn btn-default ml-2" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div> -->
@endsection

@section('script')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script>
$('#gateway_table').DataTable({
    aaSorting: [],
    responsive: true
});

// Add Gateway
$(".add-gateway").click(function () {
    $('#gateway_modal .modal-title').text('Add Payment Gateway');
    $('#gateway_form').attr('action', '/admin/payment-gateway/new');
    $('#gateway_name').val('');
});

// Edit Gateway
$(".edit-gateway").click(function () {
    let id = $(this).data('id');
    let name = $(this).data('name');

    $('#gateway_modal .modal-title').text('Update Payment Gateway');
    $('#gateway_form').attr('action', "/admin/payment-gateway/update/" + id);
    $('#gateway_name').val(name);
});

// -----------------------------
// Toggle - Only one active at a time
// -----------------------------
let isUpdatingToggle = false; // flag to prevent loop

$(".gateway-toggle").change(function () {

    if(isUpdatingToggle) return; // ignore programmatic changes

    let id = $(this).data("id");

    isUpdatingToggle = true; // block further triggers

    $.post("/admin/payment-gateway/activate-only/" + id, {
        _token: "{{ csrf_token() }}"
    }, function(response) {

        // Turn OFF all toggles silently
        $(".gateway-toggle").each(function() {
            $(this).bootstrapToggle("off", true);
        });

        // Turn ON selected toggle silently
        $("input[data-id='" + id + "']").bootstrapToggle("on", true);

        // Show success alert
        $("#ajaxAlertMsg").text(response.message);
        $("#ajaxAlert").fadeIn();

        // Hide after 3 seconds
        setTimeout(function(){
            $("#ajaxAlert").fadeOut();
        }, 3000);

        isUpdatingToggle = false; // re-enable change events
    });

});
</script>
@endsection