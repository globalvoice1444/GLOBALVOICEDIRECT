<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="deactivateUserTable">

        <thead class="top-text">

            <tr>
                <th>#</th>
                <th>Company Name</th>
                <th>User Login</th>
                <th>User Email</th>
                <th>User URL</th>
                <th>Display Name</th>
                <th>Action</th>
            </tr>

        </thead>

        <tbody>

        @forelse($deActiveUserDetail as $deActUser)

            <tr>

                <td>
                    {{ $loop->iteration + ($deActiveUserDetail->firstItem()-1) }}
                </td>

                <td>{{ $deActUser->user_nicename }}</td>

                <td>{{ $deActUser->user_login }}</td>

                <td>{{ $deActUser->user_email }}</td>

                <td>{{ $deActUser->user_url }}</td>

                <td>{{ $deActUser->display_name }}</td>

                <td>

                    <a href="{{ url('admin/act_activateUser',['uid'=>$deActUser->ID]) }}"
                       class="btn btn-success"
                       onclick="return confirm('Are you sure you want to activate this user?')">

                        Activate User

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7" class="text-center">

                    No deactivated users found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $deActiveUserDetail->links() !!}

    </div>

</div>