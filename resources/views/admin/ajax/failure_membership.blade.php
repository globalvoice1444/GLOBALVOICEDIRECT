<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="membershipFailureTable">

        <thead class="top-text">

        <tr>

            <th>#</th>
            <th>Display Name</th>
            <th>Company Name</th>
            <th>Email</th>
            <th>Phone #</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Phone Package</th>
            <th>Phone Numbers</th>
            <th>Phone State</th>
            <th>Phone City</th>
            <th>Reason</th>
            <th>Join Date</th>

        </tr>

        </thead>

        <tbody>

        @forelse($failuremembershipDetail as $failNum)

            <tr>

                <td>{{ $loop->iteration + ($failuremembershipDetail->firstItem()-1) }}</td>

                <td>{{ $failNum->display_name }}</td>

                <td>{{ $failNum->company_name }}</td>

                <td>{{ $failNum->email }}</td>

                <td>{{ $failNum->phone }}</td>

                <td>{{ $failNum->city }}</td>

                <td>{{ $failNum->state }}</td>

                <td>{{ $failNum->zip }}</td>

                <td>

                    @switch($failNum->phone_pkg)

                        @case(0)
                            SMB Standard Phone Plan Virtual Office Plan ($24.95 USD)
                            @break

                        @case(1)
                            Small Business Premium ($49.95 USD)
                            @break

                        @case(2)
                            Enterprise Virtual Office Plan ($84.95 USD)
                            @break

                        @default
                            N/A

                    @endswitch

                </td>

                <td>{{ $failNum->phone_number }}</td>

                <td>{{ $failNum->phone_state }}</td>

                <td>{{ $failNum->phone_ratecenter }}</td>

                <td>{{ $failNum->reason }}</td>

                <td>{{ \Carbon\Carbon::parse($failNum->created_at)->format('d M Y h:i A') }}</td>

            </tr>

        @empty

            <tr>

                <td colspan="14" class="text-center">

                    No Failure Membership Records Found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $failuremembershipDetail->links() !!}

    </div>

</div>