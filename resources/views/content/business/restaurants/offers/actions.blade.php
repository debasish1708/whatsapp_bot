<div class="d-flex align-items-center gap-3">
    {{-- <a href="{{route('restaurant.offers.show', $offer->id)}}" class="btn btn-sm btn-icon" title="Show">
        <span class="ti-sm ti ti-eye"></span>
    </a> --}}
    <button type="button" class="btn btn-sm btn-icon" onclick="handleViewOffer('{{ $offer->id }}')" title="Show">
        <span class="ti-sm ti ti-eye"></span>
    </button>
    <button type="button" class="btn btn-sm btn-icon" onclick="handleEditOffer('{{ $offer->id }}')" title="Edit">
        <span class="ti-sm ti ti-pencil"></span>
    </button>
    <button type="button" class="btn btn-sm btn-icon"
        onclick="deleteItem('{{ route('restaurant.offers.destroy', $offer->id) }}')" title="DELETE">
        <span class="ti-sm ti ti-trash"></span>
    </button>
</div>
