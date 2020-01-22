<div class="row">
	<div class="col-sm-12">

		<div class="row margin">
{{-- 			<div class="box-header s-margin-b">
				<h3 class="box-title">Immediate Leader</h3>
			</div> --}}

			<div class="dataTables_wrapper form-inline dt-bootstrap">

				<div class="row">
					<div class="col-sm-12 table-responsive">

						<table class="table table-bordered table-striped vertical-middle">
							<thead>
								<tr>
									<th>Email Address</th>
									<th>First Name</th>
									<th>Last Name</th>
									<th>Contact</th>
									<th>Team</th>
									<th>Position</th>
									<th>Approver</th>
									<th>
										<center>Status</center>
									</th>
									<th>Reason</th>
								</tr>
							</thead>
							<tbody>

								@foreach($approvers as $approver)		

									<tr>
										<td>{{ $approver->approver->email }}</td>
										<td>{{ $approver->approver->first_name }}</td>
										<td>{{ $approver->approver->last_name }}</td>
										<td>{{ $approver->approver->renderContact() }}</td>
										<td>{{ $approver->approver->getTeam() ? $approver->approver->getTeam()->title : '' }}</td>
										<td>{{ $approver->approver->getDepartment() && $approver->approver->getDepartment()->position ? $approver->approver->getDepartment()->position->title : '' }}</td>
										<td>{{ $approver->renderType() }}</td>
										<td>
											<center>
												<span class="badge {{ $approver->renderStatusColor() }}">{{ $approver->renderStatus() }}</span>
											</center>
										</td>
										<td>
											{{ $approver->reason }}
										</td>										
									</tr>

								@endforeach

							</tbody>
						</table>

					</div>
				</div>

			</div>
			
		</div>

	</div>
</div>