<div class="d-flex align-items-center gap-3">
    <a href="{{route('menu-items.show', $menu_item->id)}}" class="btn btn-sm btn-icon" title="Show">
        <span class="ti-sm ti ti-eye"></span>
    </a>
    <button type="button" class="btn btn-sm btn-icon" onclick="handleEditMenuItem({{ $menu_item }})" title="Edit">
        <span class="ti-sm ti ti-pencil"></span>
    </button>
    <button type="button" class="btn btn-sm btn-icon" onclick="deleteItem('{{ route('menu-items.destroy',$menu_item->id) }}')" title="DELETE">
        <span class="ti-sm ti ti-trash"></span>
    </button>
</div>