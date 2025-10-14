<!-- resources/views/order/partials/cart_table.blade.php -->

<table class="order-items-table">
    <thead>
        <tr>
            <th class="order-table-header">商品番号</th>
            <th class="order-table-header">商品名</th>
            <th class="order-table-header">数量</th>
            <th class="order-table-header">単価</th>
            <th class="order-table-header">小計</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($cart) && count($cart) > 0)
            @foreach ($cart as $item)
                <tr>
                    <td class="order-table-data" data-label="商品番号">{{ $item['product_code'] }}</td>
                    <td class="order-table-data" data-label="商品名">{{ $item['name'] }}</td>
                    <td class="order-table-data" data-label="数量">{{ $item['quantity'] }}</td>
                    <td class="order-table-data" data-label="単価">¥{{ number_format($item['price']) }}</td>
                    <td class="order-table-data" data-label="小計">
                        ¥{{ number_format($item['price'] * $item['quantity']) }}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="order-table-data" style="text-align: center;">
                    カートに商品がありません。
                </td>
            </tr>
        @endif
    </tbody>
</table>
