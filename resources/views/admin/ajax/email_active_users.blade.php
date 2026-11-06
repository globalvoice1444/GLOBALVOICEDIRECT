<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="emailActiveUserTable">

        <thead class="top-text">

            <tr>
                <th>#</th>
                <th>Company Name</th>
                <th>User Login</th>
                <th>User Email</th>
                <th>Display Name</th>
                <th>Join Date</th>
                <th>Action</th>
            </tr>

        </thead>

        <tbody>

        @forelse($emailActiveUsers as $userEmail)

            <tr>

                <td>
                    {{ $loop->iteration + ($emailActiveUsers->firstItem()-1) }}
                </td>

                <td>{{ $userEmail->user_nicename }}</td>

                <td>{{ $userEmail->user_login }}</td>

                <td>{{ $userEmail->user_email }}</td>

                <td>{{ $userEmail->display_name }}</td>

                <td>
                    {{ \Carbon\Carbon::parse($userEmail->user_registered)->format('d M Y h:i A') }}
                </td>

                <td>

                    <a href="{{ url('act_updateEmailActivate',['uid'=>$userEmail->ID]) }}"
                       class="btn btn-success"
                       onclick="return confirm('Are you sure you want to activate this user?')">

                        Activate User

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7" class="text-center">

                    No users found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $emailActiveUsers->links() !!}

    </div>

</div>