<h1>Your Reservation is Confirmed</h1>

<p>Dear {{ auth()->user()->name }},</p>

<p>Thank you for your interest in the property:</p>

<p><strong>{{ $property->title }} (ID: {{ $property->id }})</strong></p>

<p>{{ $property->description }}</p>

<p>
Your appointment is scheduled for: 
<strong>{{ $appointmentDate }}</strong>
</p>

<p>
You can visit our office to complete the purchase.<br>
Our team will contact you soon to confirm the details.
</p>

<p>Best regards,<br>EL Kayan Real Estate</p>
