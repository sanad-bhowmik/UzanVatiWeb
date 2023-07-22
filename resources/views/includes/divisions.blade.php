
<option value="">Select Division</option>
		@foreach (DB::table('divisions')->get() as $data)
		<option value="{{ $data->id}}">{{ $data->name }}</option>		
		@endforeach
