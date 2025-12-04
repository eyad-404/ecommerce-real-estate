<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\OwnsProperty;
use App\Models\PropertyReservation;
use App\Http\Middleware\Role;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['show', 'reserve', 'cancelReservation', 'create', 'store']);
        $this->middleware(OwnsProperty::class)->only(['edit', 'update', 'destroy']);
    }

    // Display property management page
    public function propertyManagement()
    {
        $user = auth()->user();
        $properties = Property::query()
            ->when($user->role === 'seller', fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('id')
            ->get();

        $canAssignOwner = $user->role === 'admin';

        return view('properties.property-management', compact('properties', 'canAssignOwner'));
    }

    // Show all properties (with filters/search)
    public function index(Request $request)
    {
        $query = Property::query();

        if ($request->filled('search_term')) {
            $term = $request->search_term;
            $query->where(function ($q) use ($term) {
                $q->where('category', 'like', "%$term%")
                  ->orWhere('location', 'like', "%$term%")
                  ->orWhere('id', $term);
            });
        }

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('location')) $query->where('location', $request->location);
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('transaction_type')) $query->where('transaction_type', $request->transaction_type);

        if ($request->filled('sort_by')) {
            [$col, $dir] = explode(' ', $request->sort_by);
            $query->orderBy($col, $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $properties = $query->get();
        $categories = Property::select('category')->distinct()->pluck('category');
        $locations = Property::select('location')->distinct()->pluck('location');

        return view('properties.index', compact('properties', 'categories', 'locations'));
    }
    

    // Show single property
    public function show(Property $property)
    {
        $property->load('images', 'reservation'); // eager load multiple images + reservation info

        $reservation = $property->reservation;
        $user = auth()->user();

        $canReserve = (!$reservation) && $property->status === 'available';
        $canCancel = $reservation && $user && ($reservation->user_id === $user->id || $user->role === 'admin');
        $isPending = $reservation && !$canCancel;
        $isSold = $property->status === 'sold';

        return view('properties.details', compact('property', 'reservation', 'canReserve', 'canCancel', 'isPending', 'isSold'));
    }


    // Show create form
    public function create()
    {
        return view('properties.create');
    }

    // Store new property
    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,available,sold,reserved',
            'description' => 'required|string',
            'transaction_type' => 'required|string|in:sale,rent',
            'installment_years' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'user_id' => 'nullable|exists:users,id',
            'multiple_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $user = auth()->user();
        $data['user_id'] = ($user->role === 'admin' && !empty($data['user_id']))
            ? $data['user_id']
            : $user->id;

        // Handle single image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/properties'), $filename);
            $data['image'] = 'images/properties/'.$filename;
        }

        $property = Property::create($data);

        // Handle multiple images
        if ($request->hasFile('multiple_images')) {
            foreach ($request->file('multiple_images') as $file) {
                $filename = time().'_'.uniqid().'_'.$file->getClientOriginalName();
                $file->move(public_path('images/properties'), $filename);

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => 'images/properties/'.$filename,
                ]);
            }
        }

        return response()->json(['success' => true, 'property' => $property], 201);
    }

    // Edit form
    public function edit(Property $property)
    {
        $property->load('images');
        return view('properties.edit', compact('property'));
    }

    // Update property
    public function update(Request $request, Property $property)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pending,available,sold,reserved',
            'description' => 'nullable|string',
            'transaction_type' => 'required|string|in:sale,rent',
            'installment_years' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'user_id' => 'nullable|exists:users,id',
            'multiple_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Single image
        if ($request->hasFile('image')) {
            if ($property->image && file_exists(public_path($property->image))) {
                unlink(public_path($property->image));
            }
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/properties'), $filename);
            $data['image'] = 'images/properties/'.$filename;
        }

        $shouldResetReservation = isset($data['status']) && in_array($data['status'], ['available', 'sold'], true);

        if (isset($data['user_id']) && auth()->user()->role !== 'admin') {
            unset($data['user_id']);
        }

        $property->update($data);

        if ($shouldResetReservation) {
            $this->releaseReservation($property);
        }

        $property->refresh();

        // Multiple images
        if ($request->hasFile('multiple_images')) {
            foreach ($request->file('multiple_images') as $file) {
                $filename = time().'_'.uniqid().'_'.$file->getClientOriginalName();
                $file->move(public_path('images/properties'), $filename);

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => 'images/properties/'.$filename,
                ]);
            }
        }

        return response()->json(['success' => true, 'property' => $property->fresh()]);
    }

    // Delete property
    public function destroy(Request $request, Property $property)
    {
        if ($property->image && file_exists(public_path($property->image))) {
            @unlink(public_path($property->image));
        }

        // Delete multiple images
        foreach ($property->images as $img) {
            if (file_exists(public_path($img->image_path))) {
                @unlink(public_path($img->image_path));
            }
            $img->delete();
        }

        $property->delete();

        return response()->json(['success' => true]);
    }
    public function reserve(Property $property)
{
    if ($property->isReserved()) {
        return redirect()->back()->with('error', 'Property already reserved.');
    }

    PropertyReservation::create([
        'property_id' => $property->id,
        'user_id' => auth()->id(),
        'reserved_at' => now(),
    ]);

    $property->update([
        'status' => 'reserved',
    ]);

    return redirect()->back()->with('success', 'Property reserved successfully.');
}
public function cancelReservation(Property $property)
{
    $reservation = $property->reservation;

    if (!$reservation) {
        return redirect()->back()->with('error', 'No reservation found for this property.');
    }

    $user = auth()->user();
    if ($reservation->user_id !== $user->id && $user->role !== 'admin') {
        abort(403, 'You are not allowed to cancel this reservation.');
    }

    $reservation->delete();
    $property->update(['status' => 'available']);

    return redirect()->back()->with('success', 'Reservation cancelled successfully.');
}

    public function paymentPlans(Property $property)
{
    return view('properties.payment_plans', compact('property'));
}

    /**
     * Reset reservation info so the property becomes unclaimed.
     */
    protected function releaseReservation(Property $property): void
    {
        // if ($property->reserved_by) {
        //     $user = User::find($property->reserved_by);

        //     if ($user && $user->reserved_property_id === $property->id) {
        //         $user->reserved_property_id = null;
        //         $user->save();
        //     }
        // }

        // $property->forceFill([
        //     'reserved_by' => null,
        //     'is_reserved' => false,
        // ])->save();
        $property->reservation?->delete();

    // اجعل حالة العقار متاحة بعد إزالة الحجز
    $property->update(['status' => 'available']);
    }
}
