

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kamatage Hearing Aid Trading Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Arial', sans-serif; }
        .navbar {
            box-shadow: 0 2px 4px #eee;
            background: #fff !important;
            z-index: 1030;
            padding-top: 0.5rem;
            padding-bottom: 0;
        }
        .navbar-brand {
            font-size: 2rem;
            display: flex;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .navbar-brand img {
            width: 48px;
            margin-left: 8px;
            margin-bottom: 0.2rem;
        }
        .navbar-nav {
            align-items: flex-end;
            height: 100%;
        }
        .navbar-nav .nav-link {
            font-size: 1.3rem;
            font-weight: bold;
            margin: 0 28px; /* increased from 18px to 28px for more space */
            padding-bottom: 0.2rem;
            color: #000;

        }
        .navbar-nav .nav-link.active,
        .navbar-nav .nav-link:focus {
            text-decoration: underline;
            text-underline-offset: 6px;
            text-decoration-thickness: 2px;
        }
        .section { padding: 60px 0; }
        /* Add margin-top to the first section to avoid being hidden by fixed navbar */
        .section:first-of-type {
            margin-top: 90px;
        }

        .section {
    scroll-margin-top: 100px; /* Adjust this value to match your navbar height */
}





        .gradient-box {
            background: linear-gradient(120deg, #f8b6b6 0%, #ffe29f 100%);
            border-radius: 10px;
            padding: 40px;
            margin: 0 auto;
            max-width: 600px;
            text-align: center;
        }
        .orange-btn {
            background: orange;
            color: #fff;
            border-radius: 8px;
            padding: 12px 40px;
            border: 2px solid #222;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 2px 2px 0 #222;
            transition: all 0.3s ease;
        }
        .orange-btn:hover { 
            background: #ff9900; 
            transform: translateY(-2px);
            box-shadow: 4px 4px 0 #222;
        }
        .branch-img {
              width: 320px;
              height: 320px;
              object-fit: cover;
              border-radius: 0;
              background: #fff;
              border: 3px solid orange;
              margin-bottom: 10px;
        }

        .orange-btn {
  background: orange;
  color: #fff;
  border-radius: 20px;
  font-weight: bold;
  border: 2px solid #222;
  box-shadow: 2px 2px 0 #222;
 }
         .orange-btn:hover { background: #ff9900; }
         .form-control, .form-select {
             border: 2px solid #222 !important;
             border-radius: 3px !important;
             box-shadow: none !important;
      }
        .service-img {
              width: 120px;
              height: 120px;
              object-fit: cover;
              border-radius: 50%;
              background: #fff;
             border: 2px solid #eee;

     }
        .contact-card {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 8px #ddd;
            max-width: 300px;
            margin: 0 auto;
        }
        .btn-link { color: #fff; text-decoration: none; }
        .btn-link:hover { text-decoration: underline; }
        .form-label { font-weight: bold; }
        .form-section { background: #fffbe6; border: 2px solid orange; border-radius: 10px; padding: 30px; }
        .schedule-label { font-weight: bold; font-size: 2rem; }
        
        /* Booking form specific styles */
        .booking-form {
            background: #fff;
            border: 3px solid orange;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .booking-form .form-control,
        .booking-form .form-select {
            border: 1px solid #222 !important;
            border-radius: 3px !important;
            box-shadow: none !important;
            transition: border-color 0.3s ease;
        }
        .booking-form .form-control:hover,
        .booking-form .form-select:hover {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 .15rem rgba(13,110,253,.15) !important;
        }
        
        .booking-form .form-control:focus,
        .booking-form .form-select:focus {
            border-color: orange !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 165, 0, 0.25) !important;
        }
        
        .booking-form .input-group-text {
            background: #fff;
            border: 1px solid #222;
            border-left: none;
        }
        .booking-form .input-group-text:hover {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 .12rem rgba(13,110,253,.15) !important;
        }
        
        .booking-form h4 {
            color: #333;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        @media (max-width: 991.98px) {
            .navbar-brand { margin-bottom: 0; }
            .navbar-nav { align-items: flex-start; }
        }

        .btn-warning {
            background-color: #e67300 !important;   /* darker orange */
            border-color: #e67300 !important;
            color: #fff !important;
    }
        .btn-warning:hover, .btn-warning:focus {
             background-color: #b35900 !important;   /* even darker on hover */
             border-color: #b35900 !important;
             color: #fff !important;
  }

  /* Make Contact title and buttons bigger */
         #contact h2 {   
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 1px;
   }

         #contact .btn-warning {
             font-size: 1.5rem;
             padding: 18px 0;
        }

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light border-bottom fixed-top">
    <div class="container d-flex align-items-center justify-content-between" style="min-height:70px;">
        <div class="d-flex align-items-center">
            <a class="navbar-brand fw-bold me-3" href="#">
                HATC
                <img src="images/logos.png" alt="Logo">
            </a>
        </div>

        <div class="d-flex justify-content-center flex-grow-1">
            <ul class="navbar-nav d-flex flex-row align-items-center mb-0">
                <li class="nav-item"><a class="nav-link active px-3" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-bold px-3" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link fw-bold px-3" href="#branch">Branch</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#contact">Contact</a></li>
                <li class="nav-item"><a class="nav-link fw-bold px-3" href="#book">Book</a></li>
            </ul>
        </div>

        <div class="d-flex align-items-center ms-3">
            <a href="{{ route('patient.login') }}" class="btn btn-dark" style="border-radius:20px; padding:8px 16px;">Log in</a>
        </div>
    </div>
</nav>

<!-- Home Section -->
<section id="home" class="section">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
        <div class="flex-fill text-center text-md-start mb-4 mb-md-0">
            <h2>Kamatage Hearing Aid Trading Center</h2>
            <p class="mt-3" style="max-width:400px;margin-bottom:20px; text-align:left;">
                Kamatage Hearing Aid Trading Center is dedicated to providing accessible and high-quality hearing solutions through reliable hearing aids and professional care, ensuring improved communication and quality of life for individuals with hearing loss.
            </p>
            <button class="orange-btn" onclick="document.getElementById('book').scrollIntoView({behavior:'smooth'})">Book</button>
        </div>
        <div class="flex-fill text-center">
            <img src="images/clinic.jpg" alt="Clinic Interior" class="img-fluid rounded" style="max-width:420px;">
        </div>
    </div>
</section>



<!-- Services Section -->
<section id="services" class="section">
    <div class="container text-center">
        <h2>Services</h2>
        <div class="row justify-content-center">
            <div class="col-6 col-md-3 mb-4">
                <img src="images/oae.png" class="service-img mb-2" alt="OAE">
                <div><b>OAE</b><br><small>Oto Acoustic with Emission</small></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/abr.png" class="service-img mb-2" alt="ABR">
                <div><b>ABR</b><br><small>Auditory Brain Response</small></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/assr.png" class="service-img mb-2" alt="ASSR">
                <div><b>ASSR</b><br><small>Auditory State Steady Response</small></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/pta.png" class="service-img mb-2" alt="PTA">
                <div><b>PTA</b><br><small>Pureton</small></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/audiometry.png" class="service-img mb-2" alt="Audiometry">
                <div><b>Audiometry</b></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/speech-test-modified.png" class="service-img mb-2" alt="Speech Test">
                <div><b>Speech Test</b></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/tympanometry.png" class="service-img mb-2" alt="Tympanometry">
                <div><b>Tympanometry</b></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/play.png" class="service-img mb-2" alt="Play Audiometry">
                <div><b>Play Audiometry</b></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/fitting.png" class="service-img mb-2" alt="Hearing Aid Fitting">
                <div><b>Hearing Aid Fitting</b></div>
            </div>
            <div class="col-6 col-md-3 mb-4">
                <img src="images/aided.png" class="service-img mb-2" alt="Aided Testing">
                <div><b>Aided Testing</b></div>
            </div>
        </div>
    </div>
</section>

<!-- Branch Section -->
<section id="branch" class="section">
    <div class="container text-center">
        <h2>Branch</h2>
        <div class="row justify-content-center">
            <div class="col-12 col-md-4 mb-4">
                <img src="images/cdo.jpg" class="img-fluid mb-2 branch-img" alt="Carmen CDO">
                <div><b>Carmen CDO</b></div>
            </div>
            <div class="col-12 col-md-4 mb-4">
                <img src="images/davao.jpg" class="img-fluid mb-2 branch-img" alt="Davao">
                <div><b>Davao</b></div>
            </div>
            <div class="col-12 col-md-4 mb-4">
                <img src="images/butuan.jpg" class="img-fluid mb-2 branch-img" alt="Butuan">
                <div><b>Butuan</b></div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <div style="position: relative; width: 100%; max-width: 440px; margin: 0 auto; min-height: 250px;">
                    <!-- Image 2 (background, behind) -->
                    <img src="images/2.jpg" class="img-fluid"
                         alt="About 2"
                         style="position: absolute; left: 30px; top:100px; width: 90%; border-radius: 18px; box-shadow: 0 2px 18px #bbb; z-index: 1;">
                    <!-- Image 1 (front, above) -->
                    <img src="images/1.jpg" class="img-fluid"
                         alt="About 1"
                         style="position: relative; width: 100%; border-radius: 18px; box-shadow: 0 4px 24px #aaa; z-index: 2;">
                </div>
            </div>
            <div class="col-md-6">
                <h2 style="font-weight:bold;">About Us</h2>
                <p style="text-align:center;">
                    Our center specializes in providing high-quality hearing aids,<br>
                    hearing assessments, and personalized hearing care services<br>
                    tailored to meet each patient’s unique needs. With a team of<br>
                    licensed audiologists and trained professionals, we ensure accurate<br>
                    diagnostics, expert advice, and ongoing support throughout your<br>
                    hearing journey.
                </p>
                <p style="text-align:center;">
                    We understand the challenges that come with hearing loss, and we aim to create a<br>
                    comfortable, respectful, and welcoming environment for all.<br>
                    Whether you need a new hearing device, a check-up, or simply guidance,<br>
                    we are here to assist you every<br>
                    step of the way
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section bg-light">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left: Image -->
            <div class="col-md-7 mb-4 mb-md-0">
                <img src="images/contact.jpg" class="img-fluid" alt="Contact Ear" style="max-width:350x;">
            </div>
            <!-- Right: Contact Info and Branch Buttons -->
            <div class="col-md-5 text-center">
                <h2 class="mb-4"><b>Contact</b></h2>
                <a href="#" class="btn btn-lg btn-warning w-100 mb-3" style="font-weight:bold;" data-bs-toggle="modal" data-bs-target="#contactModal">Cagayan de Oro</a>
                <a href="#" class="btn btn-lg btn-warning w-100 mb-3" style="font-weight:bold;" data-bs-toggle="modal" data-bs-target="#contactModal">BUTUAN</a>
                <a href="#" class="btn btn-lg btn-warning w-100 mb-3" style="font-weight:bold;" data-bs-toggle="modal" data-bs-target="#contactModal">DAVAO</a>
            </div>
        </div>
    </div>
</section>

<!-- Book Section -->
<section id="book" class="section">
  <div class="container">
    <h2 class="mb-4 text-center" style="font-weight:bold; font-size:2.5rem;">Book Appointment</h2>
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show text-center" role="alert" style="max-width:600px; margin:0 auto 20px;">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show text-center" role="alert" style="max-width:600px; margin:0 auto 20px;">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Please correct the following errors:
        <ul class="mb-0 mt-2">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
                <div class="row justify-content-center">
                        <div class="col-lg-10">
                                <form id="bookingForm" action="/book/preview" method="POST" class="booking-form" novalidate>
                                        @csrf
                                        <div class="row">
                                                <!-- Left Column - Personal Information -->
                                                <div class="col-md-6">
                                                        <h4 class="mb-3 fw-bold text-dark">Personal Information</h4>
                                                        <div class="mb-3">
                                                            <label class="form-label">First Name: <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="firstname" id="firstname" value="{{ old('firstname', session('booking_data.firstname')) }}">
                                                            <div class="invalid-feedback" id="firstnameError"></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Surname: <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="surname" id="surname" value="{{ old('surname', session('booking_data.surname')) }}">
                                                            <div class="invalid-feedback" id="surnameError"></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Middle: <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="middle" id="middle" value="{{ old('middle', session('booking_data.middle')) }}">
                                                            <div class="invalid-feedback" id="middleError"></div>
                                                        </div>
<script>
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    let hasError = false;
    // Patterns: only letters, spaces, hyphens; forbid <, >, ;, =, ', ,
    const namePattern = /^[A-Za-z\s\-]+$/;
    const forbidden = /[<>;=\',]/;
    // First Name
    const firstname = document.getElementById('firstname');
    const firstnameError = document.getElementById('firstnameError');
    firstnameError.textContent = '';
    firstname.classList.remove('is-invalid');
    if (!namePattern.test(firstname.value) || forbidden.test(firstname.value)) {
        firstnameError.textContent = 'Only letters, spaces, or hyphens allowed. Do not input special characters <, >, ;, =, \' or ,';
        firstname.classList.add('is-invalid');
        hasError = true;
    }
    // Surname
    const surname = document.getElementById('surname');
    const surnameError = document.getElementById('surnameError');
    surnameError.textContent = '';
    surname.classList.remove('is-invalid');
    if (!namePattern.test(surname.value) || forbidden.test(surname.value)) {
        surnameError.textContent = 'Only letters, spaces, or hyphens allowed. Do not input special characters <, >, ;, =, \' or ,';
        surname.classList.add('is-invalid');
        hasError = true;
    }
    // Middle Name
    const middle = document.getElementById('middle');
    const middleError = document.getElementById('middleError');
    middleError.textContent = '';
    middle.classList.remove('is-invalid');
    if (middle.value && (!namePattern.test(middle.value) || forbidden.test(middle.value))) {
        middleError.textContent = 'Only letters, spaces, or hyphens allowed. Do not input special characters <, >, ;, =, \' or ,';
        middle.classList.add('is-invalid');
        hasError = true;
    }
    // Age
    const age = document.querySelector('input[name="age"]');
    let ageError = document.getElementById('ageError');
    if (!ageError) {
        ageError = document.createElement('div');
        ageError.className = 'invalid-feedback';
        ageError.id = 'ageError';
        age.parentNode.appendChild(ageError);
    }
    ageError.textContent = '';
    age.classList.remove('is-invalid');
    if (!age.value || isNaN(age.value) || age.value < 1 || age.value > 120) {
        ageError.textContent = 'Age must be a number between 1 and 120.';
        age.classList.add('is-invalid');
        hasError = true;
    }

    // Birthdate (from day/month/year fields)
    const day = document.querySelector('input[name="birth_day"]');
    const month = document.querySelector('input[name="birth_month"]');
    const year = document.querySelector('input[name="birth_year"]');
    // Place error message after the row of fields
    let birthRow = day.parentNode.parentNode; // .row.g-2
    let birthError = document.getElementById('birthError');
    if (!birthError) {
        birthError = document.createElement('div');
        birthError.className = 'invalid-feedback';
        birthError.id = 'birthError';
        birthRow.parentNode.insertBefore(birthError, birthRow.nextSibling);
    }
    birthError.textContent = '';
    [day, month, year].forEach(f => f.classList.remove('is-invalid'));
    const dayVal = parseInt(day.value, 10), monthVal = parseInt(month.value, 10), yearVal = parseInt(year.value, 10);
    let validDate = true;
    // Check ranges first
    if (!day.value || !month.value || !year.value || isNaN(dayVal) || isNaN(monthVal) || isNaN(yearVal) ||
        dayVal < 1 || dayVal > 31 || monthVal < 1 || monthVal > 12 || yearVal < 1900 || yearVal > 2100) {
        validDate = false;
    } else {
        // Now check if the date is valid
        const dateStr = `${year.value.padStart(4,'0')}-${month.value.padStart(2,'0')}-${day.value.padStart(2,'0')}`;
        const dateObj = new Date(dateStr);
        validDate = dateObj && (dateObj.getFullYear() == yearVal) && (dateObj.getMonth()+1 == monthVal) && (dateObj.getDate() == dayVal);
    }
    if (!validDate) {
        birthError.textContent = 'Enter a valid birth date.';
        [day, month, year].forEach(f => f.classList.add('is-invalid'));
        hasError = true;
    }

    // Address
    const address = document.querySelector('input[name="address"]');
    let addressError = document.getElementById('addressError');
    if (!addressError) {
        addressError = document.createElement('div');
        addressError.className = 'invalid-feedback';
        addressError.id = 'addressError';
        address.parentNode.appendChild(addressError);
    }
    addressError.textContent = '';
    address.classList.remove('is-invalid');
    const addressPattern = /^[A-Za-z0-9\s,\.]+$/;
    const forbiddenAddr = /[<>;=\',]/;
    if (!addressPattern.test(address.value) || forbiddenAddr.test(address.value)) {
        addressError.textContent = 'Address may contain letters, numbers, commas and periods only.';
        address.classList.add('is-invalid');
        hasError = true;
    }

    if (hasError) {
        e.preventDefault();
    }
});
</script>
                                                        <div class="mb-3">
                                                                <label class="form-label">Age: <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" name="age" value="{{ old('age', session('booking_data.age')) }}">
                                                        </div>

                                                        <div class="mb-3">
                                                                <label class="form-label">Birthdate: <span class="text-danger">*</span></label>
                                                                <input type="date" class="form-control" name="birthdate" id="birthdate" value="{{ old('birthdate', session('booking_data.birthdate')) }}">
                                                        </div>

                                                        <div class="mb-3">
                                                                <label class="form-label">Address: <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="address" value="{{ old('address', session('booking_data.address')) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                                <label class="form-label">Contact: <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="contact" value="{{ old('contact', session('booking_data.contact')) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                                <label class="form-label">Email: <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control" name="email" value="{{ old('email', session('booking_data.email')) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                                <label class="form-label">Gender: <span class="text-danger">*</span></label>
                                                                <select name="gender" class="form-select">
                                                                        <option value="">Select gender</option>
                                                                        <option value="Male" {{ old('gender', session('booking_data.gender')) === 'Male' ? 'selected' : '' }}>Male</option>
                                                                        <option value="Female" {{ old('gender', session('booking_data.gender')) === 'Female' ? 'selected' : '' }}>Female</option>
                                                                        <option value="Other" {{ old('gender', session('booking_data.gender')) === 'Other' ? 'selected' : '' }}>Other</option>
                                                                </select>
                                                        </div>
                                                </div>

                                                <!-- Right Column - Appointment Details and Schedule -->
                                                <div class="col-md-6">
                                                        <h4 class="mb-3 fw-bold text-dark">Appointment Details</h4>
                                                        <div class="mb-3">
                                                                <label class="form-label">Services: <span class="text-danger">*</span></label>
                                                                @php($forced = request('service'))
                                                                @php($selectedService = $forced ?: old('services', session('booking_data.services')))
                                                                @php($lockService = request()->has('service') || request()->boolean('connected'))
                                                                <select class="form-select" name="services" id="services" @if($lockService) disabled @endif>
                                                                        <option value="">Select a service</option>
                                                                        <option value="PTA - Puretone Audiometry" {{ $selectedService === 'PTA - Puretone Audiometry' ? 'selected' : '' }}>PTA - Puretone Audiometry</option>
                                                                        <option value="Speech Audiometry" {{ $selectedService === 'Speech Audiometry' ? 'selected' : '' }}>Speech Audiometry</option>
                                                                        <option value="Tympanometry" {{ $selectedService === 'Tympanometry' ? 'selected' : '' }}>Tympanometry</option>
                                                                        <option value="ABR - Autitory Brain Response" {{ $selectedService === 'ABR - Autitory Brain Response' ? 'selected' : '' }}>ABR - Autitory Brain Response</option>
                                                                        <option value="ASSR - Auditory State Steady Response" {{ $selectedService === 'ASSR - Auditory State Steady Response' ? 'selected' : '' }}>ASSR - Auditory State Steady Response</option>
                                                                        <option value="OAE - Oto Acoustic with Emession" {{ $selectedService === 'OAE - Oto Acoustic with Emession' ? 'selected' : '' }}>OAE - Oto Acoustic with Emession</option>
                                                                        <option value="Aided Testing" {{ $selectedService === 'Aided Testing' ? 'selected' : '' }}>Aided Testing</option>
                                                                        <option value="Play Audiometry" {{ $selectedService === 'Play Audiometry' ? 'selected' : '' }}>Play Audiometry</option>
                                                                        <option value="Hearing Aid Fitting" {{ $selectedService === 'Hearing Aid Fitting' ? 'selected' : '' }}>Hearing Aid Fitting</option>
                                                                </select>
                                                                @if($lockService)
                                                                <input type="hidden" name="services" value="{{ $selectedService }}">
                                                                @endif
                                                        </div>
                                                        <div class="mb-3">
                                                                <label class="form-label">Patient type:</label>
                                                                <select class="form-select" name="patient_type" id="patient_type">
                                                                        <option value="">Select type</option>
                                                                        <option value="PWD" {{ old('patient_type', session('booking_data.patient_type')) === 'PWD' ? 'selected' : '' }}>PWD</option>
                                                                        <option value="Senior" {{ old('patient_type', session('booking_data.patient_type')) === 'Senior' ? 'selected' : '' }}>Senior</option>
                                                                        <option value="Regular" {{ old('patient_type', session('booking_data.patient_type')) === 'Regular' ? 'selected' : '' }}>Regular</option>
                                                                </select>
                                                        </div>
                                                        <div class="mb-3">
                                                                <label class="form-label">Branch: <span class="text-danger">*</span></label>
                                                                <select class="form-select" name="branch" id="branch">
                                                                        <option value="">Select branch</option>
                                                                        <option value="CDO Branch" {{ old('branch', session('booking_data.branch')) === 'CDO Branch' ? 'selected' : '' }}>CDO Branch</option>
                                                                        <option value="Davao City Branch" {{ old('branch', session('booking_data.branch')) === 'Davao City Branch' ? 'selected' : '' }}>Davao City Branch</option>
                                                                        <option value="Butuan City Branch" {{ old('branch', session('booking_data.branch')) === 'Butuan City Branch' ? 'selected' : '' }}>Butuan City Branch</option>
                                                                </select>
                                                        </div>
                                                        <div class="mb-3">
                                                                <label class="form-label">Referred by:</label>
                                                                <input type="text" class="form-control" name="referred_by" value="{{ old('referred_by', session('booking_data.referred_by')) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                                <label class="form-label">Purpose: <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" name="purpose" id="purpose" rows="3">{{ old('purpose', session('booking_data.purpose')) }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                                <label class="form-label">Medical History:</label>
                                                                <textarea class="form-control" name="medical_history" rows="4">{{ old('medical_history', session('booking_data.medical_history')) }}</textarea>
                                                        </div>

                                                        <h4 class="mb-2 fw-bold text-dark">Schedule</h4>
                                                        <div class="mb-3">
                                                                <label class="form-label">Date: <span class="text-danger">*</span></label>
                                                                <input type="date" class="form-control" name="appointment_date" id="appointment_date" value="{{ old('appointment_date', session('booking_data.appointment_date')) }}">
                                                        </div>
                            <div class="mb-3">
                                <label class="form-label">Time: <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="text" class="form-control" name="appointment_time" id="appointment_time" 
                                           value="{{ old('appointment_time', session('booking_data.appointment_time')) }}"
                                           readonly aria-readonly="true" inputmode="none" onfocus="this.blur()" placeholder="Select a slot below">
                                </div>
                                <small class="text-muted">Please select one of the available slots below.</small>
                                <div id="availableSlots" class="mt-2" style="display:none;"></div>
                            </div>
                                                </div>
                                        </div>

                                        <!-- Next Button (centered) -->
                                        <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-primary" style="min-width:120px;">Next</button>
                                        </div>
                                </form>
                        </div>
                </div>
        </div>
</section>

<!-- Booking Preview Modal -->
<div class="modal fade" id="bookingPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border:1px solid #cfcfd3;">
            <div class="modal-body" style="padding:28px 32px;">
                <div id="previewContent">
                    <!-- preview fields will be injected here -->
                </div>
                <div class="mt-3 text-muted text-center">Please review the details. Click "Book" to confirm or "Back" to edit.</div>
            </div>
            <div class="modal-footer justify-content-center" style="border-top:0; padding-bottom:22px;">
                <button type="button" class="btn" id="previewBackBtn" style="background:#9b9b9b; color:#fff; width:96px;">Back</button>
                <button type="button" class="btn" id="previewBookBtn" style="background:#17a745; color:#fff; width:96px;">Book</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Highlight active nav link on scroll
    const sections = document.querySelectorAll('.section');
    const navLinks = document.querySelectorAll('.nav-link');
    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            if (window.scrollY >= sectionTop) {
                current = section.getAttribute('id');
            }
        });
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });

    // Patient type functionality (auto-fill removed)
    document.getElementById('patient_type').addEventListener('change', function() {
        const patientType = this.value;
        const dateField = document.getElementById('appointment_date');
        
        // Only auto-fill date based on patient type
        let suggestedDate = '';
        
        switch(patientType) {
            case 'PWD':
                // Suggest next available weekday (Monday-Friday)
                suggestedDate = getNextWeekday();
                break;
            case 'Senior':
                // Suggest next available weekday
                suggestedDate = getNextWeekday();
                break;
            case 'Regular':
                // Suggest next available date (any day)
                suggestedDate = getNextAvailableDate();
                break;
        }
        
        // Fill the date field only
        if (suggestedDate && !dateField.value) {
            dateField.value = suggestedDate;
        }
    });
    
    // Keep age and birthdate in sync
    (function(){
        const ageEl = document.querySelector('input[name="age"]');
        const birthEl = document.getElementById('birthdate');
        if(!ageEl || !birthEl) return;
        function clampAge(n){ n = parseInt(n,10); if(isNaN(n)) return ''; if(n<1) return 1; if(n>120) return 120; return n; }
        function toYmd(d){ const y=d.getFullYear(); const m=String(d.getMonth()+1).padStart(2,'0'); const da=String(d.getDate()).padStart(2,'0'); return `${y}-${m}-${da}`; }
        function onAgeChange(){ const age = clampAge(ageEl.value); if(!age) return; const today = new Date(); const bd = new Date(today); bd.setFullYear(today.getFullYear()-age); birthEl.value = toYmd(bd); }
        function onBirthChange(){ const v=birthEl.value; if(!v) return; const bd = new Date(v); if(isNaN(bd.getTime())) return; const today = new Date(); let age = today.getFullYear()-bd.getFullYear(); const m = today.getMonth()-bd.getMonth(); if(m<0 || (m===0 && today.getDate()<bd.getDate())) age--; if(age>=1 && age<=120) ageEl.value = age; }
        ageEl.addEventListener('input', onAgeChange);
        birthEl.addEventListener('change', onBirthChange);
        if(!birthEl.value && ageEl.value){ onAgeChange(); }
        else if(birthEl.value && !ageEl.value){ onBirthChange(); }
    })();
    
    // Helper function to get next weekday (Mon-Fri)
    function getNextWeekday() {
        const today = new Date();
        const nextDay = new Date(today);
        nextDay.setDate(today.getDate() + 1);
        
        // If it's weekend, move to Monday
        while (nextDay.getDay() === 0 || nextDay.getDay() === 6) {
            nextDay.setDate(nextDay.getDate() + 1);
        }
        
        return nextDay.toISOString().split('T')[0];
    }
    
    // Helper function to get next available date
    function getNextAvailableDate() {
        const today = new Date();
        const nextDay = new Date(today);
        nextDay.setDate(today.getDate() + 1);
        return nextDay.toISOString().split('T')[0];
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Highlight active nav link on scroll
    const sections = document.querySelectorAll('.section');
    const navLinks = document.querySelectorAll('.nav-link');
    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            if (window.scrollY >= sectionTop) {
                current = section.getAttribute('id');
            }
        });
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
</script>

<script>
// Booking preview: intercept the form submit, show modal with values
(function(){
    const form = document.getElementById('bookingForm');
    if(!form) return;

    const modalEl = document.getElementById('bookingPreviewModal');
    const previewContent = document.getElementById('previewContent');
    const previewBackBtn = document.getElementById('previewBackBtn');
    const previewBookBtn = document.getElementById('previewBookBtn');
    const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static' });

    function serializeForm(f) {
        const data = {};
        new FormData(f).forEach((v,k) => { data[k]=v; });
        return data;
    }

    function buildPreviewHtml(data){
                // build a two-column patient info layout similar to provided screenshot
                const leftItems = [
                        ['Name', `${data.firstname || ''} ${data.middle || ''} ${data.surname || ''}`],
                        ['Surname', data.surname || ''],
                        ['Middle', data.middle || ''],
                        ['Age', data.age || ''],
                        ['Birthday', `${data.birth_day || ''}/${data.birth_month || ''}/${data.birth_year || ''}`],
                        ['Address', data.address || ''],
                        ['Contact', data.contact || ''],
                        ['Email', data.email || ''],
                        ['Gender', data.gender || '']
                ];

                const rightItems = [
                        ['Services', data.services || ''],
                        ['Patient Type', data.patient_type || ''],
                        ['Branch', data.branch || ''],
                        ['Purpose', data.purpose || ''],
                        ['Referred by', data.referred_by || ''],
                        ['Medical History', data.medical_history || ''],
                        ['Date', data.appointment_date || ''],
                        ['Time', data.appointment_time || '']
                ];

                function renderList(items){
                        return items.map(i => `<div style="display:flex; justify-content:space-between; padding:4px 0;"><div style="font-weight:600;">${i[0]} :</div><div style="text-align:right;">${i[1] || '<span style=\"color:#999;\">-</span>'}</div></div>`).join('\n');
                }

                return `
                    <div style="max-width:640px; margin:0 auto;">
                        <div style="border:2px solid #c8cbd1; padding:26px;">
                            <h4 style="text-align:center; text-decoration:underline; margin-bottom:18px;">Patient Info</h4>
                            <div style="display:flex; gap:28px;">
                                <div style="flex:1; padding-right:8px;">${renderList(leftItems)}</div>
                                <div style="flex:1; padding-left:8px;">${renderList(rightItems)}</div>
                            </div>
                        </div>
                    </div>
                `;
    }

    function resetPreviewUI(){
        previewBookBtn.disabled = false;
        previewBookBtn.style.opacity = '1';
        previewBackBtn.textContent = 'Back';
        var footerEl = previewBookBtn ? previewBookBtn.parentElement : null;
        if (footerEl) { footerEl.style.display = ''; }
    }

    // Reset when modal fully hidden
    modalEl.addEventListener('hidden.bs.modal', function(){
        resetPreviewUI();
        // Clear preview (optional)
        previewContent.innerHTML = '';
    });

    form.addEventListener('submit', function(e){
        // Run all validation (including contact/email/gender and optional fields)
        triedSubmit = true;
        const validMain = typeof validateBookingForm === 'function' ? validateBookingForm(true) : true;
        const validContact = typeof validateContactEmailGender === 'function' ? validateContactEmailGender(true) : true;
        const validOptional = typeof validateOptionalFields === 'function' ? validateOptionalFields(true) : true;
        if (!validMain || !validContact || !validOptional) {
            e.preventDefault();
            return;
        }
        e.preventDefault();
        resetPreviewUI();
        const data = serializeForm(form);
        previewContent.innerHTML = buildPreviewHtml(data);
        bsModal.show();
    });

    previewBackBtn.addEventListener('click', function(){
        bsModal.hide();
    });

    // Book only when user clicks Book button
    previewBookBtn.addEventListener('click', function(){
        const payload = serializeForm(form);
        fetch('/book/confirm', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        }).then(async r => {
            const isJson = (r.headers.get('content-type') || '').includes('application/json');
            const data = isJson ? await r.json() : null;
            if (r.ok && data && data.status === 'ok') {
                previewContent.innerHTML = `
                  <div style="padding:18px; border:2px solid #17a745; background:#fff; max-width:520px; margin:0 auto; text-align:center;">
                    <h3 style="color:#17a745; font-weight:800; margin-bottom:8px;">Appointment saved. <span style="font-size:20px;">✔️</span></h3>
                    <div style="color:#17a745; font-size:1.05rem; font-weight:600;">Please wait for confirmation.</div>
                  </div>
                `;
                // Hide Back/Book buttons for the success view
                var footerEl = previewBookBtn ? previewBookBtn.parentElement : null;
                if (footerEl) { footerEl.style.display = 'none'; }
                setTimeout(() => {
                    bsModal.hide();
                    form.reset();
                    resetPreviewUI();
                }, 1200);
            } else {
                const msg = (data && (data.message || data.error)) || 'Unable to confirm appointment. Please try again.';
                previewContent.innerHTML = `<div class="text-danger" style="text-align:center;">${msg}</div>`;
            }
        }).catch(err => {
            console.error(err);
            previewContent.innerHTML = '<div class="text-danger" style="text-align:center;">Network error while confirming appointment.</div>';
        });
    });

})();







// --- BEGIN: Show errors only after interaction or submit ---
let triedSubmit = false;
function validateContactEmailGender(showAllErrors = false) {
    let hasError = false;
    // Contact
    const contact = document.querySelector('input[name="contact"]');
    let contactError = contact.nextElementSibling;
    if (!contactError || !contactError.classList.contains('invalid-feedback')) {
        contactError = document.createElement('div');
        contactError.className = 'invalid-feedback';
        contactError.id = 'contactError';
        contact.parentNode.appendChild(contactError);
    }
    contactError.textContent = '';
    contact.classList.remove('is-invalid');
    if ((showAllErrors || contact.value) && !/^[0-9]{11}$/.test(contact.value)) {
        contactError.textContent = 'Contact must be exactly 11 digits (numbers only).';
        contact.classList.add('is-invalid');
        hasError = true;
    }
    // Email
    const email = document.querySelector('input[name="email"]');
    let emailError = email.nextElementSibling;
    if (!emailError || !emailError.classList.contains('invalid-feedback')) {
        emailError = document.createElement('div');
        emailError.className = 'invalid-feedback';
        emailError.id = 'emailError';
        email.parentNode.appendChild(emailError);
    }
    emailError.textContent = '';
    email.classList.remove('is-invalid');
    const forbiddenEmail = /[<>;=\',]/;
    const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if ((showAllErrors || email.value) && !emailPattern.test(email.value)) {
        emailError.textContent = 'Must follow email format (example: name@example.com).';
        email.classList.add('is-invalid');
        hasError = true;
    } else if ((showAllErrors || email.value) && forbiddenEmail.test(email.value)) {
        emailError.textContent = 'Do not input special characters <, >, ;, =, \' or ,';
        email.classList.add('is-invalid');
        hasError = true;
    }
    // Gender
    const gender = document.querySelector('select[name="gender"]');
    let genderError = document.getElementById('genderError');
    if (!genderError) {
        genderError = document.createElement('div');
        genderError.className = 'invalid-feedback';
        genderError.id = 'genderError';
        gender.parentNode.appendChild(genderError);
    }
    genderError.textContent = '';
    gender.classList.remove('is-invalid');
    if ((showAllErrors || gender.value) && (!gender.value || (gender.value !== 'Male' && gender.value !== 'Female'))) {
        genderError.textContent = 'Please select Male or Female.';
        gender.classList.add('is-invalid');
        hasError = true;
    }
    return !hasError;
}

const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
        triedSubmit = true;
        if (!validateContactEmailGender(true)) {
            e.preventDefault();
        }
    });
    // Add blur/change event listeners for instant feedback after user interaction
    ['contact','email'].forEach(function(field) {
        const el = document.querySelector('input[name="'+field+'"]');
        if (el) {
            el.addEventListener('blur', function() {
                if (triedSubmit) validateContactEmailGender(true);
                else validateContactEmailGender(false);
            });
        }
    });
    const genderEl = document.querySelector('select[name="gender"]');
    if (genderEl) {
        genderEl.addEventListener('change', function() {
            if (triedSubmit) validateContactEmailGender(true);
            else validateContactEmailGender(false);
        });
    }
}
// --- END: Show errors only after interaction or submit ---

// --- BEGIN: Optional fields and dropdown/date/time validation ---
function validateOptionalFields(showAllErrors = false) {
    let hasError = false;
    // Referred by: optional, but if filled, only letters and spaces, no <, >, ;, =, ', and max 100 chars
    const referred = document.querySelector('input[name="referred_by"]');
    if (referred) {
        let referredError = referred.nextElementSibling;
        if (!referredError || !referredError.classList.contains('invalid-feedback')) {
            referredError = document.createElement('div');
            referredError.className = 'invalid-feedback';
            referredError.id = 'referredByError';
            referred.parentNode.appendChild(referredError);
        }
        referredError.textContent = '';
        referred.classList.remove('is-invalid');
        if (referred.value && referred.value.length > 100) {
            referredError.textContent = 'Input cannot exceed 100 characters.';
            referred.classList.add('is-invalid');
            hasError = true;
        } else if (referred.value && (!/^[A-Za-z\s]+$/.test(referred.value) || /[<>;=\',]/.test(referred.value))) {
            referredError.textContent = 'Letters and spaces only.';
            referred.classList.add('is-invalid');
            hasError = true;
        }
    }
    // Medical History: optional, but if filled, no <, >, ;, =, ', and max 100 chars
    const med = document.querySelector('textarea[name="medical_history"]');
    if (med) {
        let medError = med.nextElementSibling;
        if (!medError || !medError.classList.contains('invalid-feedback')) {
            medError = document.createElement('div');
            medError.className = 'invalid-feedback';
            medError.id = 'medicalHistoryError';
            med.parentNode.appendChild(medError);
        }
        medError.textContent = '';
        med.classList.remove('is-invalid');
        if (med.value && med.value.length > 100) {
            medError.textContent = 'Input cannot exceed 100 characters.';
            med.classList.add('is-invalid');
            hasError = true;
        } else if (med.value && /[<>;=\',]/.test(med.value)) {
            medError.textContent = 'Text only.';
            med.classList.add('is-invalid');
            hasError = true;
        }
    }
    // Purpose: optional, no <, >, ;, =, ', and max 100 chars
    const purpose = document.querySelector('textarea[name="purpose"]');
    if (purpose) {
        let purposeError = purpose.nextElementSibling;
        if (!purposeError || !purposeError.classList.contains('invalid-feedback')) {
            purposeError = document.createElement('div');
            purposeError.className = 'invalid-feedback';
            purposeError.id = 'purposeError';
            purpose.parentNode.appendChild(purposeError);
        }
        purposeError.textContent = '';
        purpose.classList.remove('is-invalid');
        if (purpose.value && purpose.value.length > 100) {
            purposeError.textContent = 'Input cannot exceed 100 characters.';
            purpose.classList.add('is-invalid');
            hasError = true;
        } else if ((showAllErrors || purpose.value) && /[<>;=\',]/.test(purpose.value)) {
            purposeError.textContent = 'Text only.';
            purpose.classList.add('is-invalid');
            hasError = true;
        }
    }
    // --- Dropdowns ---
    // Services: required, no <, >, ;, =, '
    const service = document.querySelector('select[name="services"]');
    if (service) {
        let serviceError = service.nextElementSibling;
        if (!serviceError || !serviceError.classList.contains('invalid-feedback')) {
            serviceError = document.createElement('div');
            serviceError.className = 'invalid-feedback';
            serviceError.id = 'serviceError';
            service.parentNode.appendChild(serviceError);
        }
        serviceError.textContent = '';
        service.classList.remove('is-invalid');
        if (!service.value || /[<>;=\',]/.test(service.value)) {
            serviceError.textContent = 'Please select a valid service.';
            service.classList.add('is-invalid');
            hasError = true;
        }
    }
    // Patient type: required, no <, >, ;, =, '
    const patientType = document.querySelector('select[name="patient_type"]');
    if (patientType) {
        let patientTypeError = patientType.nextElementSibling;
        if (!patientTypeError || !patientTypeError.classList.contains('invalid-feedback')) {
            patientTypeError = document.createElement('div');
            patientTypeError.className = 'invalid-feedback';
            patientTypeError.id = 'patientTypeError';
            patientType.parentNode.appendChild(patientTypeError);
        }
        patientTypeError.textContent = '';
        patientType.classList.remove('is-invalid');
        if (!patientType.value || /[<>;=\',]/.test(patientType.value)) {
            patientTypeError.textContent = 'Please select a valid patient type.';
            patientType.classList.add('is-invalid');
            hasError = true;
        }
    }
    // Branch: required, no <, >, ;, =, '
    const branch = document.querySelector('select[name="branch"]');
    if (branch) {
        let branchError = branch.nextElementSibling;
        if (!branchError || !branchError.classList.contains('invalid-feedback')) {
            branchError = document.createElement('div');
            branchError.className = 'invalid-feedback';
            branchError.id = 'branchError';
            branch.parentNode.appendChild(branchError);
        }
        branchError.textContent = '';
        branch.classList.remove('is-invalid');
        if (!branch.value || /[<>;=\',]/.test(branch.value)) {
            branchError.textContent = 'Please select a valid branch.';
            branch.classList.add('is-invalid');
            hasError = true;
        }
    }
    // Gender: already validated in validateContactEmailGender
    // --- Date and Time ---
    // Date: required, must be today or future, valid format
    const date = document.querySelector('input[name="appointment_date"]');
    if (date) {
        let dateError = date.nextElementSibling;
        if (!dateError || !dateError.classList.contains('invalid-feedback')) {
            dateError = document.createElement('div');
            dateError.className = 'invalid-feedback';
            dateError.id = 'dateError';
            date.parentNode.appendChild(dateError);
        }
        dateError.textContent = '';
        date.classList.remove('is-invalid');
        if (!date.value) {
            dateError.textContent = 'Please select a date.';
            date.classList.add('is-invalid');
            hasError = true;
        } else {
            const today = new Date();
            const selected = new Date(date.value);
            today.setHours(0,0,0,0);
            selected.setHours(0,0,0,0);
            if (selected < today) {
                dateError.textContent = 'Date cannot be in the past.';
                date.classList.add('is-invalid');
                hasError = true;
            }
        }
    }
    // Time: required, valid format (HH:MM)
    const time = document.querySelector('input[name="appointment_time"]');
    if (time) {
        let timeError = time.nextElementSibling;
        if (!timeError || !timeError.classList.contains('invalid-feedback')) {
            timeError = document.createElement('div');
            timeError.className = 'invalid-feedback';
            timeError.id = 'timeError';
            time.parentNode.appendChild(timeError);
        }
        timeError.textContent = '';
        time.classList.remove('is-invalid');
        if (!time.value || !/^([01]\d|2[0-3]):([0-5]\d)$/.test(time.value)) {
            timeError.textContent = 'Please select a valid time (HH:MM).';
            time.classList.add('is-invalid');
            hasError = true;
        }
    }
    return !hasError;
}
// --- END: Optional fields and dropdown/date/time validation ---

</script>

<script>
// Show available slots as buttons under the time input for easier selection
(function(){
    const dateEl = document.querySelector('input[name="appointment_date"]');
    const timeEl = document.querySelector('input[name="appointment_time"]');
    const branchEl = document.querySelector('select[name="branch"]');
    const wrap = document.getElementById('availableSlots');
    if(!dateEl || !timeEl || !wrap) return;

    function qs(params){ return Object.entries(params).map(([k,v])=> `${encodeURIComponent(k)}=${encodeURIComponent(v ?? '')}`).join('&'); }

    async function renderSlots(){
    const dateVal = dateEl.value;
    if(!dateVal){ wrap.style.display='none'; wrap.innerHTML=''; timeEl.value=''; return; }
        const branch = branchEl ? branchEl.value : '';
        try{
            const res = await fetch(`/api/appointments/available-slots?${qs({ appointment_date: dateVal, branch })}`, { headers: { 'Accept':'application/json' } });
            if(!res.ok) throw new Error('Failed');
            const data = await res.json();
            const slots = (data && data.slots) || [];
            if(!slots.length){
                wrap.style.display='block';
                wrap.innerHTML = '<div class="text-danger">No available slots for this date.</div>';
                timeEl.value = '';
                return;
            }
            wrap.style.display='block';
            wrap.innerHTML = '<div class="small text-muted mb-1">Available slots:</div>' +
                '<div class="d-flex flex-wrap gap-2">' +
                slots.map(s=>`<button type="button" class="btn btn-sm btn-outline-primary slot-btn" data-val="${s.value}">${s.label}</button>`).join('') +
                '</div>';
            const buttons = wrap.querySelectorAll('.slot-btn');
            function clearTimeError(){
                const err = document.getElementById('timeError') || (timeEl.parentNode && timeEl.parentNode.querySelector('.invalid-feedback'));
                if (err) err.textContent = '';
                timeEl.classList.remove('is-invalid');
            }
            function setActive(val){
                buttons.forEach(b=> b.classList.remove('btn-primary'));
                buttons.forEach(b=> b.classList.add('btn-outline-primary'));
                const active = Array.from(buttons).find(b=> b.getAttribute('data-val')===val);
                if(active){
                    active.classList.remove('btn-outline-primary');
                    active.classList.add('btn-primary');
                }
            }
            buttons.forEach(btn => {
                btn.addEventListener('click', function(){
                    const val = this.getAttribute('data-val');
                    timeEl.value = val;
                    setActive(val);
                    // clear any time errors if present
                    clearTimeError();
                });
            });
            // If current time isn't one of the available slots, select the first one by default
            const current = timeEl.value;
            const allowedValues = slots.map(s=>s.value);
            const defaultVal = allowedValues.includes(current) ? current : allowedValues[0];
            timeEl.value = defaultVal;
            setActive(defaultVal);
            clearTimeError();
        }catch(e){ wrap.style.display='none'; wrap.innerHTML=''; }
    }

    dateEl.addEventListener('change', renderSlots);
    if(branchEl) branchEl.addEventListener('change', renderSlots);
    if(dateEl.value){ renderSlots(); }
})();
</script>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="max-width:520px;margin:auto; border:2.5px solid #222;">
      <div class="modal-body text-center" style="padding:40px 30px;">
        <h2 id="contactModalLabel" style="font-weight:bold; border-bottom:2px solid #222; padding-bottom:10px; margin-bottom:20px; font-size:2.2rem;">Contact Us</h2>
        <h3 style="font-weight:bold; margin-bottom:18px; font-size:2rem;">Address</h3>
        <div style="font-weight:600; margin-bottom:10px; font-size:1.1rem;">
          Kamatage Trading Hearing<br>
          and Health Solution
        </div>
        <div style="margin-bottom:10px; font-size:1.1rem;">
          Kabi Building JV Seriña St.<br>
          Door# 5, 2nd floor Carmen, CDO
        </div>
        <h3 style="font-weight:bold; margin:22px 0 10px 0; font-size:2rem;">Phone Number</h3>
        <div style="margin-bottom:10px; font-size:1.1rem;">09351471786</div>
        <h3 style="font-weight:bold; margin:22px 0 10px 0; font-size:2rem;">Social Media</h3>
        <div style="margin-bottom:18px;">
          <a href="#" target="_blank"><img src="images/fb.png" alt="Facebook" style="width:44px;margin-right:18px;"></a>
          <a href="#" target="_blank"><img src="images/insta.jpg" alt="Instagram" style="width:44px;"></a>
        </div>
        <div style="font-weight:bold; font-size:1.1rem;">Kamatage Hearing and Health Solutions</div>
      </div>
    </div>
  </div>
</div>



</body>
</html>
</body>
</html>  		    