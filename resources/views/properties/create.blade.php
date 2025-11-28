{{-- resources/views/properties/create.blade.php --}}
<h1>Add New Property</h1>

@if ($errors->any())
    <div style="color:red">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('properties.store') }}" method="POST">
    @csrf
    <label>Title:</label>
    <input type="text" name="title" value="{{ old('title') }}"><br><br>

    <label>Location:</label>
    <input type="text" name="location" value="{{ old('location') }}"><br><br>

    <label>Price:</label>
    <input type="number" name="price" value="{{ old('price') }}"><br><br>

    <label>Phone:</label>
    <input type="text" name="phone" value="{{ old('phone') }}"><br><br>
    

    <button type="submit">Add Property</button>
</form>

<a href="{{ route('properties.index') }}">Back to List</a>
