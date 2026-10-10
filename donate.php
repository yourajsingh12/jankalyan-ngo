<!DOCTYPE html>
<html lang="en">
<?php
require_once __DIR__ . '/includes/functions.php';

$donatePage = row('SELECT * FROM donate_page_content WHERE id = 1') ?? [
    'breadcrumb_title' => 'Support Our Mission',
    'tagline' => 'Make A Difference',
    'heading' => 'Your support creates',
    'heading_highlight' => 'lasting change',
    'intro' => 'Your generous contributions help us continue our mission and expand our reach to those in need. Together, we can make a difference.',
    'bank_section_title' => 'NGO Bank Account Details',
    'bank_name_label' => 'Bank Name',
    'bank_name' => 'HDFC BANK',
    'account_number_label' => 'Account Number',
    'account_number' => '50200101465765',
    'ifsc_label' => 'IFSC Code',
    'ifsc_code' => 'HDFC0002645',
    'micr_label' => 'MICR',
    'micr_code' => '1100240248',
    'impact_title' => 'Why Your Donation Matters',
    'impact_intro' => 'Your support directly impacts our community. With your donations, we can:',
    'benefit_1' => 'Support education and learning opportunities.',
    'benefit_2' => 'Provide health and nutrition assistance.',
    'benefit_3' => 'Expand community welfare programs.',
    'benefit_4' => 'Bring hope and dignity to families in need.',
    'contact_title' => 'Need More Information?',
    'contact_phone' => '+91 8929939502',
    'contact_email' => 'support@jankalyansanghfoundation.com',
    'closing_message' => 'Join us in our journey to create a better future for everyone. Your contribution, no matter how small, helps us achieve our goals and bring hope to many lives.',
    'qr_code_image' => null,
];
$benefits = array_values(array_filter(array_map(
    static fn (string $key): string => trim((string) $donatePage[$key]),
    ['benefit_1', 'benefit_2', 'benefit_3', 'benefit_4']
), static fn (string $benefit): bool => $benefit !== ''));

include_once __DIR__ . '/head.php';
?>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <main class="main donation-page">
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title"><?= e($donatePage['breadcrumb_title']) ?></h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Donate</li>
                </ul>
            </div>
        </div>

        <section class="py-100">
            <div class="container">
                <div class="donation-intro">
                    <span class="site-title-tagline"><i class="far fa-heart"></i> <?= e($donatePage['tagline']) ?></span>
                    <h1 class="site-title"><?= e($donatePage['heading']) ?> <span><?= e($donatePage['heading_highlight']) ?></span></h1>
                    <p><?= e($donatePage['intro']) ?></p>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-6">
                        <div class="donation-card">
                            <h3><?= e($donatePage['bank_section_title']) ?></h3>
                            <dl class="bank-details">
                                <div class="bank-row">
                                    <dt><?= e($donatePage['bank_name_label']) ?></dt>
                                    <dd><?= e($donatePage['bank_name']) ?></dd>
                                </div>
                                <div class="bank-row">
                                    <dt><?= e($donatePage['account_number_label']) ?></dt>
                                    <dd><?= e($donatePage['account_number']) ?></dd>
                                </div>
                                <div class="bank-row">
                                    <dt><?= e($donatePage['ifsc_label']) ?></dt>
                                    <dd><?= e($donatePage['ifsc_code']) ?></dd>
                                </div>
                                <?php if (trim((string) $donatePage['micr_code']) !== ''): ?>
                                    <div class="bank-row">
                                        <dt><?= e($donatePage['micr_label']) ?></dt>
                                        <dd><?= e($donatePage['micr_code']) ?></dd>
                                    </div>
                                <?php endif; ?>
                            </dl>
                            <?php if (!empty($donatePage['qr_code_image'])): ?>
                                <div class="mt-4 text-center">
                                    <h3>Scan to Donate</h3>
                                    <img src="<?= e(img_url((string) $donatePage['qr_code_image'])) ?>"
                                        alt="Scan this QR code to make a donation"
                                        loading="lazy"
                                        style="width:min(100%, 280px); height:auto; margin:0 auto;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="donation-note">
                            <h3><?= e($donatePage['impact_title']) ?></h3>
                            <p><?= e($donatePage['impact_intro']) ?></p>
                            <ul>
                                <?php foreach ($benefits as $benefit): ?>
                                    <li><?= e($benefit) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="donation-contact">
                                <h4><?= e($donatePage['contact_title']) ?></h4>
                                <p class="mb-2"><i class="far fa-phone me-2"></i><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) $donatePage['contact_phone'])) ?>"><?= e($donatePage['contact_phone']) ?></a></p>
                                <p class="mb-0"><i class="far fa-envelope me-2"></i><a href="mailto:<?= e($donatePage['contact_email']) ?>"><?= e($donatePage['contact_email']) ?></a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="site-heading text-center mt-5 mb-0">
                    <p class="mb-0"><?= e($donatePage['closing_message']) ?></p>
                </div>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
