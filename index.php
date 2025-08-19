<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zip2cab</title>
    <link rel="canonical" href="https://zip2cab.com/" />
    <meta property="og:title" content="Zip2cab" />
    <meta property="og:image" content="https://zip2cab.com/Assets/Images/meta_logo.webp" />
    <meta property="og:image:secure_url" content="https://zip2cab.com/Assets/Images/meta_logo.webp" />
    <meta property="article:published_time" content="2023-09-06T02:41:55+00:00" />
    <meta name="twitter:description"
        content="Cab Driver for Your Journey, Our PriorityTrusted &amp; Affordable CabsCab Driver for Your Journey, Our PriorityFastest &amp; Friendly CabsCab Driver for Your Journey, Our PrioritySafe &amp; Secured Cabs About our CompanyHop in for a smooth journey Welcome to ZipTripGo, your trusted ride partner where we redefine travel. With 4 years of experience in the industry," />
    <meta name="twitter:image" content="https://zip2cab.com/Assets/Images/meta_logo.webp" />
    <meta name="description"
        content="Cab Driver for Your Journey, Our PriorityTrusted &amp; Affordable CabsCab Driver for Your Journey, Our PriorityFastest &amp; Friendly CabsCab Driver for Your Journey, Our PrioritySafe &amp; Secured Cabs About our CompanyHop in for a smooth journey Welcome to ZipTripGo, your trusted ride partner where we redefine travel. With 4 years of experience in the industry," />
    <meta property="og:description"
        content="Cab Driver for Your Journey, Our PriorityTrusted &amp; Affordable CabsCab Driver for Your Journey, Our PriorityFastest &amp; Friendly CabsCab Driver for Your Journey, Our PrioritySafe &amp; Secured Cabs About our CompanyHop in for a smooth journey Welcome to ZipTripGo, your trusted ride partner where we redefine travel. With 4 years of experience in the industry," />

    <link href="Assets/Images/icon.webp" rel="icon">
    <link href="Assets/Images/icon.webp" rel="apple-touch-icon">

    <link rel="stylesheet" href="Assets/CSS/Bootstrap.css">
    <link rel="stylesheet" href="Assets/CSS/navbar-1.css">
    <link rel="stylesheet" href="Assets/CSS/Style.css">
    <link rel="stylesheet" href="Assets/CSS/formStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>



    <script>
        // Global variables
        let currentStep = 1;
        let bookingData = {
            plan: 'airport',
            route: 'city-to-airport',
            vehicle: '',
            price: 0
        };

        // Initialize form
        document.addEventListener('DOMContentLoaded', function () {
            initializeForm();
            updateProgressBar();
        });

        function initializeForm() {
            // Set today's date as minimum and default
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            document.getElementById('booking-date').min = formattedDate;
            document.getElementById('booking-date').value = formattedDate;

            // Plan selection handlers
            document.querySelectorAll('.plan-option').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelectorAll('.plan-option').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    bookingData.plan = this.dataset.plan;
                    switchPlan(bookingData.plan);
                    validateStep1();
                });
            });

            // Route selection handlers
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('route-option')) {
                    e.preventDefault();
                    const parent = e.target.parentElement;
                    parent.querySelectorAll('.route-option').forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    bookingData.route = e.target.dataset.route;
                    updateLocationLabels();
                    validateStep1();
                }
            });

            // Vehicle selection handlers
            document.querySelectorAll('.vehicle-card').forEach(card => {
                card.addEventListener('click', function () {
                    document.querySelectorAll('.vehicle-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    bookingData.vehicle = this.dataset.vehicle;
                    bookingData.price = parseInt(this.dataset.price);
                    validateStep2();
                });
            });

            // Form input handlers
            document.addEventListener('input', function (e) {
                if (currentStep === 1) validateStep1();
                if (currentStep === 3) validateStep3();
            });
            document.addEventListener('change', function (e) {
                if (currentStep === 1) validateStep1();
            });

            // Step navigation handlers
            document.getElementById('next-step1').addEventListener('click', () => goToStep(2));
            document.getElementById('next-step2').addEventListener('click', () => goToStep(3));
            document.getElementById('back-step1').addEventListener('click', () => goToStep(1));
            document.getElementById('back-step2').addEventListener('click', () => goToStep(2));

            // Form submission handler
            document.getElementById('booking-form').addEventListener('submit', function (e) {
                e.preventDefault();
                submitBooking();
            });
        }

        function switchPlan(plan) {
            // Hide all route sections
            document.getElementById('airport-routes').classList.add('hidden');
            document.getElementById('local-routes').classList.add('hidden');
            document.getElementById('outstation-routes').classList.add('hidden');

            // Hide all location sections
            document.getElementById('airport-locations').classList.add('hidden');
            document.getElementById('local-locations').classList.add('hidden');
            document.getElementById('outstation-locations').classList.add('hidden');

            // Show relevant sections
            setTimeout(() => {
                if (plan === 'airport') {
                    document.getElementById('airport-routes').classList.remove('hidden');
                    document.getElementById('airport-locations').classList.remove('hidden');
                    bookingData.route = 'city-to-airport';
                } else if (plan === 'local') {
                    document.getElementById('local-routes').classList.remove('hidden');
                    document.getElementById('local-locations').classList.remove('hidden');
                    bookingData.route = 'pickup-drop';
                } else if (plan === 'outstation') {
                    document.getElementById('outstation-routes').classList.remove('hidden');
                    document.getElementById('outstation-locations').classList.remove('hidden');
                    bookingData.route = 'one-way';
                }

                updateLocationLabels();
            }, 50);
        }

        function updateLocationLabels() {
            const pickupLabel = document.getElementById('pickup-label');
            if (bookingData.plan === 'airport') {
                if (bookingData.route === 'city-to-airport') {
                    pickupLabel.textContent = 'Pickup Location:';
                    document.getElementById('pickup-location').placeholder = 'Enter pickup location (City)';
                } else {
                    pickupLabel.textContent = 'Drop Location:';
                    document.getElementById('pickup-location').placeholder = 'Enter drop location (City)';
                }
            }
        }

        function validateStep1() {
            let isValid = false;

            if (bookingData.plan === 'airport') {
                const location = document.getElementById('pickup-location').value.trim();
                isValid = location.length > 0;
            } else if (bookingData.plan === 'local') {
                const pickup = document.getElementById('local-pickup').value.trim();
                const drop = document.getElementById('local-drop').value.trim();
                isValid = pickup.length > 0 && drop.length > 0;
            } else if (bookingData.plan === 'outstation') {
                const destination = document.getElementById('outstation-destination').value.trim();
                isValid = destination.length > 0;
            }

            // Check date and time
            const date = document.getElementById('booking-date').value;
            const hour = document.getElementById('booking-hour').value;
            const minute = document.getElementById('booking-minute').value;
            const period = document.getElementById('booking-period').value;

            isValid = isValid && date && hour && minute && period;

            document.getElementById('next-step1').disabled = !isValid;
        }

        function validateStep2() {
            const isValid = bookingData.vehicle && bookingData.price > 0;
            document.getElementById('next-step2').disabled = !isValid;
        }

        function validateStep3() {
            const name = document.getElementById('user-name').value.trim();
            const email = document.getElementById('user-email').value.trim();
            const phone = document.getElementById('user-phone').value.trim();

            let isValid = true;

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));

            // Validate name
            if (name.length < 2) {
                showError('name', 'Name must be at least 2 characters long');
                isValid = false;
            }

            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            }

            // Validate phone
            const phoneRegex = /^[6-9]\d{9}$/;
            if (!phoneRegex.test(phone.replace(/[\s\-\+\(\)]/g, ''))) {
                showError('phone', 'Please enter a valid 10-digit Indian mobile number');
                isValid = false;
            }

            document.getElementById('submit-booking').disabled = !isValid;
            return isValid;
        }

        function showError(field, message) {
            const errorEl = document.getElementById(field + '-error');
            const inputEl = document.getElementById('user-' + field);

            if (errorEl) errorEl.textContent = message;
            if (inputEl) inputEl.classList.add('error');
        }

        function goToStep(step) {
            // Hide all step contents
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Show target step content
            document.getElementById(`step${step}-content`).classList.remove('hidden');

            currentStep = step;
            updateProgressBar();

            // Update booking summary for steps 2 and 3
            if (step === 2) {
                updateBookingSummary();
            } else if (step === 3) {
                updateFinalSummary();
            }
        }

        function updateProgressBar() {
            // Update step indicators
            for (let i = 1; i <= 3; i++) {
                const stepEl = document.getElementById(`step-${i}`);
                if (i < currentStep) {
                    stepEl.classList.add('completed');
                    stepEl.classList.remove('active');
                } else if (i === currentStep) {
                    stepEl.classList.add('active');
                    stepEl.classList.remove('completed');
                } else {
                    stepEl.classList.remove('active', 'completed');
                }
            }

            // Update progress line
            const progressLine = document.getElementById('progress-line');
            const progressWidth = ((currentStep - 1) / 2) * 100;
            progressLine.style.setProperty('--progress', progressWidth + '%');
        }

        function updateBookingSummary() {
            // Collect booking data
            bookingData.date = document.getElementById('booking-date').value;
            bookingData.time = `${document.getElementById('booking-hour').value}:${document.getElementById('booking-minute').value} ${document.getElementById('booking-period').value}`;

            if (bookingData.plan === 'airport') {
                bookingData.location = document.getElementById('pickup-location').value;
            } else if (bookingData.plan === 'local') {
                bookingData.pickup = document.getElementById('local-pickup').value;
                bookingData.drop = document.getElementById('local-drop').value;
            } else if (bookingData.plan === 'outstation') {
                bookingData.destination = document.getElementById('outstation-destination').value;
            }

            // Generate summary HTML
            let summaryHTML = `
                <div class="summary-row">
                    <span class="summary-label">Service Type:</span>
                    <span class="summary-value">${getPlanName(bookingData.plan)}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Route:</span>
                    <span class="summary-value">${getRouteName(bookingData.route)}</span>
                </div>
            `;

            if (bookingData.plan === 'airport') {
                const label = bookingData.route === 'city-to-airport' ? 'Pickup' : 'Drop';
                summaryHTML += `
                    <div class="summary-row">
                        <span class="summary-label">${label} Location:</span>
                        <span class="summary-value">${bookingData.location}</span>
                    </div>
                `;
            } else if (bookingData.plan === 'local') {
                summaryHTML += `
                    <div class="summary-row">
                        <span class="summary-label">Pickup:</span>
                        <span class="summary-value">${bookingData.pickup}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Drop:</span>
                        <span class="summary-value">${bookingData.drop}</span>
                    </div>
                `;
            } else if (bookingData.plan === 'outstation') {
                summaryHTML += `
                    <div class="summary-row">
                        <span class="summary-label">Destination:</span>
                        <span class="summary-value">${bookingData.destination}</span>
                    </div>
                `;
            }

            summaryHTML += `
                <div class="summary-row">
                    <span class="summary-label">Date & Time:</span>
                    <span class="summary-value">${formatDate(bookingData.date)} at ${bookingData.time}</span>
                </div>
            `;

            document.getElementById('booking-summary').innerHTML = summaryHTML;
        }

        function updateFinalSummary() {
            let summaryHTML = `
                <div class="summary-row">
                    <span class="summary-label">Service:</span>
                    <span class="summary-value">${getPlanName(bookingData.plan)}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Vehicle:</span>
                    <span class="summary-value">${getVehicleName(bookingData.vehicle)}</span>
                </div>
            `;

            if (bookingData.plan === 'airport') {
                const label = bookingData.route === 'city-to-airport' ? 'Pickup' : 'Drop';
                summaryHTML += `
                    <div class="summary-row">
                        <span class="summary-label">${label}:</span>
                        <span class="summary-value">${bookingData.location}</span>
                    </div>
                `;
            } else if (bookingData.plan === 'local') {
                summaryHTML += `
                    <div class="summary-row">
                        <span class="summary-label">Route:</span>
                        <span class="summary-value">${bookingData.pickup} → ${bookingData.drop}</span>
                    </div>
                `;
            } else if (bookingData.plan === 'outstation') {
                summaryHTML += `
                    <div class="summary-row">
                        <span class="summary-label">Destination:</span>
                        <span class="summary-value">${bookingData.destination}</span>
                    </div>
                `;
            }

            summaryHTML += `
                <div class="summary-row">
                    <span class="summary-label">Date & Time:</span>
                    <span class="summary-value">${formatDate(bookingData.date)} at ${bookingData.time}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total Amount:</span>
                    <span class="summary-value">₹${bookingData.price}</span>
                </div>
            `;

            document.getElementById('final-summary').innerHTML = summaryHTML;

            // Update hidden fields
            document.getElementById('hidden-plan').value = bookingData.plan;
            document.getElementById('hidden-route').value = bookingData.route;
            document.getElementById('hidden-vehicle').value = bookingData.vehicle;
            document.getElementById('hidden-price').value = bookingData.price;
            document.getElementById('hidden-time').value = bookingData.time;
        }

        function submitBooking() {
            if (!validateStep3()) return;

            // Show loading state
            const submitBtn = document.getElementById('submit-booking');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitBtn.disabled = true;

            // Submit form
            const formData = new FormData(document.getElementById('booking-form'));

            fetch('sumbit.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        document.getElementById('step3-content').classList.add('hidden');
                        document.getElementById('success-content').classList.remove('hidden');
                        currentStep = 4;
                        updateProgressBar();
                    } else {
                        alert('Error: ' + result.message);
                        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Booking';
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Booking';
                    submitBtn.disabled = false;
                });
        }

        // Helper functions
        function getPlanName(plan) {
            const names = {
                'airport': 'Airport Taxi',
                'local': 'Local Taxi',
                'outstation': 'Out Station'
            };
            return names[plan] || plan;
        }

        function getRouteName(route) {
            const names = {
                'city-to-airport': 'City to Airport',
                'airport-to-city': 'Airport to City',
                'pickup-drop': 'Pickup & Drop',
                'one-way': 'One Way'
            };
            return names[route] || route;
        }

        function getVehicleName(vehicle) {
            const names = {
                'sedan': 'Sedan',
                'suv': 'SUV',
                'luxury': 'Luxury Car',
                'minibus': 'Mini Bus',
                'tt': 'TT (Tempo Traveller)'
            };
            return names[vehicle] || vehicle;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-IN', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    </script>



    <?php include 'header.php' ?>


    <section class="banner">
        <div class="d-none d-lg-block">
            <img src="Assets/Images/Client_smiling.webp" alt="" class="img-fluid w-100 ">
        </div>
        <div class="d-block d-lg-none">
            <img src="Assets/Images/Client_smiling_mobile.webp" alt="" class="img-fluid mb-3 ">
        </div>


        <!-- <div class="container">
            <div class="row align-items-center ">

                <div class="col-lg-6">
                    <div class="banner-teaxt">
                        <h3>Ride Smart, Ride Safe</h3>
                        <h1>Bangalore’s No.1<br> <span> Cab Service ! </span> </h1>
                    </div>
                </div>
            </div>
        </div> -->
    </section>

    <section class="booknow-form">
        <div class="container-fluid ">
            <div class="row align-items-center ">
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    <div class="banner-teaxt">
                        <h3>Ride Smart, Ride Safe</h3>
                        <h1>Bangalore’s No.1<br> <span> Cab Service ! </span> </h1>
                    </div>
                    <div class="call-section">
                        <div class="call-text">Make a CALL for QUICK Booking!</div>
                        <a href="tel:9164109403" class="phone-number">
                            <div class="phone-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            +91-91641-09403
                        </a>
                    </div>

                </div>
                <div class="col-lg-6">
                    <div class="booking-container">
                        <div class="header">
                            <h2><i class="fas fa-taxi me-2"></i>Book Your Ride</h2>
                            <p class="mb-0">Fast, reliable, and affordable</p>
                        </div>


                        <div class="progress-bar-container">
                            <div class="progress-steps">
                                <div class="progress-line" id="progress-line"></div>
                                <div class="step active" id="step-1">
                                    1
                                    <div class="step-label">Service</div>
                                </div>
                                <div class="step" id="step-2">
                                    2
                                    <div class="step-label">Vehicle</div>
                                </div>
                                <div class="step" id="step-3">
                                    3
                                    <div class="step-label">Details</div>
                                </div>
                            </div>
                        </div>

                        <form method="post" action="sumbit.php" id="booking-form">

                            <div class="form-content">
                                <!-- Step 1: Service Selection -->
                                <div id="step1-content" class="step-content">
                                    <!-- Plan Selection -->
                                    <div class="section-title">Select a Plan:</div>
                                    <div class="plan-selector">
                                        <button type="button" class="plan-option active" data-plan="airport">
                                            <i class="fas fa-plane me-1"></i> Airport Taxi
                                        </button>
                                        <button type="button" class="plan-option" data-plan="local">
                                            <i class="fas fa-map-marker-alt me-1"></i> Local Taxi
                                        </button>
                                        <button type="button" class="plan-option" data-plan="outstation">
                                            <i class="fas fa-route me-1"></i> Out Station
                                        </button>
                                    </div>

                                    <!-- Airport Route Options -->
                                    <div id="airport-routes" class="fade-in">
                                        <div class="section-title">From to:</div>
                                        <div class="route-selector">
                                            <button type="button" class="route-option active"
                                                data-route="city-to-airport">
                                                <i class="fas fa-plane-departure me-1"></i> City to Airport
                                            </button>
                                            <button type="button" class="route-option" data-route="airport-to-city">
                                                <i class="fas fa-plane-arrival me-1"></i> Airport to City
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Local Routes -->
                                    <div id="local-routes" class="hidden">
                                        <div class="section-title">Route Type:</div>
                                        <div class="route-selector">
                                            <button type="button" class="route-option active" data-route="pickup-drop">
                                                <i class="fas fa-map-marked-alt me-1"></i> Pickup & Drop
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Outstation Routes -->
                                    <div id="outstation-routes" class="hidden">
                                        <div class="section-title">Journey Type:</div>
                                        <div class="route-selector">
                                            <button type="button" class="route-option active" data-route="one-way">
                                                <i class="fas fa-long-arrow-alt-right me-1"></i> One Way
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Location Inputs -->
                                    <div class="location-group">
                                        <!-- Airport Service Locations -->
                                        <div id="airport-locations" class="fade-in">
                                            <div class="section-title" id="pickup-label">Pickup Location:</div>
                                            <div class="location-input-wrapper">
                                                <i class="fas fa-map-marker-alt location-icon"></i>
                                                <input type="text" class="location-input" id="pickup-location"
                                                    name="pickup_location" placeholder="Enter pickup location">
                                            </div>
                                        </div>

                                        <!-- Local Service Locations -->
                                        <div id="local-locations" class="hidden">
                                            <div class="section-title">Pickup Location:</div>
                                            <div class="location-input-wrapper">
                                                <i class="fas fa-circle location-icon" style="color: #4caf50;"></i>
                                                <input type="text" class="location-input" id="local-pickup"
                                                    name="local_pickup" placeholder="Enter pickup location">
                                            </div>
                                            <div class="location-input-wrapper">
                                                <i class="fas fa-map-marker-alt location-icon"
                                                    style="color: #f44336;"></i>
                                                <input type="text" class="location-input" id="local-drop"
                                                    name="local_drop" placeholder="Enter drop location">
                                            </div>
                                        </div>

                                        <!-- Outstation Service Locations -->
                                        <div id="outstation-locations" class="hidden">
                                            <div class="section-title">Destination:</div>
                                            <div class="location-input-wrapper">
                                                <i class="fas fa-map-marker-alt location-icon"></i>
                                                <input type="text" class="location-input" id="outstation-destination"
                                                    name="outstation_destination" placeholder="Enter destination city">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date and Time Selection -->
                                    <div class="datetime-row">
                                        <div class="datetime-group">
                                            <label class="datetime-label">Select Date</label>
                                            <input type="date" class="datetime-input" id="booking-date"
                                                name="booking_date" min="">
                                        </div>
                                        <div class="datetime-group">
                                            <label class="datetime-label">Select Time</label>
                                            <div class="time-selectors">
                                                <select class="datetime-input" id="booking-hour" name="booking_hour">
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06" selected>06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                </select>
                                                <select class="datetime-input" id="booking-minute"
                                                    name="booking_minute">
                                                    <option value="00" selected>00</option>
                                                    <option value="15">15</option>
                                                    <option value="30">30</option>
                                                    <option value="45">45</option>
                                                </select>
                                                <select class="datetime-input" id="booking-period"
                                                    name="booking_period">
                                                    <option value="AM">AM</option>
                                                    <option value="PM" selected>PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn-primary" id="next-step1" disabled>
                                        <i class="fas fa-arrow-right me-2"></i>Next - Select Vehicle
                                    </button>
                                </div>

                                <!-- Step 2: Vehicle Selection -->
                                <div id="step2-content" class="step-content hidden">
                                    <div class="section-title">Your Journey Details:</div>
                                    <div class="booking-summary" id="booking-summary">
                                        <!-- Summary will be populated by JavaScript -->
                                    </div>

                                    <div class="section-title">Choose Your Vehicle:</div>
                                    <div class="vehicle-grid">
                                        <div class="vehicle-card" data-vehicle="sedan" data-price="899">
                                            <div class="selection-badge">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="vehicle-icon">
                                                <i class="fas fa-car"></i>
                                            </div>
                                            <div class="vehicle-name">SEDAN AC</div>
                                            <div class="vehicle-features">4 Seater • AC • Comfortable</div>
                                            <div class="vehicle-price">₹899</div>
                                            <small>Toll extra | T&C Apply </small>
                                            
                                        </div>

                                        <div class="vehicle-card" data-vehicle="suv" data-price="1599">
                                            <div class="selection-badge">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="vehicle-icon">
                                                <i class="fas fa-truck"></i>
                                            </div>
                                            <div class="vehicle-name">ERTICA SUV AC</div>
                                            <div class="vehicle-features">7 Seater • AC • Spacious</div>
                                            <div class="vehicle-price">₹1599</div>
                                            <small>Toll extra | T&C Apply </small>
                                        </div>

                                        <div class="vehicle-card" data-vehicle="luxury" data-price="1799">
                                            <div class="selection-badge">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="vehicle-icon">
                                                <i class="fas fa-gem"></i>
                                            </div>
                                            <div class="vehicle-name">INNOVA AC</div>
                                            <div class="vehicle-features">4+1 Seater • Premium • Luxury</div>
                                            <div class="vehicle-price">₹1799</div>
                                            <small>Toll extra | T&C Apply </small>
                                        </div>

                                        <div class="vehicle-card" data-vehicle="minibus" data-price="1999">
                                            <div class="selection-badge">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="vehicle-icon">
                                                <i class="fas fa-bus"></i>
                                            </div>
                                            <div class="vehicle-name">INNOVA CRYSTA AC</div>
                                            <div class="vehicle-features">12 Seater • AC • Group Travel</div>
                                            <div class="vehicle-price">₹1999</div>
                                            <small>Toll extra | T&C Apply </small>
                                        </div>

                                        <div class="vehicle-card" data-vehicle="tt" data-price="4100">
                                            <div class="selection-badge">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="vehicle-icon">
                                                <i class="fas fa-taxi"></i>
                                            </div>
                                            <div class="vehicle-name">TT (Tempo Traveller)</div>
                                            <div class="vehicle-features">3 Wheeler • Budget Friendly</div>
                                            <div class="vehicle-price">₹4100</div>
                                            <small>Toll extra | T&C Apply </small>
                                        </div>
                                    </div>

                                    <button type="button" class="btn-secondary" id="back-step1">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </button>
                                    <button type="button" class="btn-primary" id="next-step2" disabled>
                                        <i class="fas fa-arrow-right me-2"></i>Next - Enter Details
                                    </button>
                                </div>

                                <!-- Step 3: User Details -->
                                <div id="step3-content" class="step-content hidden">
                                    <div class="section-title">Booking Summary:</div>
                                    <div class="booking-summary" id="final-summary">
                                        <!-- Final summary will be populated by JavaScript -->
                                    </div>

                                    <div class="section-title">Your Details:</div>
                                    <div class="user-details-form row " id="user-form">
                                        <div class="form-group col-lg-6 ">
                                            <label class="form-label" for="user-name">Full Name *</label>
                                            <input type="text" class="form-input" id="user-name" name="name" required>
                                            <div class="error-message" id="name-error"></div>
                                        </div>

                                        <div class="form-group col-lg-6 ">
                                            <label class="form-label" for="user-email">Email Address *</label>
                                            <input type="email" class="form-input" id="user-email" name="email"
                                                required>
                                            <div class="error-message" id="email-error"></div>
                                        </div>

                                        <div class="form-group col-lg-6 ">
                                            <label class="form-label" for="user-phone">Phone Number *</label>
                                            <input type="tel" class="form-input" id="user-phone" name="phone" required>
                                            <div class="error-message" id="phone-error"></div>
                                        </div>

                                        <div class="form-group col-lg-6 ">
                                            <label class="form-label" for="user-message">Special Requirements
                                                (Optional)</label>
                                            <textarea class="form-input" id="user-message" name="message" rows="2"
                                                placeholder="Any special requests or requirements..."></textarea>
                                        </div>
                                    </div>

                                    <!-- Hidden fields for booking data -->
                                    <input type="hidden" name="plan" id="hidden-plan">
                                    <input type="hidden" name="route" id="hidden-route">
                                    <input type="hidden" name="vehicle" id="hidden-vehicle">
                                    <input type="hidden" name="price" id="hidden-price">
                                    <input type="hidden" name="booking_time" id="hidden-time">

                                    <!-- Google reCAPTCHA -->
                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="6Ldo_KErAAAAAPMEbavFVfyPpHzZ5CYi1ZMZJPuQ"
                                            data-callback="enableSubmit"></div>
                                    </div>

                                    <button type="button" class="btn-secondary" id="back-step2">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </button>
                                    
                                    <button type="submit" class="btn-primary" id="submit-booking">
                                        <i class="fas fa-check me-2"></i>Confirm Booking
                                    </button>
                                </div>

                                <!-- Success Message -->
                                <div id="success-content" class="step-content hidden">
                                    <div class="success-message">
                                        <div class="success-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <h3>Booking Confirmed!</h3>
                                        <p>Thank you for choosing our service. We'll contact you shortly to confirm
                                            your
                                            ride details.
                                        </p>
                                        <button type="button" class="btn-primary" onclick="location.reload()">
                                            <i class="fas fa-plus me-2"></i>Book Another Ride
                                        </button>
                                    </div>
                                </div>

                                <!-- Call Section -->

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="agency-banglore scroll-section" id="WhatWeDo">
        <div class="sec-title">
            <h4>Leading Cab Services Agency in Bangalore</h4>
            <h2>Reliable & On-Time Service</h2>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="agency-banglore-details pb-2 pb-lg-0 mb-3 mb-lg-0 ">
                        <img src="Assets/Images/Airport_Trasfer.webp" alt="" class="img-fluid">
                        <h3>Airport Trasfer</h3>
                        <p class="mb-0">Stress-free drop & pickup</pc>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="agency-banglore-details pb-2 pb-lg-0 mb-3 mb-lg-0 ">
                        <img src="Assets/Images/OutStation_Trips.webp" alt="" class="img-fluid">
                        <h3>Outstation Trips</h3>
                        <p class="mb-0">Explore beyond city limits <br>
                            with ease</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="agency-banglore-details pb-2 mb-lg-0 ">
                        <img src="Assets/Images/Local_Trips.webp" alt="" class="img-fluid">
                        <h3>Local Trips</h3>
                        <p class="mb-0">Quick, smooth travel <br>
                            within the city</p>
                    </div>
                </div>
            </div>
        </div>


    </section>

    <section class="airport-cabs">

        <div class="row flex-column-reverse flex-lg-row">
            <div class="col-lg-3">
                <div class="d-none d-lg-block">
                    <img src="Assets/Images/Airport-taxi.webp" alt="" class="img-fluid w-100 ">
                </div>
                <div class="d-block d-lg-none">
                    <img src="Assets/Images/Airport-taxi.webp.jpg" alt="" class="img-fluid w-100  mb-3 ">
                </div>

            </div>
            <div class="col-lg-9">
                <div class="airport-cabs-description">
                    <h2 class="fw-bold">Airport Transfer Fares</h2>
                    <p>Start or end your journey with our reliable airport transfer services. We ensure <br> timely
                        pickups & drop-offs, so you never have to worry about missing a flight or waiting around.</p>
                    <br>
                </div>
                <div class="row">
                    <div class="pricing-cards">
                        <div class="pricing-card sedan">
                            <div class="card-top">
                                <div class="vehicle-icon">
                                    <img src="Assets/Images/Cars/SEDAN_AC.webp" alt="" class="img-fluid">
                                </div>
                                <h3 class="vehicle-name">Sedan AC</h3>
                            </div>
                            <div class="card-body">
                                <div class="price">₹899/-</div>
                                <div class="route-info">From/To Airport <br> <span>(T&C Apply)</span> </div>
                                <div class="toll-info">
                                    <i class="fas fa-road"></i> Toll Charges Extra
                                </div>
                                <?php include 'cta.php' ?>
                            </div>
                        </div>

                        <div class="pricing-card ertica">
                            <div class="card-top">
                                <div class="vehicle-icon">
                                    <img src="Assets/Images/Cars/ERTICA_SUV_AC.webp" alt="" class="img-fluid">
                                </div>
                                <h3 class="vehicle-name">ERTICA SUV AC</h3>
                            </div>
                            <div class="card-body">
                                <div class="price">₹1599/-</div>
                                <div class="route-info">From/To Airport <br> <span>(T&C Apply)</span> </div>
                                <div class="toll-info">
                                    <i class="fas fa-road"></i> Toll Charges Extra
                                </div>
                                <?php include 'cta.php' ?>
                            </div>
                        </div>

                        <div class="pricing-card innova">
                            <div class="card-top">
                                <div class="vehicle-icon">
                                    <img src="Assets/Images/Cars/Innova.webp" alt="" class="img-fluid">
                                </div>
                                <h3 class="vehicle-name">INNOVA AC</h3>
                            </div>
                            <div class="card-body">
                                <div class="price">₹1799/-</div>
                                <div class="route-info">From/To Airport <br> <span>(T&C Apply)</span> </div>
                                <div class="toll-info">
                                    <i class="fas fa-road"></i> Toll Charges Extra
                                </div>
                                <?php include 'cta.php' ?>
                            </div>
                        </div>
                        <div class="pricing-card cresta">
                            <div class="card-top">
                                <div class="vehicle-icon">
                                    <img src="Assets/Images/Cars/INNOVA_CRESTA_AC.webp" alt="" class="img-fluid">
                                </div>
                                <h3 class="vehicle-name">INNOVA CRYSTA AC</h3>
                            </div>
                            <div class="card-body">
                                <div class="price">₹1999/-</div>
                                <div class="route-info">From/To Airport <br> <span>(T&C Apply)</span> </div>
                                <div class="toll-info">
                                    <i class="fas fa-road"></i> Toll Charges Extra
                                </div>
                                <?php include 'cta.php' ?>
                            </div>
                        </div>
                        <div class="pricing-card cresta">
                            <div class="card-top">
                                <div class="vehicle-icon">
                                    <img src="Assets/Images/Cars/TT.webp" alt="" class="img-fluid">
                                </div>
                                <h3 class="vehicle-name">TT AC</h3>
                            </div>
                            <div class="card-body">
                                <div class="price">₹4199/-</div>
                                <div class="route-info">From/To Airport <br> <span>(T&C Apply)</span> </div>
                                <div class="toll-info">
                                    <i class="fas fa-road"></i> Toll Charges Extra
                                </div>
                                <?php include 'cta.php' ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tariff-details">
                    <a href="tel:9164109403">
                        <h2>Know your waiting Time & Tarif : +91 91641 09403</h2>
                    </a>
                </div>
            </div>
        </div>

    </section>







    <section class="choose-zip2cab scroll-section" id="Services">
        <div class="sec-title">
            <h2>Choose Bangalore’s <br>
                <span>Trusted Cabs Services</span>
            </h2>
        </div>
        <div class="container">
            <div class="row align-items-center ">
                <div class="col-lg-4">
                    <div class="choose-zip2cab-service">
                        <img src="Assets/Images/Cars/SEDAN_AC.webp" alt="" class="img-fluid">
                        <h5>SEDAN AC</h5>
                        <p>One Way Tariff - Price 14 Rs/KM <br>
                            Round Trip Tariff - Price 13 Rs/KM<br> <span>(T&C Apply)</span></p>

                        <div class="choose-zip2cab-tariff-details">
                            <div class="car">
                                <i class="fa-solid fa-car me-1 "></i> SEDAN
                            </div>
                            <div class="car">
                                <i class="fa-solid fa-users me-1"></i> 4+1
                            </div>
                            <div class="car">
                                <i class="fas fa-snowflake me-1"></i> AC
                            </div>
                        </div>
                        <?php include 'cta.php' ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="choose-zip2cab-service">
                        <img src="Assets/Images/Cars/ERTICA_SUV_AC.webp" alt="" class="img-fluid">
                        <h5>ERTICA SUV AC</h5>
                        <p>One Way Tariff - Price 18 Rs/KM <br>
                            Round Trip Tariff - Price 16 Rs/KM<br> <span>(T&C Apply)</span></p>

                        <div class="choose-zip2cab-tariff-details">
                            <div class="car">
                                <i class="fa-solid fa-car me-1 "></i> ERTICA
                            </div>
                            <div class="car">
                                <i class="fa-solid fa-users me-1"></i> 6+1
                            </div>
                            <div class="car">
                                <i class="fas fa-snowflake me-1"></i> AC
                            </div>
                        </div>
                        <?php include 'cta.php' ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="choose-zip2cab-service">
                        <img src="Assets/Images/Cars/Innova.webp" alt="" class="img-fluid">
                        <h5>INNOVA AC</h5>
                        <p>One Way Tariff - Price 20 Rs/KM <br>
                            Round Trip Tariff - Price 18 Rs/KM<br> <span>(T&C Apply)</span></p>

                        <div class="choose-zip2cab-tariff-details">
                            <div class="car">
                                <i class="fa-solid fa-car me-1 "></i> INNOVA
                            </div>
                            <div class="car">
                                <i class="fa-solid fa-users me-1"></i> 7+1
                            </div>
                            <div class="car">
                                <i class="fas fa-snowflake me-1"></i> AC
                            </div>
                        </div>
                        <?php include 'cta.php' ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-4">
                    <div class="choose-zip2cab-service">
                        <img src="Assets/Images/Cars/INNOVA_CRESTA_AC.webp" alt="" class="img-fluid">
                        <h5>INNOVA CRYSTA AC</h5>
                        <p>One Way Tariff - Price 22 Rs/KM <br>
                            Round Trip Tariff - Price 20 Rs/KM<br> <span>(T&C Apply)</span></p>

                        <div class="choose-zip2cab-tariff-details">
                            <div class="car">
                                <i class="fa-solid fa-car me-1 "></i> INNOVA
                            </div>
                            <div class="car">
                                <i class="fa-solid fa-users me-1"></i> 7+1
                            </div>
                            <div class="car">
                                <i class="fas fa-snowflake me-1"></i> AC
                            </div>
                        </div>
                        <?php include 'cta.php' ?>
                    </div>
                </div>
                <div class="col-lg-4 ">
                    <div class="choose-zip2cab-service">
                        <img src="Assets/Images/Cars/TT.webp" alt="" class="img-fluid">
                        <h5>TEMPO TRAVELLER AC </h5>
                        <p> <br>
                            Round Trip Tariff - Price 22 Rs/KM <br> <span>(T&C Apply)</span> </p>

                        <div class="choose-zip2cab-tariff-details">
                            <div class="car">
                                <i class="fa-solid fa-car me-1 "></i> TT
                            </div>
                            <div class="car">
                                <i class="fa-solid fa-users me-1"></i> 12+1
                            </div>
                            <div class="car">
                                <i class="fas fa-snowflake me-1"></i> AC
                            </div>
                        </div>
                        <?php include 'cta.php' ?>
                    </div>
                </div>
                <div class="col-lg-4">

                    <div class="choose-zip2cab-service vehicle-card direct-card  ">
                        <!-- <h5> <i class="fa-solid fa-plane-departure"></i> AIRPORT TRANSFER</h5>
                        <p>Reliable airport pickup and drop services.<br>On-time | Comfortable | Affordable</p>
                        <a href="tel:+919164109403" class="direct-call"><i class="fa-solid fa-phone-volume"></i> Call
                            Now</a>
                        <a href="https://wa.me/919164109403" class="direct-whatsapp" target="_blank"><i
                                class="fab fa-whatsapp"></i> WhatsApp Now</a> -->
                        <h5><i class="fas fa-city vehicle-icon"></i> City Packages</h5>
                        <div class="city-card">


                            <div class="card-section">
                                <div class="icon-text"> Sedan (4+1)</div>
                                <div class="time-row">
                                    <div><i class="fas fa-clock"></i> 4Hrs & 40Km: ₹1200</div>
                                    <div><i class="fas fa-clock"></i> 8Hrs & 80Km: ₹2300</div>
                                    <div><i class="fas fa-clock"></i> 12Hrs & 120Km: ₹3000</div>
                                </div>
                                <div class="extra-row mt-2">
                                    <div><i class="fas fa-road"></i> Extra KMs: ₹16</div>
                                    <div><i class="fas fa-hourglass-half"></i> Extra Hour: ₹160</div>
                                </div>
                            </div>

                            <hr>

                            <div class="card-section">
                                <div class="icon-text"> Ertiga (6+1)</div>
                                <div class="time-row">
                                    <div><i class="fas fa-clock"></i> 4Hrs & 40Km: ₹1600</div>
                                    <div><i class="fas fa-clock"></i> 8Hrs & 80Km: ₹2800</div>
                                    <div><i class="fas fa-clock"></i> 12Hrs & 120Km: ₹3600</div>
                                </div>
                                <div class="extra-row mt-2">
                                    <div><i class="fas fa-road"></i> Extra KMs: ₹18</div>
                                    <div><i class="fas fa-hourglass-half"></i> Extra Hour: ₹180</div>
                                </div>
                            </div>

                            <hr>

                            <div class="card-section">
                                <div class="icon-text"> Innova Crysta (7+1)</div>
                                <div class="time-row">
                                    <div><i class="fas fa-clock"></i> 4Hrs & 40Km: ₹2000</div>
                                    <div><i class="fas fa-clock"></i> 8Hrs & 80Km: ₹3500</div>
                                    <div><i class="fas fa-clock"></i> 12Hrs & 120Km: ₹4500</div>
                                </div>
                                <div class="extra-row mt-2">
                                    <div><i class="fas fa-road"></i> Extra KMs: ₹22</div>
                                    <div><i class="fas fa-hourglass-half"></i> Extra Hour: ₹220</div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>


    <section class="experience-zip2code">
        <div class="container">
            <div class="row ">
                <div class="col-lg-6">

                </div>
                <div class="col-lg-6">
                    <div class="experience-zip2code-details">
                        <h2>Experience the <span>Zip2Cab <br>
                                Difference</span> </h2>
                        <p>Thousands of happy riders trust Zip2Cab daily for punctuality, safety, and a smooth
                            travel
                            experience.
                            We’re more than a cab service we’re your everyday
                            travel partner.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="best-cab-service  scroll-section ">
        <div class="sec-title">
            <h2>We Offer the best <br>
                <span>Cabs Service for you.</span>
            </h2>
            <p>No delays. No surprises. Just seamless rides</p>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="best-cab-service-payment best-cab-service-keys  mb-3 mb-lg-0">
                        <img src="Assets/Images/Rupee-icon.webp" alt="" class="img-fluid">
                        <h4>Support All Payment</h4>
                        <img src="Assets/Images/Payment_options.webp" alt="" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="best-cab-service-safety best-cab-service-keys  mb-3 mb-lg-0">
                        <img src="Assets/Images/Safety_icon.webp" alt="" class="img-fluid">
                        <h4>Safety First</h4>
                        <p>Your comfort and safety come before everything else.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="best-cab-service-suport best-cab-service-keys">
                        <img src="Assets/Images/Support_icon.webp" alt="" class="img-fluid">
                        <h4>24/7 Support</h4>
                        <p>Anytime, anywhere we’re <br>
                            here to help.</p>
                    </div>
                </div>
            </div>
            <div class="smart-safe-text">
                <h2>Ride Smart, Ride Safe</h2>
            </div>
        </div>
    </section>
    <section class="about-zip scroll-section" id="AboutZip2Cab">
        <div class="container">
            <div class="row align-items-center  ">
                <div class="col-lg-5">
                    <img src="Assets/Images/Shake_hand_together.webp" alt="" class="img-fluid">
                </div>
                <div class="col-lg-7">
                    <div class="about-zip-content">
                        <h4>About Zip2cab</h4>
                        <h3>Trusted Cab Services </h3>
                        <h2>In Whitefield, Bangalore</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center ">
                <div class="col-lg-10">
                    <p>Welcome to Zip2cab your <strong>trusted ride partner in Bangalore, proudly serving Whitefield
                            and
                            beyond</strong>. With over 4 years of experience in the industry, we’ve built a
                        reputation
                        for excellence, reliability, and professionalism. Whether you're commuting to work,
                        exploring
                        the vibrant streets of Bangalore, or traveling from Whitefield to any part of the city, our
                        premium services ensure a seamless and enjoyable journey every time. At Zip2cab, you’re not
                        just
                        a passenger you’re our top priority. Our mission is to provide safe, reliable, and
                        eco-friendly
                        travel solutions that enhance your journey and exceed expectations. We strive to be the
                        leading
                        Cab Services agency in Bangalore, known for our unwavering commitment to customer
                        satisfaction
                        and sustainable practices.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="faq-section scroll-section" id="FAQ">
        <div class="sec-title">
            <h2>Have questions? <br>
                We’ve got <span>answers !</span>
            </h2>
        </div>
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-lg-8">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    What is Zip2Cab?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    Zip2Cab is a reliable cab service offering city rides, airport transfers, outstation
                                    travel, and hourly rentals. We prioritize safety, punctuality, and customer
                                    satisfaction.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    How do I book a ride?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>Booking a ride with Zip2Cab is easy and convenient.</strong> Simply visit
                                    our website https://zip2cab.com, choose your pickup and drop-off locations, select
                                    the type of cab (Sedan, SUV, etc.), and confirm your ride. You can also call our
                                    customer service for assistance with bookings. Zip2Cab offers a hassle-free and
                                    reliable taxi booking experience online.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Do you provide airport pickup and drop services?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>Yes, Zip2Cab offers reliable airport pickup and drop services across major
                                        cities.</strong> Whether you're arriving at or departing from the airport, our
                                    drivers ensure timely service and comfort. Book an airport cab in advance via
                                    https://zip2cab.com to avoid last-minute delays and travel stress-free.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Can I pre-book a cab in advance?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>Absolutely! Zip2Cab allows you to pre-book your cab ride in
                                        advance.</strong> Whether it's for an early morning airport transfer, an office
                                    commute, or an outstation trip, you can schedule your ride at your convenience.
                                    Visit https://zip2cab.com and choose your desired date and time to secure your
                                    booking.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    What are your payment options?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>Zip2Cab accepts multiple payment methods to make your ride smooth and
                                        cashless.</strong> You can pay using UPI, credit/debit cards, digital wallets,
                                    or cash at the end of your ride. Our flexible payment system ensures a seamless and
                                    user-friendly experience every time you book with Zip2Cab.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    Are your drivers verified?
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong> Yes, all Zip2Cab drivers are professionally verified and trained.</strong>
                                    We prioritize your safety and comfort. Our drivers undergo thorough background
                                    checks, driving tests, and customer service training to ensure you have a secure and
                                    pleasant journey.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    Do you offer outstation rides?
                                </button>
                            </h2>
                            <div id="collapseSeven" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>Yes, Zip2Cab offers comfortable and affordable outstation cab
                                        services.</strong> Whether it’s a weekend getaway or a business trip, you can
                                    book one-way or round-trip rides to nearby cities. Visit https://zip2cab.com and
                                    select “Outstation” to plan your journey today.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                    Is Zip2Cab available in my area?
                                </button>
                            </h2>
                            <div id="collapseEight" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>Zip2Cab is rapidly expanding and available in multiple cities across
                                        India.</strong> To check if Zip2Cab services are available in your area, simply
                                    enter your location on our website https://zip2cab.com. If we’re not there yet, stay
                                    tuned—we're coming soon!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <footer class="footer scroll-section" id="Contact">
        <div class="container">
            <div class="row">
                <div class="footer-top">
                    <h2>Welcome to Zip2cab, your trusted ride partner<br>where we redefine travel.</h2>
                </div>
                <div class="footer-columns">
                    <div class="footer-col">
                        <h4>Company<span></span></h4>
                        <ul>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Refund Policy</a></li>
                            <li><a href="#">Terms & Conditions</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Services<span></span></h4>
                        <ul>
                            <li><a href="#">AIRPORT TRANSFER</a></li>
                            <li><a href="#">OUTSTATION TRIP</a></li>
                            <li><a href="#">LOCAL TRIP</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Address<span></span></h4>
                        <p><strong>Whitefield, Bengaluru</strong></p>
                    </div>
                    <div class="footer-col">
                        <h4>Contact<span></span></h4>
                        <p><strong>PHONE NUMBER</strong> : +91 9164109403</p>
                        <p><strong>EMAIL ADDRESS</strong> : zip2cab@gmail.com</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>Designed by E9DS</p>
                </div>
                <div class="col-lg-12">
                    <div class="text-center">
                        <img src="Assets/Images/Logo_color.webp" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <button id="backToTop" title="Back to Top">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <!-- Outer Tyre -->
            <circle cx="100" cy="100" r="90" fill="#222" stroke="#000" stroke-width="8" />

            <!-- Tread Pattern -->
            <circle cx="100" cy="100" r="80" fill="none" stroke="#555" stroke-width="8" stroke-dasharray="15,15" />

            <!-- Rim -->
            <circle cx="100" cy="100" r="55" fill="#333" stroke="#888" stroke-width="6" />

            <!-- Hub -->
            <circle cx="100" cy="100" r="30" fill="#ccc" stroke="#999" stroke-width="4" />

            <!-- Bolts -->
            <circle cx="100" cy="75" r="4" fill="#666" />
            <circle cx="125" cy="100" r="4" fill="#666" />
            <circle cx="100" cy="125" r="4" fill="#666" />
            <circle cx="75" cy="100" r="4" fill="#666" />

            <!-- Larger Up Arrow -->
            <polygon points="100,70 85,100 92,100 92,125 108,125 108,100 115,100" fill="#222" />
        </svg>
    </button>

    
    <div class="floating-contact">
        <a href="tel:+919164109403" class="call-btn" title="Call Us">
            <i class="fa fa-phone"></i>
        </a>
        <a href="https://wa.me/919164109403" class="whatsapp-btn" title="Chat on WhatsApp" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>






    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="Assets/JS/script.js"></script>

    <script>
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const offcanvasEl = document.getElementById('offcanvasNavbar');
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                bsOffcanvas.hide();
            });
        });
    </script>

    <script>
        function toggleTooltip(button) { 
            document.querySelectorAll('.tooltip-box').forEach(tip => {
                if (tip !== button.nextElementSibling) tip.style.display = 'none';
            });

            const tooltip = button.nextElementSibling;
            tooltip.style.display = tooltip.style.display === 'block' ? 'none' : 'block';
        } 
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.vehicle-card')) {
                document.querySelectorAll('.tooltip-box').forEach(tip => tip.style.display = 'none');
            }
        });
    </script>





</body>

</html>



