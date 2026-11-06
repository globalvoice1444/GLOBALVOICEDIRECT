<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="affiliateWithdrawTable">

        <thead class="top-text">

            <tr>

                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Withdraw Balance</th>
                <th>Status</th>
                <th>Actions</th>

            </tr>

        </thead>

        <tbody>

        @forelse($withdrawRequests as $request)

            <tr>

                <td>

                    {{ $loop->iteration + ($withdrawRequests->firstItem()-1) }}

                </td>

                <td>{{ $request->name }}</td>

                <td>{{ $request->email }}</td>

                <td>{{ $request->phone }}</td>

                <td>${{ number_format($request->balance,2) }}</td>

                <td>

                    @if($request->status)

                        <span class="badge badge-success">

                            Transferred

                        </span>

                    @else

                        <span class="badge badge-warning">

                            Pending

                        </span>

                    @endif

                </td>

                <td>

                    @if(!$request->status)

                        <a href="{{ url('/admin/affiliate/withdraw/approved/'.$request->id) }}"
                           class="btn btn-primary">

                            Transfer

                        </a>

                    @else

                        <span class="badge badge-success">

                            Transferred

                        </span>

                    @endif

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7" class="text-center">

                    No Withdraw Requests Found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $withdrawRequests->links() !!}

    </div>

</div>