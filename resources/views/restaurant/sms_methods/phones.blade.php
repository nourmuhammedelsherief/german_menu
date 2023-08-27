<table class="table table-striped phones">
	<thead>
		<tr>
			<th  scope="col">{{ trans('dashboard.entry.phone') }}</th>
			<th  scope="col">{{trans('dashboard.is_send')}}</th>
		</tr>
	</thead>

	<tbody>
		@foreach ($phones as $item)
			<tr>
				<td>{{$item->phone}}</td>
				<td>
					@if($item->is_sent == 1) <span class="badge badge-success">{{ trans('dashboard.sent') }}</span>
					@else  <span class="badge badge-danger">{{ trans('dashboard.not_sent') }}</span>
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>