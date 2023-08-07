@if ($message = Session::get('success'))
<div class="bg-green-500 text-white p-4 rounded-lg mb-6 text-center">
        <strong>{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('error'))
<div class="bg-red-500 text-white p-4 rounded-lg mb-6 text-center">
        <strong>{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('warning'))
<div class="bg-yellow-500 text-white p-4 rounded-lg mb-6 text-center">
	<strong>{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('info'))
<div class="bg-blue-500 text-white p-4 rounded-lg mb-6 text-center">
	<strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('status'))
<div class="bg-blue-500 text-white p-4 rounded-lg mb-6 text-center">
	<strong>{{ $message }}</strong>
</div>
@endif

@if ($errors->any())
<div class="bg-red-500 text-white p-4 rounded-lg mb-6 text-center">
   <ul>
       @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
      @endforeach
   </ul>
</div>

@endif
