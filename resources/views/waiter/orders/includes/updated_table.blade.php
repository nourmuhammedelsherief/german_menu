<table id="example1" class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>
				<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
					<input type="checkbox" class="group-checkable"
						data-set="#sample_1 .checkboxes" />
					<span></span>
				</label>
			</th>
			{{-- <th></th> --}}

			<th> @lang('dashboard.waiter_order_id') </th>

			<th> @lang('dashboard.table') </th>
			{{-- <th> @lang('dashboard.user_phone') </th> --}}
			<th> @lang('dashboard.items') </th>
			<th> @lang('dashboard.note') </th>
			<th> @lang('dashboard.entry.status') </th>
			
			<th> @lang('dashboard.entry.created_at') </th>
			<th> @lang('messages.operations') </th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 0; ?>
		@foreach ($orders as $item)
			<tr class="odd gradeX" id="row-{{ $item->id }}">
				<td>
					<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
						<input type="checkbox" class="checkboxes" value="1" />
						<span></span>
					</label>
				</td>

				<td>
					{{ $item->id }}
				</td>
				<td>
					@if (isset($item->table->id))
						<a
							href="{{ route('restaurant.waiter.tables.edit', $item->table->id) }}">{{ $item->table->name }}</a>
					@endif
				</td>
				{{-- <td>
					{{ $item->phone }}
				</td> --}}

				<td>
					@foreach ($item->items as $tt)
						<span class="badge badge-info">{{ $tt->name }}</span>
					@endforeach
				</td>
				<td>{!! $item->note !!}</td>


				<td class="change-status">
					@if ($item->status == 'pending')
						<span class="badge badge-secondary">{{ trans('dashboard.pending') }}</span>
					@elseif ($item->status == 'in_progress')
						<span
							class="badge badge-primary">{{ trans('dashboard.in_progress') }}</span>
					@elseif ($item->status == 'completed')
						<span class="badge badge-success">{{ trans('dashboard.completed') }}</span>
					@elseif ($item->status == 'canceled')
						<span class="badge badge-danger">{{ trans('dashboard.canceled') }}</span>
					@endif
				</td>
				<td>{{ date('Y-m-d h:i A', strtotime($item->created_at)) }}</td>
				<td>
					@if (in_array($item->status, ['in_progress', 'pending']))
						<a class=" btn btn-primary change-status" data-id="{{ $item->id }}"
							data-toggle="modal" data-target="#changeStatus"
							data_name="{{ $item->name }}">
							<i class="fa fa-edit"></i> @lang('dashboard.change_status')
						</a>
					@endif
					


				</td>
			</tr>
		@endforeach
	</tbody>
</table>