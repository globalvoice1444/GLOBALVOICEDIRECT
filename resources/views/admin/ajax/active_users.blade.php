<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="activeUserTable">

        <thead>

        <tr>

            <th>#</th>
            <th>Company Name</th>
            <th>User Login</th>
            <th>User Email</th>
            <th>User Phone</th>
            <th>User URL</th>
            <th>Display Name</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Joining Date</th>
            <th>Action</th>

        </tr>

        </thead>

        <tbody>

        @forelse($activeUserDetail as $actUser)

            <tr>

                <td>
                    {{ $loop->iteration + ($activeUserDetail->firstItem()-1) }}
                </td>

                <td>{{ $actUser->user_nicename }}</td>

                <td>{{ $actUser->user_login }}</td>

                <td>{{ $actUser->user_email }}</td>

                <td>{{ $actUser->phone }}</td>

                <td>{{ $actUser->user_url }}</td>

                <td>{{ $actUser->display_name }}</td>

                <td>{{ $actUser->address }}</td>

                <td>{{ $actUser->city }}</td>

                <td>{{ $actUser->state }}</td>

                <td>{{ $actUser->zip }}</td>

                <td>
                    {{ \Carbon\Carbon::parse($actUser->user_registered)->format('d M Y h:i A') }}
                </td>

                <td>

                    <a href="{{ url('admin/act_removeUser',['uid'=>$actUser->ID]) }}"
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure?')">

                        Deactivate User

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="13" class="text-center">

                    No Record Found

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $activeUserDetail->links() !!}

    </div>

</div>