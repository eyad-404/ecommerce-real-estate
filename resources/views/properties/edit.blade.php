{{-- resources/views/properties/edit.blade.php --}}
<h1>Edit Property</h1>

@if ($errors->any())
    <div style="color:red">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('properties.update', $property) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Title:</label>
    <input type="text" name="title" value="{{ old('title', $property->title) }}"><br><br>

    <label>Location:</label>
    <input type="text" name="location" value="{{ old('location', $property->location) }}"><br><br>

    <label>Price:</label>
    <input type="number" name="price" value="{{ old('price', $property->price) }}"><br><br>

    <label>Phone:</label>
    <input type="text" name="phone" value="{{ old('phone', $property->phone) }}"><br><br>

    <button type="submit">Update Property</button>
</form>

<a href="{{ route('properties.index') }}">Back to List</a>
