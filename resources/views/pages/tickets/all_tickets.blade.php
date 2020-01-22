<table id="sample" class="table table-bordered table-responsive table-striped">

    <thead>
        <tr>
          <th>Ticket #</th>
          <th>Request #</th>
          <th>Form</th>
          <th>Priority</th>
          <th>Category</th>
          <th>Requested By</th>
          <th>Assigned To</th>
          <th>SLA</th>
          <th>Date Needed</th>
          <th>Date Closed</th>
          <th>Date Created</th>
          <th>Status</th>
          <th>State</th>
          <th>Action</th>
        </tr>
    </thead>

	{{-- <tbody>
		@foreach($all as $l)
       		<tr>
                <td>{{ $l->ticketId }}</td>
                <td>{{ $l->formId }}</td>
                <td>{{ $l->form }}</td>
                <td>
                    @if($l->priority == 'LOW')
                        <span class="badge bg-yellow">{{ $l->priority }}</span>
                    @elseif($l->priority == 'MEDIUM')
                        <span class="badge bg-orange">{{ $l->priority }}</span>
                    @else
                        <span class="badge bg-red">{{ $l->priority }}</span>  
                    @endif
                </td>
                <td>{{ $l->category }}</td>
                <td>{{ $l->requestedBy }}</td>
                <td>{{ $l->assignedTo }}</td>
                <td>{{ $l->sla }}</td>
                <td>{{ date('M j, Y, g:i a', strtotime($l->date_needed)) }}</td>
                <td>{{ date('M j, Y, g:i a', strtotime($l->date_closed)) }}</td>
                <td>{{ date('M j, Y, g:i a', strtotime($l->date_created)) }}</td>
                <td>
                    @if($l->status == 'OPEN')
                        <span class="badge bg-primary">{{ $l->status }}</span>
                    @elseif($l->status == 'CLOSED')
                        <span class="badge bg-green">{{ $l->status }}</span>
                    @elseif($l->status == 'ON HOLD')
                        <span class="badge bg-yellow">{{ $l->status }}</span>
                    @else
                        <span class="badge bg-red">{{ $l->status }}</span>
                    @endif
                </td>
                <td><span class="badge">{{ $l->state }}</span></td>
                <td align="center"><a href="{{ url('/tickets',$l->ticketId) }}" class="btn btn-primary btn-xs" ><span class="fa fa-eye"></span></a></td>
       		</tr>
        @endforeach	

	</tbody> --}}

</table>