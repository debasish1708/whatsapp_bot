<div class="d-flex align-items-center gap-3">
    <button type="button" class="btn btn-sm btn-icon" onclick="handleEditTable({{ $table }})" title="Edit">
        <span class="ti-sm ti ti-pencil"></span>
    </button>
    <button type="button" class="btn btn-sm btn-icon" onclick="handleDeleteTable('{{ route('tables.destroy',$table->id) }}')" title="DELETE">
        <span class="ti-sm ti ti-trash"></span>
    </button>
</div>