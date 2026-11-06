<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="memberWithdrawTable">

        <thead class="top-text">
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Referral</th>
                <th>Email</th>
                <th>Reward</th>
                <th>Shopping</th>
                <th>Req Date</th>
                <th>Reward Status</th>
                <th>Req Type</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        @forelse($members_withdraw_request as $request)

            <tr>

                <td>{{ $members_withdraw_request->firstItem() + $loop->index }}</td>

                <td>{{ $request->member_id }}</td>

                <td>
                    @if($request->referral_id==0)
                        <span class="badge badge-success">Bonus</span>
                    @else
                        {{ $request->referral_id }}
                    @endif
                </td>

                <td>{{ $request->email }}</td>

                <td>${{ $request->reward_amount }}</td>

                <td>${{ $request->shopping_amount }}</td>

                <td>{{ $request->req_date }}</td>

                <td>

                    @if($request->reward_status==0)

                        <span class="badge badge-success">
                            Bonus
                        </span>

                    @else

                        <span class="badge badge-danger">
                            Referral Reward
                        </span>

                    @endif

                </td>

                <td>

                    @if($request->req_type==0)

                        <span class="badge badge-success">
                            Pay Bill
                        </span>

                    @else

                        <span class="badge badge-info">
                            Transfer
                        </span>

                    @endif

                </td>

                <td>

                    @if(!$request->req_status)

                        <form method="POST" action="/admin/withdraw/approved">

                            @csrf

                            <input type="hidden" name="request_id" value="{{ $request->id }}">

                            <input type="hidden"
                                   name="request_type"
                                   value="{{ $request->req_type==0 ? 'pay_bill':'transfer' }}">

                            <input type="hidden"
                                   name="reward_status"
                                   value="{{ $request->reward_status }}">

                            <button
                                type="submit"
                                class="btn {{ $request->req_type==0 ? 'btn-info':'btn-success' }}">

                                {{ $request->req_type==0 ? 'Pay Bill':'Transfer' }}

                            </button>

                        </form>

                    @else

                        <span class="badge badge-success">

                            Approved

                        </span>

                    @endif

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="10" class="text-center">

                    No withdraw requests found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $members_withdraw_request->links() !!}

    </div>

</div>