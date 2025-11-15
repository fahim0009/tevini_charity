
<div class="py-1 text-center d-flex gap-2 justify-content-center">
    <a class="text-decoration-none bg-dark text-white py-1 px-3 rounded mb-1 d-block text-center"
       href="{{ route('topup', [$u->id, 0]) }}" target="_blank">
        <small>Top Up</small>
    </a>
</div>

<div class=" text-center d-flex gap-2 justify-content-center">
    <button type="button" class="text-decoration-none bg-secondary text-white py-1 px-3 rounded mb-1 d-block text-center" data-bs-toggle="modal" data-bs-target="#sendTextModal{{$u->id}}">
        <small>Message</small>
    </button>
    <div class="modal fade" id="sendTextModal{{$u->id}}" tabindex="-1" aria-labelledby="sendTextModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendTextModalLabel">Send Text Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.donor.sendtext', $u->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="textMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="textMessage" name="message" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



    <a href="{{ route('sendemail', $u->id) }}">
        <i class="fa fa-envelope-o" style="color:#4D617E;font-size:16px;"></i>
    </a>

    <a href="{{ route('donor.profile', $u->id) }}">
        <i class="fa fa-eye" style="color:#09a311;font-size:16px;"></i>
    </a>

    <a href="{{ route('donor.edit', encrypt($u->id)) }}">
        <i class="fa fa-edit" style="color:#2094f3;font-size:16px;"></i>
    </a>

    <a id="deleteBtn" rid="{{ $u->id }}">
        <i class="fa fa-trash-o" style="color:red;font-size:16px;"></i>
    </a>
