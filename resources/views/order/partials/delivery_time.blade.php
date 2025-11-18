<div class="order-card order-delivery-info-card">
    <h2 class="order-card-title">お届け希望日時</h2>
    <div class="order-field">
        <span class="order-label">お届け希望日:</span>
        <span class="order-value">
            <input type="date" id="delivery_date" class="form-control-date" name="delivery_date"
                value="{{ old('delivery_date') }}">
        </span>
    </div>
    <div class="order-field">
        <span class="order-label">お届け希望時間:</span>
        <span class="order-value">
            <select class="form-select" id="delivery_time" name="delivery_time">
                @foreach ($deliveryTimes as $time)
                    <option value="{{ $time }}" {{ old('delivery_time') == $time ? 'selected' : '' }}>
                        {{ $time }}
                    </option>
                @endforeach
            </select>
            @error('delivery_time')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </span>
    </div>
</div>
