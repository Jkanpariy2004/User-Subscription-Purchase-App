@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="row">
            @foreach ($plans as $plan)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            {{ $plan->name }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">${{ $plan->amount }} Charge</h5>
                            <p class="card-text">Trial Days: {{ $plan->trial_days }}</p>
                            <button class="btn btn-primary confirmationBtn" data-bs-toggle="modal"
                                data-bs-target="#confirmationModal" data-id="{{ $plan->id }}">Subscribe</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmationModalLabel">...</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="confirmation-data text-center">
                        <i class="fa fa-spinner fa-spin fs-1"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary continueBuyPlan">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="stripeModal" tabindex="-1" aria-labelledby="stripeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="stripeModalLabel">Buy Subscription</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="planId" id="planId">

                    {{-- Stripe Card Element --}}
                    <label for="card-element" class="form-label">Enter your card details:</label>
                    <div id="card-element"></div>

                    {{-- Show Card Errors --}}
                    <div id="card-errors"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="buyPlanSubmitBtn">Buy Plan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        $('.confirmationBtn').click(function() {
            $('#confirmationModalLabel').text('...');
            $('.confirmation-data').html('<i class="fa fa-spinner fa-spin fs-1"></i>');
            var planId = $(this).data('id');

            $('#planId').val(planId);

            $.ajax({
                type: "post",
                url: "{{ route('getPlanDetails') }}",
                data: {
                    id: planId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;

                        var html = '';
                        $('#confirmationModalLabel').text(data.name + '($' + data.amount + ')');
                        html += `<p>` + response.message + `</p>`;

                        $('.confirmation-data').html(html);

                    } else {
                        alert("Error");
                    }
                }
            });

            $('.continueBuyPlan').click(function() {
                $('#confirmationModal').modal('hide');
                $('#stripeModal').modal('show');
            });
        });

        if (window.Stripe) {
            var stripe = Stripe("{{ env('STRIPE_PUBLIC_KEY') }}");

            var elements = stripe.elements();

            var card = elements.create('card', {
                hidePostalCode: true
            });

            card.mount('#card-element');

            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');

                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            var submitButton = document.getElementById('buyPlanSubmitBtn');

            submitButton.addEventListener('click', function(ev) {
                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message
                    } else {
                        createSubscription(result.token);
                    }
                });
            });
        }

        function createSubscription(token) {
            var pla_id = $('#planId').val();

            $.ajax({
                type: "POST",
                url: "{{ route('createSubscription') }}",
                data: {
                    data: token,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        console.log(response);
                    } else {
                        alert("Error");
                    }
                }
            });
        }
    </script>
@endpush
