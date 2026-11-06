<div class="table-responsive log-table">

    <table class="table table-hover log-table" id="affiliatesTable">

        <thead class="top-text">

            <tr>

                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>

            </tr>

        </thead>

        <tbody>

        @forelse($affiliates as $affiliate)

            <tr>

                <td>

                    {{ $loop->iteration + ($affiliates->firstItem() - 1) }}

                </td>

                <td>{{ $affiliate->name }}</td>

                <td>{{ $affiliate->email }}</td>

                <td>{{ $affiliate->phone }}</td>

                <td>

                    @if($affiliate->is_active)

                        <a href="#"
                           class="btn btn-primary"
                           data-toggle="modal"
                           data-target="#referral-users"
                           onclick="getAffiliateUsers('{{ $affiliate->id }}')">

                            View All Referrals

                        </a>

                        <a href="{{ url('/admin/deactivate-affiliate/'.$affiliate->id) }}"
                           class="btn btn-danger">

                            Deactivate

                        </a>

                    @else

                        <span class="badge badge-info">

                            Affiliate Deactivated

                        </span>

                    @endif

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="5" class="text-center">

                    No Affiliates Found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">

        {!! $affiliates->links() !!}

    </div>

</div>