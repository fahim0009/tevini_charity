@extends('frontend.layouts.charity')
@section('content')

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Custom Style -->
<style>
    .form-card {
        background: #FDF3EE;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .form-card .section-title {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }
    .form-card .section-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 25px;
    }
    .form-group-custom {
        margin-bottom: 20px;
    }
    .form-group-custom label {
        font-size: 14px;
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
        display: block;
    }
    .input-wrapper {
        position: relative;
    }
    .input-wrapper i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 16px;
        transition: color 0.3s ease;
    }
    .input-wrapper .form-control {
        padding: 12px 15px 12px 45px;
        border: 2px solid #e0d5cd;
        border-radius: 8px;
        font-size: 15px;
        background: #fff;
        transition: all 0.3s ease;
    }
    .input-wrapper .form-control:focus {
        border-color: #18988B;
        box-shadow: 0 0 0 3px rgba(212, 119, 92, 0.15);
        outline: none;
    }
    .input-wrapper .form-control:focus + i,
    .input-wrapper .form-control:focus ~ i {
        color: #18988B;
    }
    .input-wrapper .form-control::placeholder {
        color: #bbb;
        font-size: 14px;
    }
    .ermsg {
        background: #fff3f3;
        color: #d32f2f;
        padding: 10px 15px;
        border-radius: 8px;
        border-left: 4px solid #d32f2f;
        font-size: 14px;
        margin-bottom: 20px;
        display: none;
    }
    .ermsg.show {
        display: block;
    }
    .divider {
        display: flex;
        align-items: center;
        margin: 25px 0;
    }
    .divider-line {
        flex: 1;
        height: 1px;
        background: #e0d5cd;
    }
    .divider-text {
        padding: 0 15px;
        font-size: 13px;
        color: #999;
    }
    .quick-amounts {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
    }
    .quick-amount-btn {
        background: #ffffff;
        border: 2px solid #18988B;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 14px;
        font-weight: 600;
        color: #555;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .quick-amount-btn:hover,
    .quick-amount-btn.active {
        border-color: #18988B;
        color: #18988B;
        background: rgba(212, 119, 92, 0.08);
    }
    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
        }
    }
</style>

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Invite your donors to support your cause by sharing your personalised donation link through your website, email, whatsapp and social media.
            </div>
        </div>
    </div>
    <!-- Image loader -->
    <div id='loading' style='display:none;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
    </div>
    <!-- Image loader -->
    <form action="">
        <div class="row">
            <!-- Left Side - Redesigned Form -->
            <div class="col-lg-6 px-3 mt-4">
                <div class="form-card">
                    <h4 class="section-title">Send Donation Link</h4>
                    
                    <div class="ermsg" id="errorMsg"></div>

                    <!-- Name Field -->
                    <div class="form-group-custom">
                        <label for="name">Recipient Name</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group-custom">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <!-- Amount Field -->
                    <div class="form-group-custom">
                        <label for="amount">Donation Amount</label>
                        <div class="quick-amounts">
                            <button type="button" class="quick-amount-btn" data-amount="10">£10</button>
                            <button type="button" class="quick-amount-btn" data-amount="25">£25</button>
                            <button type="button" class="quick-amount-btn" data-amount="50">£50</button>
                            <button type="button" class="quick-amount-btn" data-amount="100">£100</button>
                            <button type="button" class="quick-amount-btn" data-amount="500">£500</button>
                        </div>
                        <div class="input-wrapper">
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Or enter custom amount">
                            <i class="fas fa-pound-sign"></i>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="button" id="submit" class="text-white btn-theme bg-primary d-block fs-14 fw-bold w-100">
                            <i class="fas fa-paper-plane"></i>
                            Send Donation Link
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - QR Code Section -->
            <div class="col-lg-6 px-3 mt-4">
                <div class="qr-code-container text-center p-4" style="background: #FDF3EE; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1);">
                    <h5 class="mb-3">Scan to Donate</h5>
                    <div id="qrcode" class="d-inline-block p-3" style="background: #ffffff; border-radius: 8px;"></div>
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Donation Link:</small>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="donationLink" value="{{ $donationLink }}" readonly>
                            <button class="btn btn-sm btn-primary" type="button" id="copyLink">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>

                    <!-- Social Media Share Section -->
                    <div class="mt-4 pt-3" style="border-top: 1px solid rgba(0,0,0,0.1);">
                        <small class="text-muted d-block mb-3">Share via:</small>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <!-- WhatsApp -->
                            <a href="#" id="shareWhatsApp" target="_blank" class="btn btn-sm" style="background: #25D366; color: #fff; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 50%;" title="Share on WhatsApp">
                                <i class="fab fa-whatsapp fa-lg"></i>
                            </a>
                            <!-- Facebook -->
                            <a href="#" id="shareFacebook" target="_blank" class="btn btn-sm" style="background: #1877F2; color: #fff; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 50%;" title="Share on Facebook">
                                <i class="fab fa-facebook-f fa-lg"></i>
                            </a>
                            <!-- LinkedIn -->
                            <a href="#" id="shareLinkedIn" target="_blank" class="btn btn-sm" style="background: #0A66C2; color: #fff; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 50%;" title="Share on LinkedIn">
                                <i class="fab fa-linkedin-in fa-lg"></i>
                            </a>
                            <!-- Email -->
                            <a href="#" id="shareEmail" class="btn btn-sm" style="background: #EA4335; color: #fff; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 50%;" title="Share via Email">
                                <i class="fas fa-envelope fa-lg"></i>
                            </a>
                            <!-- Telegram -->
                            <a href="#" id="shareTelegram" target="_blank" class="btn btn-sm" style="background: #0088cc; color: #fff; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 50%;" title="Share on Telegram">
                                <i class="fab fa-telegram-plane fa-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $("#usercontact").addClass('active');
    });
</script>

<script>
    $(document).ready(function () {
        // Header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Donation Link
        var donationLink = "{{ $donationLink }}";
        var shareText = "Support our charity! Donate here: ";
        var fullShareText = shareText + donationLink;

        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: donationLink,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Copy link functionality
        $("#copyLink").click(function() {
            var copyText = document.getElementById("donationLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value).then(function() {
                var $btn = $("#copyLink");
                $btn.html('<i class="fas fa-check"></i> Copied!');
                setTimeout(function() {
                    $btn.html('<i class="fas fa-copy"></i> Copy');
                }, 2000);
            });
        });

        // Social Media Share Links
        $("#shareWhatsApp").attr("href", "https://api.whatsapp.com/send?text=" + encodeURIComponent(fullShareText));
        $("#shareFacebook").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(donationLink));
        $("#shareLinkedIn").attr("href", "https://www.linkedin.com/sharing/share-offsite/?url=" + encodeURIComponent(donationLink));
        $("#shareEmail").attr("href", "mailto:?subject=" + encodeURIComponent("Support Our Charity!") + "&body=" + encodeURIComponent(fullShareText));
        $("#shareTelegram").attr("href", "https://t.me/share/url?url=" + encodeURIComponent(donationLink) + "&text=" + encodeURIComponent(shareText));

        // Quick Amount Buttons
        $(".quick-amount-btn").click(function() {
            $(".quick-amount-btn").removeClass("active");
            $(this).addClass("active");
            $("#amount").val($(this).data("amount"));
        });

        // Remove active class when typing custom amount
        $("#amount").on("input", function() {
            var val = $(this).val();
            var found = false;
            $(".quick-amount-btn").each(function() {
                if ($(this).data("amount") == val) {
                    $(this).addClass("active");
                    found = true;
                } else {
                    $(this).removeClass("active");
                }
            });
        });

        // Make mail start
        var url = "{{ URL::to('/charity/create-a-link') }}";
        $("#submit").click(function(){
            var name = $("#name").val();
            var email = $("#email").val();
            var amount = $("#amount").val();

            // Hide previous error
            $("#errorMsg").removeClass("show").html("");

            // Basic validation
            if (!name || !email || !amount) {
                $("#errorMsg").html("<i class='fas fa-exclamation-circle'></i> Please fill in all fields.").addClass("show");
                return;
            }

            $("#loading").show();
            
            $.ajax({
                url: url,
                method: "POST",
                data: {name, email, amount},
                success: function (d) {
                    if (d.status == 303) {
                        $("#errorMsg").html("<i class='fas fa-exclamation-circle'></i> " + d.message).addClass("show");
                    } else if(d.status == 300) {
                        $("#errorMsg").html("<i class='fas fa-check-circle'></i> " + d.message).removeClass("show").css({
                            "background": "#e8f5e9",
                            "color": "#2e7d32",
                            "border-left-color": "#2e7d32"
                        }).addClass("show");
                        window.setTimeout(function(){ location.reload() }, 2000);
                    }
                },
                complete: function(data) {
                    $("#loading").hide();
                },
                error: function (d) {
                    console.log(d);
                }
            });
        });
        // Send mail end
    });
</script>

@endsection