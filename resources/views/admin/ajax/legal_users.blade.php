<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="legalUsersTable">

        <thead class="top-text">

            <tr>

                <th>#</th>
                <th>Company Name</th>
                <th>User Login</th>
                <th>User Email</th>
                <th>IP Address</th>
                <th>Join Date</th>
                <th>ESign</th>
                <th>TOS</th>

            </tr>

        </thead>

        <tbody>

        @forelse($legalUserDetail as $legalUser)

            <tr>

                <td>
                    {{ $loop->iteration + ($legalUserDetail->firstItem()-1) }}
                </td>

                <td>{{ $legalUser->user_nicename }}</td>

                <td>{{ $legalUser->user_login }}</td>

                <td>{{ $legalUser->user_email }}</td>

                <td>{{ $legalUser->public_ip_address }}</td>

                <td>
                    {{ \Carbon\Carbon::parse($legalUser->created_at)->format('d M Y h:i A') }}
                </td>

                <td>

                    @if(!empty($legalUser->esign_path) && file_exists(public_path('credit-app/'.$legalUser->esign_path)))

                        <img src="{{ asset('public/credit-app/'.$legalUser->esign_path) }}"
                             style="max-width:200px;max-height:200px;">

                    @else

                        <span class="text-muted">

                            No E-Sign

                        </span>

                    @endif

                </td>

                <td>

                    <a href="{{ url('/admin/tospdf/'.$legalUser->ID) }}"
                       class="btn btn-info"
                       target="_blank">

                        Download TOS PDF

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="8" class="text-center">

                    No Legal Users Found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $legalUserDetail->links() !!}

    </div>

</div>