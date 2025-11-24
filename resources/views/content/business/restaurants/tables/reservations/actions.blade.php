<div class="d-flex align-items-center gap-3">
    @if($reservation->status==\App\Enums\ReservationStatus::PENDING->value)
        <button
            type="button"
            class="btn btn-sm btn-icon"
            title="Accept"
            onclick="handleStatusButtons('{{route('table.reservation.accept', $reservation->id)}}', 'accept')"
        >
            <span class="ti-sm ti ti-check text-success"></span>
        </button>
        <button
            type="button"
            class="btn btn-sm btn-icon"
            title="Reject"
            onclick="handleStatusButtons('{{route('table.reservation.reject',$reservation->id)}}', 'reject')"
        >
            <span class="ti-sm ti ti-x text-danger"></span>
        </button>
    @endif
    <button type="button" class="btn btn-sm btn-icon" onclick="handleDeleteReservation('{{ route('reservation.destroy',$reservation->id) }}')" title="DELETE">
        <span class="ti-sm ti ti-trash"></span>
    </button>
</div>