<div class="place ">
    @if ($party->additions->count() > 0)
        <div class="additions">
            <label class="">{{ trans('messages.additions') }}</label>
            <div class="list">
                @foreach ($party->additions as $item)
                    <div class="addition checkbox-c">
                        <input type="checkbox" name="additions[]" id="addition{{ $item->id }}"
                            data-price="{{ $item->price }}" value="{{ $item->id }}" {!! $item->is_required ? 'onclick="return false;" checked="checked"' : '' !!}>
                        <div class="checkbox-v">
                            <span><i class="fas fa-check"></i></span>
                        </div>
                        <label for="addition{{ $item->id }}">{{ $item->name }} ({{ $item->price }}
                            {{ $country->currency }})</label>
                    </div>
                @endforeach
            </div>

        </div>

    @endif
    @if ($party->fields->count() > 0)
        <div class="fields">
            @foreach ($party->fields as $item)
                @if ($item->type == 'text')
                    <div class="form-group ">
                        <label for="fields{{ $item->id }}">
                            {{ $item->name }} @if ($item->is_required)
                                <span class="required">*</span>
                            @endif
                        </label>
                        <input type="text" name="fields[{{ $item->id }}]" id="fields{{ $item->id }}"
                            {{ $item->is_required ? 'required' : '' }} class="form-control">
                    </div>
                @elseif($item->type == 'checkbox')
                    <div class="form-group ">
                        <label for="fields{{ $item->id }}">
                            {{ $item->name }} @if ($item->is_required)
                                <span class="required">*</span>
                            @endif
                        </label>
                        <div class="row">
                            @foreach ($item->options as $tt)
                                <div class="col-12">

                                    <div class="checkbox-c">
                                        <input type="checkbox" id="fields{{ $tt->id }}" class="form-checkbox"
                                            name="fields[{{ $item->id }}][]" value="{{ $tt->id }}" id="field-checkbox-{{$tt->id}}"
                                            style="width:15px;height:15px;">
                                        <div class="checkbox-v">
                                            <span><i class="fas fa-check"></i></span>
                                        </div>
                                        <label class="field-checkbox-{{$tt->id}}"" style="margin-bottom: 0;"
                                            for="fields{{ $tt->id }}">{{ $tt->name }}</label>
                                    </div>

                                </div>
                            @endforeach

                        </div>
                    </div>
                @elseif($item->type == 'select')
                    <div class="form-group ">
                        <label for="fields{{ $item->id }}">
                            {{ $item->name }} @if ($item->is_required)
                                <span class="required">*</span>
                            @endif
                        </label>
                        <select name="fields[{{ $item->id }}]" id="fields{{ $item->id }}"
                            class="form-control select2" {{ $item->is_required ? 'required' : '' }}>
                            @foreach ($item->options as $op)
                                <option value="{{ $op->id }}" {{ $op->is_default == 1 ? 'selected' : '' }}>
                                    {{ $op->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
    <div class="form-group payment">
        <label for="payment_type">
            @lang('messages.payment_method')
        </label>
        <select name="payment_type" id="payment_type" class="select2 form-control">
            @if ($restaurant->enable_party_payment_cash == 'true')
                <option value="cash" selected>
                    @lang('dashboard.cash_on_delivery')
                </option>
            @endif
            @if ($restaurant->enable_party_payment_bank == 'true')
                <option value="bank">
                    @lang('messages.transfer_bank')
                </option>
            @endif
            @if ($restaurant->enable_party_payment_online == 'true' and false)
                <option value="online">
                    @lang('messages.online')
                </option>
            @endif
        </select>
    </div>

    <div class="total-price display-none">الاجمالي :
        <span></span>
        {{ $country->currency }}
    </div>
    <div class="footer-button text-center">
        <button type="submit" class="btn btn-primary" id="save-all">{{ trans('messages.next_step') }}</button>

        <button data-href="{{ route('sliverHome', $restaurant->name_barcode) }}" class="btn btn-secondary return"
            type="button">{{ trans('messages.return') }}</button>
    </div>

</div>
