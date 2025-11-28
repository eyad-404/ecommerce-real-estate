{{-- resources/views/properties/show.blade.php --}}
<h1>Property Details</h1>

<p><strong>ID:</strong> {{ $property->id }}</p>
<p><strong>Title:</strong> {{ $property->title }}</p>
<p><strong>Location:</strong> {{ $property->location }}</p>
<p><strong>Type:</strong>{{$property->transaction_type}}</p>
<p><strong>Price:</strong> {{ $property->price }}</p>
<p><strong>Phone:</strong> {{ $property->phone }}</p>

<a href="{{ route('properties.edit', $property) }}">Edit</a>
<form action="{{ route('properties.destroy', $property) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
</form>
<br><br>
<a href="{{ route('properties.index') }}">Back to List</a>
