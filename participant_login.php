<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
start_session();
ensure_participants_table();

$loginError = '';
$loginNotice = '';
$identifier = '';
$participant = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';

    if ($action === 'logout') {
        unset($_SESSION['participant_id']);
        session_regenerate_id(true);
        redirect('participant_login.php');
    }

    $identifier = is_string($_POST['identifier'] ?? null) ? trim($_POST['identifier']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if ($action !== 'login' || $identifier === '' || $password === '') {
        $loginError = 'Enter your registered email or mobile number and password.';
    } elseif (mb_strlen($identifier) > 190 || mb_strlen($password) > 255) {
        $loginError = 'The email/mobile number or password is incorrect.';
    } else {
        $matchingParticipants = rows(
            'SELECT id, password_hash, status
               FROM participants
              WHERE mobile = ? OR email = ?
              LIMIT 2',
            [$identifier, $identifier]
        );

        if (
            count($matchingParticipants) !== 1
            || empty($matchingParticipants[0]['password_hash'])
            || !password_verify($password, (string) $matchingParticipants[0]['password_hash'])
        ) {
            $loginError = 'The email/mobile number or password is incorrect.';
        } elseif ((int) $matchingParticipants[0]['status'] !== 1) {
            $loginNotice = 'Your registration is awaiting admin approval. Your ID card will be available here once approved.';
        } else {
            session_regenerate_id(true);
            $_SESSION['participant_id'] = (int) $matchingParticipants[0]['id'];
            redirect('participant_login.php');
        }
    }
} elseif (isset($_SESSION['participant_id'])) {
    $participant = row(
        'SELECT id, full_name, mobile, email, dob, designation, address, state, district, pincode, profile_photo, status
           FROM participants
          WHERE id = ?',
        [(int) $_SESSION['participant_id']]
    );

    if ($participant === null || (int) $participant['status'] !== 1) {
        unset($_SESSION['participant_id']);
        $participant = null;
        $loginNotice = 'Your participant ID is unavailable. Please contact the administrator if you need help.';
    }
}

$pageTitle = 'Participant Login';
include_once __DIR__ . '/head.php';
?>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <style>
        .participant-login-page { min-height: 65vh; padding: 70px 0; background: linear-gradient(180deg, #eff6ff, #fff 75%); }
        .participant-login-panel {
            max-width: 560px; margin: 0 auto; padding: 36px; background: #fff; border: 1px solid #dbeafe;
            border-radius: 20px; box-shadow: 0 20px 55px rgba(30, 58, 138, .1);
        }
        .participant-login-panel h1 { margin-bottom: 8px; color: #172554; font-size: 30px; font-weight: 800; }
        .participant-login-panel p { color: #64748b; }
        .participant-login-panel label { display: block; margin: 18px 0 7px; color: #334155; font-weight: 700; }
        .participant-login-panel input {
            width: 100%; min-height: 48px; padding: 10px 14px; color: #172554; background: #f8fafc;
            border: 1px solid #cbd5e1; border-radius: 10px;
        }
        .participant-login-panel input:focus { border-color: #274bd8; outline: 3px solid rgba(39,75,216,.12); }
        .participant-login-card-wrap { display: flex; flex-direction: column; align-items: center; gap: 18px; }
        .participant-login-card {
            position: relative; container-type: inline-size; width: min(340px, 100%); aspect-ratio: 591 / 1004;
            overflow: hidden; box-shadow: 0 22px 55px rgba(30,58,138,.2); font-family: Arial, sans-serif;
        }
        .participant-login-card--front { order: 0; }
        .participant-login-card--back { order: 1; }
        .participant-login-card__template { position: absolute; z-index: 0; inset: 0; width: 100%; height: 100%; }
        .participant-login-card__photo, .participant-login-card__initial {
            position: absolute; z-index: 2; top: 16.8%; left: 27.6%; width: 44.3%; aspect-ratio: 1;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
            border-radius: 50%; background: #e0f2fe; color: #123d70; font-size: 18cqw; font-weight: 800; object-fit: cover;
        }
        .participant-login-card__name {
            position: absolute; z-index: 2; top: 45%; left: 5%; width: 90%; text-align: center; overflow: hidden;
            color: #123d70; font-size: 6cqw; font-weight: 800; line-height: 1.3; text-overflow: ellipsis; white-space: nowrap;
        }
        .participant-login-card__designation {
            position: absolute; z-index: 2; top: 50.3%; left: 5%; width: 90%; text-align: center; overflow: hidden;
            color: #274b73; font-size: 4cqw; font-weight: 700; line-height: 1.3; text-overflow: ellipsis; white-space: nowrap;
        }
        .participant-login-card__details {
            position: absolute; z-index: 2; top: 59.5%; left: 24.5%; right: 4%; display: grid;
            grid-template-rows: repeat(4, 6cqw); color: #172554; font-size: 3.2cqw; font-weight: 600; line-height: 6cqw;
        }
        .participant-login-card__detail { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .participant-login-card--back { box-shadow: 0 22px 55px rgba(30,58,138,.2); }
        @media print {
            @page { size: 54mm 91.8mm; margin: 0; }
            body * { visibility: hidden !important; }
            .participant-login-card-wrap, .participant-login-card-wrap * { visibility: visible !important; }
            .participant-login-card-wrap { position: absolute; inset: 0 auto auto 0; display: block; width: 54mm; margin: 0; padding: 0; }
            .participant-login-card {
                width: 54mm; height: 91.8mm; aspect-ratio: auto; margin: 0; box-shadow: none;
                print-color-adjust: exact; -webkit-print-color-adjust: exact;
            }
            .participant-login-card--back { break-before: page; page-break-before: always; }
            .participant-login-card-actions { display: none !important; }
        }
        @media (max-width: 575px) {
            .participant-login-page { padding: 40px 12px; }
            .participant-login-panel { padding: 26px 20px; }
        }
    </style>

    <main class="participant-login-page">
        <div class="container">
            <?php if ($participant !== null): ?>
                <div class="participant-login-card-wrap">
                    <div class="participant-login-card participant-login-card--front" aria-label="Your Jan Kalyan participant ID card with QR code and details">
                        <img class="participant-login-card__template" src="<?= e(base_url('assets/img/idcard/id_card.png?v=5')) ?>" alt="Jan Kalyan ID card template with QR code">
                        <?php if (!empty($participant['profile_photo'])): ?>
                            <img class="participant-login-card__photo" src="<?= e(img_url((string) $participant['profile_photo'])) ?>" alt="<?= e($participant['full_name']) ?>">
                        <?php else: ?>
                            <div class="participant-login-card__initial" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) $participant['full_name'], 0, 1))) ?></div>
                        <?php endif; ?>
                        <div class="participant-login-card__name"><?= e($participant['full_name']) ?></div>
                        <div class="participant-login-card__designation"><?= e($participant['designation']) ?></div>
                        <div class="participant-login-card__details">
                            <div class="participant-login-card__detail">JKSF-<?= e(str_pad((string) $participant['id'], 6, '0', STR_PAD_LEFT)) ?></div>
                            <div class="participant-login-card__detail"><?= e($participant['email']) ?></div>
                            <div class="participant-login-card__detail"><?= e($participant['mobile']) ?></div>
                            <div class="participant-login-card__detail"><?= e(fmt_date((string) $participant['dob'], 'd/m/Y')) ?></div>
                        </div>
                    </div>

                    <div class="participant-login-card participant-login-card--back" aria-label="Back of your Jan Kalyan participant ID card">
                        <img class="participant-login-card__template" src="<?= e(base_url('assets/img/idcard/id_card2.png?v=5')) ?>" alt="Jan Kalyan ID card back template">
                    </div>

                    <div class="participant-login-card-actions d-flex flex-wrap justify-content-center gap-2">
                        <button class="theme-btn" type="button" onclick="window.print()"><i class="fas fa-print"></i> Print ID card</button>
                        <form method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="logout">
                            <button class="theme-btn" type="submit"><i class="fas fa-sign-out-alt"></i> Log out</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <section class="participant-login-panel">
                    <h1>Participant Login</h1>
                    <p>Sign in with your registered email or mobile number to view your approved ID card.</p>
                    <?php if ($loginError !== ''): ?>
                        <div class="alert alert-danger" role="alert"><?= e($loginError) ?></div>
                    <?php endif; ?>
                    <?php if ($loginNotice !== ''): ?>
                        <div class="alert alert-info" role="status"><?= e($loginNotice) ?></div>
                    <?php endif; ?>
                    <form method="post" action="participant_login.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="login">
                        <label for="participant-identifier">Registered email or mobile number</label>
                        <input id="participant-identifier" name="identifier" type="text" maxlength="190" autocomplete="username" value="<?= e($identifier) ?>" required>
                        <label for="participant-password">Password</label>
                        <input id="participant-password" name="password" type="password" maxlength="255" autocomplete="current-password" required>
                        <button class="theme-btn mt-4" type="submit"><i class="fas fa-sign-in-alt"></i> Login and view ID</button>
                    </form>
                    <p class="mt-3 mb-0">Not registered yet? <a href="participate.php">Register as a participant</a>.</p>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>