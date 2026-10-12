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
        'SELECT id, full_name, mobile, email, designation, address, state, district, pincode, profile_photo, status
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
            width: min(440px, 100%); aspect-ratio: 1.585 / 1; display: flex; flex-direction: column; overflow: hidden;
            color: #172554; background: #fff; border: 1px solid #c7d2fe; border-radius: 18px;
            box-shadow: 0 22px 55px rgba(30,58,138,.2); font-family: Arial, sans-serif;
        }
        .participant-login-card__header {
            display: flex; align-items: center; gap: 12px; min-height: 29%; padding: 12px 18px; color: #fff;
            background: linear-gradient(115deg, #101b73, #1836b8 66%, #274bd8); border-bottom: 3px solid #e32636;
        }
        .participant-login-card__logo { width: 54px; height: 54px; flex: 0 0 auto; object-fit: contain; }
        .participant-login-card__org { min-width: 0; font-size: 13px; font-weight: 800; line-height: 1.2; letter-spacing: .045em; }
        .participant-login-card__org small { display: block; margin-top: 4px; color: #dbeafe; font-size: 8px; font-weight: 600; letter-spacing: .14em; }
        .participant-login-card__tag {
            margin-left: auto; padding: 6px 9px; border: 1px solid #facc15; border-radius: 999px;
            color: #fff; font-size: 8px; font-weight: 800; letter-spacing: .1em; white-space: nowrap;
        }
        .participant-login-card__body { display: flex; align-items: center; gap: 16px; flex: 1; min-height: 0; padding: 13px 18px 10px; }
        .participant-login-card__photo, .participant-login-card__initial {
            display: flex; align-items: center; justify-content: center; width: 82px; height: 96px; flex: 0 0 auto;
            border: 3px solid #c7d2fe; border-radius: 12px; background: #eef2ff; color: #1d4ed8;
            font-size: 27px; font-weight: 800; object-fit: cover;
        }
        .participant-login-card__details { min-width: 0; flex: 1; }
        .participant-login-card__name {
            margin: 0 0 3px; overflow: hidden; color: #172554; font-size: 17px; font-weight: 800;
            line-height: 1.15; text-overflow: ellipsis; white-space: nowrap;
        }
        .participant-login-card__designation { margin-bottom: 9px; color: #1d4ed8; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
        .participant-login-card__detail { display: flex; gap: 6px; margin-top: 4px; overflow: hidden; color: #475569; font-size: 9px; line-height: 1.25; }
        .participant-login-card__detail strong { flex: 0 0 35px; color: #64748b; font-size: 8px; text-transform: uppercase; letter-spacing: .04em; }
        .participant-login-card__detail span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .participant-login-card__footer {
            display: flex; align-items: center; justify-content: space-between; min-height: 15%; padding: 7px 18px;
            color: #eff6ff; background: #101b73; border-top: 2px solid #e32636; font-size: 8px; letter-spacing: .04em;
        }
        .participant-login-card__number { color: #fff; font-size: 10px; font-weight: 800; letter-spacing: .11em; }
        @media print {
            @page { size: 85.6mm 54mm; margin: 0; }
            body * { visibility: hidden !important; }
            .participant-login-card-wrap, .participant-login-card-wrap * { visibility: visible !important; }
            .participant-login-card-wrap { position: fixed; inset: 0; display: block; width: 85.6mm; height: 54mm; margin: 0; padding: 0; }
            .participant-login-card {
                width: 85.6mm; height: 54mm; aspect-ratio: auto; border: 0; border-radius: 0; box-shadow: none;
                print-color-adjust: exact; -webkit-print-color-adjust: exact;
            }
            .participant-login-card__header { gap: 2.5mm; padding: 2.5mm 3.5mm; }
            .participant-login-card__logo { width: 11mm; height: 11mm; }
            .participant-login-card__org { font-size: 8pt; }
            .participant-login-card__org small { margin-top: .7mm; font-size: 5pt; }
            .participant-login-card__tag { padding: 1.2mm 1.8mm; font-size: 5pt; }
            .participant-login-card__body { gap: 3mm; padding: 2.5mm 3.5mm 2mm; }
            .participant-login-card__photo, .participant-login-card__initial { width: 17mm; height: 21mm; border-width: .6mm; border-radius: 2mm; font-size: 15pt; }
            .participant-login-card__name { margin-bottom: .5mm; font-size: 10pt; }
            .participant-login-card__designation { margin-bottom: 1.5mm; font-size: 6pt; }
            .participant-login-card__detail { gap: 1mm; margin-top: .7mm; font-size: 5.5pt; }
            .participant-login-card__detail strong { flex-basis: 8mm; font-size: 4.5pt; }
            .participant-login-card__footer { min-height: 8mm; padding: 1.5mm 3.5mm; font-size: 5pt; }
            .participant-login-card__number { font-size: 6pt; }
            .participant-login-card-actions { display: none !important; }
        }
        @media (max-width: 575px) {
            .participant-login-page { padding: 40px 12px; }
            .participant-login-panel { padding: 26px 20px; }
            .participant-login-card__header { gap: 8px; padding: 10px 12px; }
            .participant-login-card__logo { width: 42px; height: 42px; }
            .participant-login-card__org { font-size: 10px; }
            .participant-login-card__org small { font-size: 6px; }
            .participant-login-card__tag { padding: 5px 6px; font-size: 6px; }
            .participant-login-card__body { gap: 10px; padding: 10px 12px; }
            .participant-login-card__photo, .participant-login-card__initial { width: 64px; height: 78px; }
            .participant-login-card__name { font-size: 14px; }
            .participant-login-card__detail { font-size: 8px; }
            .participant-login-card__footer { padding: 6px 12px; font-size: 7px; }
            .participant-login-card__number { font-size: 8px; }
        }
    </style>

    <main class="participant-login-page">
        <div class="container">
            <?php if ($participant !== null): ?>
                <div class="participant-login-card-wrap">
                    <div class="participant-login-card" aria-label="Your Jan Kalyan participant ID card front side">
                        <div class="participant-login-card__header">
                            <img class="participant-login-card__logo" src="<?= e(base_url('admin/img/logo_white.png')) ?>" alt="Jan Kalyan logo">
                            <div class="participant-login-card__org">
                                JAN KALYAN SANGH FOUNDATION
                                <small>PUBLIC WELFARE • COMMUNITY SERVICE</small>
                            </div>
                            <span class="participant-login-card__tag">IDENTITY CARD</span>
                        </div>
                        <div class="participant-login-card__body">
                            <?php if (!empty($participant['profile_photo'])): ?>
                                <img class="participant-login-card__photo" src="<?= e(img_url((string) $participant['profile_photo'])) ?>" alt="<?= e($participant['full_name']) ?>">
                            <?php else: ?>
                                <div class="participant-login-card__initial" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) $participant['full_name'], 0, 1))) ?></div>
                            <?php endif; ?>
                            <div class="participant-login-card__details">
                                <h1 class="participant-login-card__name"><?= e($participant['full_name']) ?></h1>
                                <div class="participant-login-card__designation"><?= e($participant['designation']) ?></div>
                                <div class="participant-login-card__detail"><strong>Mobile</strong><span><?= e($participant['mobile']) ?></span></div>
                                <div class="participant-login-card__detail"><strong>Email</strong><span><?= e($participant['email']) ?></span></div>
                            </div>
                        </div>
                        <div class="participant-login-card__footer">
                            <span>APPROVED PARTICIPANT</span>
                            <span class="participant-login-card__number">ID: JKSF-<?= e(str_pad((string) $participant['id'], 6, '0', STR_PAD_LEFT)) ?></span>
                        </div>
                    </div>

                    <div class="participant-login-card" aria-label="Your Jan Kalyan participant ID card back side">
                        <div class="participant-login-card__header">
                            <img class="participant-login-card__logo" src="<?= e(base_url('admin/img/logo_white.png')) ?>" alt="Jan Kalyan logo">
                            <div class="participant-login-card__org">
                                JAN KALYAN SANGH FOUNDATION
                                <small>PUBLIC WELFARE • COMMUNITY SERVICE</small>
                            </div>
                            <span class="participant-login-card__tag">IDENTITY CARD</span>
                        </div>
                        <div class="participant-login-card__body">
                            <?php if (!empty($participant['profile_photo'])): ?>
                                <img class="participant-login-card__photo" src="<?= e(img_url((string) $participant['profile_photo'])) ?>" alt="<?= e($participant['full_name']) ?>">
                            <?php else: ?>
                                <div class="participant-login-card__initial" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) $participant['full_name'], 0, 1))) ?></div>
                            <?php endif; ?>
                            <div class="participant-login-card__details">
                                <h1 class="participant-login-card__name"><?= e($participant['full_name']) ?></h1>
                                <div class="participant-login-card__designation"><?= e($participant['designation']) ?></div>
                                <div class="participant-login-card__detail"><strong>Mobile</strong><span><?= e($participant['mobile']) ?></span></div>
                                <div class="participant-login-card__detail"><strong>Email</strong><span><?= e($participant['email']) ?></span></div>
                            </div>
                        </div>
                        <div class="participant-login-card__footer">
                            <span>APPROVED PARTICIPANT</span>
                            <span class="participant-login-card__number">ID: JKSF-<?= e(str_pad((string) $participant['id'], 6, '0', STR_PAD_LEFT)) ?></span>
                        </div>
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
