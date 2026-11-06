<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="phoneUsersTable">

        <thead class="top-text">

        <tr>

            <th>#</th>
            <th>Company Name</th>
            <th>User Login</th>
            <th>User Email</th>
            <th>Package</th>
            <th>Phone Numbers</th>
            <th>State</th>
            <th>City</th>
            <th>Join Date</th>

        </tr>

        </thead>

        <tbody>

        @forelse($userNumberDetail as $userNum)

            <tr>

                <td>

                    {{ $loop->iteration + ($userNumberDetail->firstItem()-1) }}

                </td>

                <td>{{ $userNum->user_nicename }}</td>

                <td>{{ $userNum->user_login }}</td>

                <td>{{ $userNum->user_email }}</td>

                <td>

                    @switch($userNum->package_id)

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

                <td>{{ $userNum->numbers }}</td>

                <td>{{ $userNum->state }}</td>

                <td>{{ $userNum->ratecenter }}</td>

                <td>

                    {{ \Carbon\Carbon::parse($userNum->created_at)->format('d M Y h:i A') }}

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="9" class="text-center">

                    No Phone Numbers Found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $userNumberDetail->links() !!}

    </div>

</div>