<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Booking Preview</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .preview-box { max-width:720px; margin:48px auto; border:3px solid #ddd; padding:28px; border-radius:8px; }
    .success-banner { border:3px solid #1aa05a; color:#1aa05a; padding:18px; font-weight:700; }
  </style>
</head>
<body>
  <div class="container">
    <div class="preview-box text-center">
      <h3 class="mb-4">Patient Info</h3>
      <div class="success-banner mb-4">Appointment preview. Please confirm details before booking.</div>

      <div class="row text-start">
        <div class="col-md-6">
          <p><strong>First name:</strong> {{ $data['first_name'] ?? '' }}</p>
          <p><strong>Surname:</strong> {{ $data['surname'] ?? '' }}</p>
          <p><strong>Middle:</strong> {{ $data['middle'] ?? '' }}</p>
          <p><strong>Address:</strong> {{ $data['address'] ?? '' }}</p>
          <p><strong>Contact:</strong> {{ $data['contact'] ?? '' }}</p>
          <p><strong>Email:</strong> {{ $data['email'] ?? '' }}</p>
        </div>
        <div class="col-md-6">
          <p><strong>Service:</strong> {{ $data['services'] ?? '' }}</p>
          <p><strong>Branch:</strong> {{ $data['branch'] ?? '' }}</p>
          <p><strong>Date:</strong> {{ $data['appointment_date'] ?? '' }}</p>
          <p><strong>Time:</strong> {{ $data['appointment_time'] ?? '' }}</p>
          <p><strong>Gender:</strong> {{ $data['gender'] ?? '' }}</p>
        </div>
      </div>

      <div class="mt-4 d-flex justify-content-center gap-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        <form action="{{ route('book.confirm') }}" method="POST" style="display:inline">
          @csrf
          <button type="submit" class="btn btn-success">Book</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
