<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="passwordUsersTable">

        <thead class="top-text">

        <tr>

            <th>#</th>
            <th>Company Name</th>
            <th>User Login</th>
            <th>User Email</th>
            <th>User Url</th>
            <th>Display Name</th>
            <th>Action</th>

        </tr>

        </thead>

        <tbody>

        @forelse($activeUserDetail1 as $actUser)

            <tr>

                <td>
                    {{ $loop->iteration + ($activeUserDetail1->firstItem()-1) }}
                </td>

                <td>{{ $actUser->user_nicename }}</td>

                <td>{{ $actUser->user_login }}</td>

                <td>{{ $actUser->user_email }}</td>

                <td>{{ $actUser->user_url }}</td>

                <td>{{ $actUser->display_name }}</td>

                <td>

                    <a href="#"
                       class="btn btn-danger"
                       data-toggle="modal"
                       data-target="#changepassword"
                       onclick="changeuserID('{{ $actUser->ID }}')">

                        Change User Password

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

        {!! $activeUserDetail1->links() !!}

    </div>

</div>