<?php
require_once __DIR__ . '/includes/functions.php';
start_session();

$errorMessages = [
    '1' => 'Please complete all required fields.',
    '2' => 'Password and confirmation do not match.',
    '3' => 'Password must contain between 6 and 255 characters.',
    '4' => 'Please check your details and enter valid information.',
    '5' => 'The profile photo could not be uploaded. Please use a JPG, PNG, WEBP or GIF image.',
    '6' => 'Your form could not be saved. Please try again later.',
];
$errorCode = is_string($_GET['error'] ?? null) ? $_GET['error'] : '';
?>

<?php include_once __DIR__ . '/head.php'; ?>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #f5f8fb 0%, #eefaf4 100%);
            color: #1f2937;
        }

        .participate-wrap {
            padding: 80px 0;
        }

        .participate-card {
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(148,163,184,0.18);
            box-shadow: 0 25px 55px rgba(15, 23, 42, 0.08);
        }

        .participate-header {
            background: linear-gradient(135deg, #0f172a, #0ea5e9);
            color: #fff;
            padding: 30px 32px;
        }

        .participate-header h2 {
            margin: 0;
            font-size: clamp(1.9rem, 3vw, 2.8rem);
            font-weight: 800;
        }

        .participate-header p {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
        }

        .participate-form {
            padding: 30px;
        }

        .section-title {
            margin: 28px 0 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 1.08rem;
            font-weight: 800;
            color: #0f172a;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #334155;
        }

        .form-control,
        .form-select,
        textarea {
            width: 100%;
            min-height: 48px;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #dbeafe;
            background: #f8fafc;
            color: #0f172a;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            border-color: #38bdf8;
            box-shadow: none;
            background: #fff;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .upload-box {
            border: 1.5px dashed #93c5fd;
            border-radius: 18px;
            background: linear-gradient(180deg, #f8fafc, #eff6ff);
            text-align: center;
            padding: 20px;
            color: #0f172a;
            font-weight: 600;
        }

        .upload-box input {
            display: none;
        }

        .upload-box label {
            display: block;
            margin: 0;
            cursor: pointer;
        }

        .helper-text {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 8px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin: 0;
        }

        .submit-btn {
            background: linear-gradient(135deg, #0ea5e9, #0f766e);
            border: none;
            border-radius: 12px;
            color: white;
            padding: 14px 26px;
            font-weight: 700;
            min-width: 220px;
        }

        .submit-btn:hover {
            opacity: 0.96;
        }

        @media (max-width: 767px) {
            .participate-wrap {
                padding: 40px 0;
            }

            .participate-header {
                padding: 24px 20px;
            }

            .participate-form {
                padding: 20px 16px;
            }
        }
    </style>

    <main class="participate-wrap">
        <div class="container">
            <div class="participate-card">
                <div class="participate-header">
                    <h2>Participate With Us</h2>
                    <p>Become a valued member of Jan Kalyan Sangh Foundation and support our journey for education, dignity, health, and inclusive development.</p>
                </div>

                <div class="participate-form">
                    <?php if (($_GET['success'] ?? '') === '1'): ?>
                        <div class="alert alert-success mt-3" role="status">Your participation form has been submitted successfully. It is pending approval, and our team will contact you soon.</div>
                    <?php endif; ?>
                    <?php if (isset($errorMessages[$errorCode])): ?>
                        <div class="alert alert-danger mt-3" role="alert"><?= e($errorMessages[$errorCode]) ?></div>
                    <?php endif; ?>

                    <form action="participate_submit.php" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="section-title">Personal Information / व्यक्तिगत जानकारी</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="full_name" placeholder="Enter full name" maxlength="255" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mobile Number *</label>
                                <input type="tel" class="form-control" name="mobile" placeholder="10-digit mobile number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter email address" maxlength="190" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password *</label>
                                <input id="participate-password" type="password" class="form-control" name="password" placeholder="Minimum 6 characters" minlength="6" maxlength="255" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirm Password *</label>
                                <input id="participate-confirm-password" type="password" class="form-control" name="confirm_password" placeholder="Re-enter password" minlength="6" maxlength="255" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date of Birth *</label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <select class="form-select" name="dob_day" required>
                                            <option value="">Day</option>
                                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-select" name="dob_month" required>
                                            <option value="">Month</option>
                                            <?php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; foreach ($months as $index => $month): ?>
                                                <option value="<?= $index + 1 ?>"><?= $month ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-select" name="dob_year" required>
                                            <option value="">Year</option>
                                            <?php for ($y = 1950; $y <= 2008; $y++): ?>
                                                <option value="<?= $y ?>"><?= $y ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Gender *</label>
                                <select class="form-select" name="gender" required>
                                    <option value="">-- Select Gender --</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Transgender</option>
                                    <option>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Marital Status *</label>
                                <select class="form-select" name="marital_status" required>
                                    <option value="">-- Select Marital Status --</option>
                                    <option>Single</option>
                                    <option>Married</option>
                                    <option>Widowed</option>
                                    <option>Divorced</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Father / Husband Name *</label>
                                <input type="text" class="form-control" name="guardian_name" placeholder="Father / Husband name" maxlength="255" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Aadhar Number *</label>
                                <input type="text" class="form-control" name="aadhar" placeholder="12-digit Aadhar number" maxlength="12" pattern="[0-9]{12}" inputmode="numeric" required>
                            </div>
                        </div>

                        <div class="section-title">Demographic Information / जनसांख्यिकीय जानकारी</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">-- Select Category --</option>
                                    <option>General</option>
                                    <option>OBC</option>
                                    <option>SC</option>
                                    <option>ST</option>
                                    <option>Minority</option>
                                    <option>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Blood Group (Optional)</label>
                                <select class="form-select" name="blood_group">
                                    <option value="">-- Select Blood Group --</option>
                                    <option>A+</option>
                                    <option>A-</option>
                                    <option>B+</option>
                                    <option>B-</option>
                                    <option>AB+</option>
                                    <option>AB-</option>
                                    <option>O+</option>
                                    <option>O-</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title">Professional Information / व्यावसायिक जानकारी</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Occupation *</label>
                                <select class="form-select" name="occupation" required>
                                    <option value="">-- Select Occupation --</option>
                                    <option>Student</option>
                                    <option>Private Job</option>
                                    <option>Government Job</option>
                                    <option>Business</option>
                                    <option>Farmer</option>
                                    <option>Self Employed</option>
                                    <option>Homemaker</option>
                                    <option>Retired</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title">Residential Address / आवासीय पता</div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Address *</label>
                                <textarea name="address" maxlength="2000" placeholder="House No, Street, Area" required></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Pin Code *</label>
                                <input type="text" class="form-control" name="pincode" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" placeholder="6-digit PIN code" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">State *</label>
                                <select class="form-select" name="state" required>
                                    <option value="">-- Select State --</option>
                                    <option>Delhi</option>
                                    <option>Haryana</option>
                                    <option>Uttar Pradesh</option>
                                    <option>Punjab</option>
                                    <option>Rajasthan</option>
                                    <option>Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">District *</label>
                                <select class="form-select" name="district" required>
                                    <option value="">-- Select District --</option>
                                    <option>Central Delhi</option>
                                    <option>East Delhi</option>
                                    <option>West Delhi</option>
                                    <option>North Delhi</option>
                                    <option>South Delhi</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title">Media Upload / मीडिया अपलोड</div>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="upload-box">
                                    <label for="profile_photo">
                                        <input type="file" id="profile_photo" name="profile_photo" accept="image/png,image/jpeg,image/webp,image/gif">
                                        <span>Upload Profile Photo</span>
                                        <div class="helper-text">Click to upload (JPG, PNG, WEBP or GIF)</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="section-title">Role in Organization / संगठन में पद</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Designation *</label>
                                <select class="form-select" name="designation" required>
                                    <option value="">-- Select Designation --</option>
                                    <option>Member</option>
                                    <option>Volunteer</option>
                                    <option>Coordinator</option>
                                    <option>District Lead</option>
                                    <option>State Manager</option>
                                    <option>Advisor</option>
                                    <option>Supporter</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree with Terms and Conditions of Organization.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <button type="submit" class="submit-btn">Preview & Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
    <script>
        const passwordField = document.getElementById('participate-password');
        const confirmPasswordField = document.getElementById('participate-confirm-password');
        const validatePasswordMatch = () => {
            confirmPasswordField.setCustomValidity(
                confirmPasswordField.value !== '' && confirmPasswordField.value !== passwordField.value
                    ? 'Passwords do not match.'
                    : ''
            );
        };
        passwordField.addEventListener('input', validatePasswordMatch);
        confirmPasswordField.addEventListener('input', validatePasswordMatch);
    </script>
</body>
</html>
