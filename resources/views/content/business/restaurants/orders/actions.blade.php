<div class="d-flex align-items-center gap-3">
    <a href="{{route('restaurant.orders.show', $order->id)}}" class="btn btn-sm btn-icon" title="Show">
        <span class="ti-sm ti ti-eye"></span>
    </a>
    @if($order->status == 'pending')
        <button 
            type="button" 
            class="btn btn-sm btn-icon" 
            title="Mark delivered" 
            onclick="handleStatusButtons('{{route('restaurant.orders.mark-delivered', $order->id)}}', 'delivered')"
        >
            <span class="ti-sm ti ti-check text-success"></span>
        </button>
        <button 
            type="button" 
            class="btn btn-sm btn-icon" 
            title="cancel"
            onclick="handleStatusButtons('{{route('restaurant.orders.mark-canceled', $order->id)}}', 'canceled')"
        >
            <span class="ti-sm ti ti-x text-warning"></span>
        </button>
    @endif
</div>