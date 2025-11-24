<div class="d-flex align-items-center gap-3">
    <button
        type="button"
        class="btn btn-sm btn-icon"
        title="Show"
        onclick="handleShowApplication('{{$row->id}}')"
    >
            <span class="ti-sm ti ti-eye"></span>
        </button>
    @if($row->status=='new' || $row->status == 'inprocess')
        <button
            type="button"
            class="btn btn-sm btn-icon"
            title="Approve"
            onclick="handleStatusButtons('{{route('school.admissions.accept', $row->id)}}', 'approve')"
        >
            <span class="ti-sm ti ti-check text-success"></span>
        </button>
        <button
            type="button"
            class="btn btn-sm btn-icon"
            title="Reject"
            onclick="handleStatusButtons('{{route('school.admissions.reject',$row->id)}}', 'reject')"
        >
            <span class="ti-sm ti ti-x text-danger"></span>
        </button>
    @endif
    @if ($row->status == 'rejected' && $row->payment_status == 'paid')
        <button
            type="button"
            class="btn btn-sm btn-icon"
            title="Refund Payment"
            onclick="handleStatusButtons('{{ route('school.admissions.refund', $row->id) }}', 'refund')"
        >
            <i class="fa-solid fa-rotate-left"></i>
        </button>
    @endif
</div>