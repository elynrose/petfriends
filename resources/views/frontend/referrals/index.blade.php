@extends('layouts.frontend')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Refer Friends & Earn Credits</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h5 class="alert-heading">How it works:</h5>
                        <p class="mb-0">Invite your friends to join PetFriends. When they sign up using your referral link, you'll receive 4 hours of free credits!</p>
                    </div>

                    <div class="mb-4">
                        <h5>Your Referral Link</h5>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $referralLink }}" id="referralLink" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('frontend.referrals.invite') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="form-group">
                            <label for="email">Invite by Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" placeholder="Enter friend's email" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Send Invitation
                                    </button>
                                </div>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </form>

                    <h5>Your Referrals</h5>
                    @if($referrals->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referrals as $referral)
                                        <tr>
                                            <td>{{ $referral->email }}</td>
                                            <td>
                                                @if($referral->is_registered)
                                                    <span class="badge badge-success">Registered</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $referral->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No referrals yet. Start inviting your friends!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
function copyReferralLink() {
    var copyText = document.getElementById("referralLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    // Show feedback
    var button = event.target.closest('button');
    var originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Copied!';
    setTimeout(function() {
        button.innerHTML = originalText;
    }, 2000);
}
</script>
@endsection 