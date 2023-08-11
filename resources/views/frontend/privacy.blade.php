@extends('frontend.layouts.master')

@section('content')

<style type="text/css"> * {margin:0; padding:0; text-indent:0; }
    .s1 { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 16pt; }
    .p, p { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; margin:0pt; }
    h1 { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 11pt; }
    .a, a { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
    li {display: block; }
    #l1 {padding-left: 0pt;counter-reset: c1 1; }
    #l1> li>*:first-child:before {counter-increment: c1; content: counter(c1, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
    #l1> li:first-child>*:first-child:before {counter-increment: c1 0;  }
    #l2 {padding-left: 0pt;counter-reset: c2 1; }
    #l2> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)" "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
    #l2> li:first-child>*:first-child:before {counter-increment: c2 0;  }
    #l3 {padding-left: 0pt;counter-reset: c2 1; }
    #l3> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)" "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
    #l3> li:first-child>*:first-child:before {counter-increment: c2 0;  }
    #l4 {padding-left: 0pt;counter-reset: c2 1; }
    #l4> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)" "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
    #l4> li:first-child>*:first-child:before {counter-increment: c2 0;  }
   </style>


<div class="container">



<p class="s1" style="padding-top: 1pt; padding-left: 5pt; text-indent: 0pt; text-align: left;">Privacy Policy for Tevini Ltd Website</p>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<p style="padding-top: 12pt; padding-left: 5pt; text-indent: 0pt; text-align: left;">Effective Date: 10/08/2023</p>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<p style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
    At <b>Tevini Ltd</b>, we are committed to protecting and respecting your privacy. This Privacy Policy outlines how we collect, use, disclose, and protect the information you provide to us when you visit our website or interact with our
    online services. Please take a moment to read this Privacy Policy to understand our practices.
</p>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<ol id="l1">
    <li data-list-text="1.">
        <h1 style="padding-left: 15pt; text-indent: -10pt; text-align: left;">Information We Collect</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;">We may collect the following types of information:</p>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l2">
            <li data-list-text="1.1">
                <h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
                    Personal Information
                    <span class="p">
                        : This includes your name, email address, postal address, phone number, and other personally identifiable information you provide to us when filling out forms, subscribing to our newsletter, or otherwise interacting
                        with our website.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="1.2">
                <h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
                    Usage Information
                    <span class="p">
                        : We may collect information about your usage of our website, such as your IP address, browser type, operating system, pages visited, and referring URLs. We may also use cookies and similar technologies to collect
                        this information.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="2.">
        <h1 style="padding-left: 15pt; text-indent: -10pt; text-align: left;">How We Use Your Information</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;">We use the information we collect for various purposes, including:</p>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l3">
            <li data-list-text="2.1">
                <h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
                    Providing Services<span class="p">: To deliver the services you request from us, such as responding to inquiries, processing orders, and providing customer support.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="2.2">
                <h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
                    Communication<span class="p">: To send you updates, newsletters, marketing materials, and other information related to our products and services. You can opt out of these communications at any time.</span>
                </h1>
            </li>
            <li data-list-text="2.3">
                <h1 style="padding-top: 1pt; padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
                    Improving Website<span class="p">: To analyze usage patterns, improve our website, and customize content based on your preferences.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="2.4">
                <h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 107%; text-align: left;">Legal Compliance<span class="p">: To comply with legal obligations, resolve disputes, and enforce our terms and conditions.</span></h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="3.">
        <h1 style="padding-left: 15pt; text-indent: -10pt; text-align: left;">Disclosure of Your Information</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
            We do not sell, trade, or rent your personal information to third parties. However, we may share your information with trusted third parties under the following circumstances:
        </p>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l4">
            <li data-list-text="3.1">
                <h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 107%; text-align: left;">
                    Service Providers
                    <span class="p">: We may engage third-party service providers to assist with our operations, such as website hosting, payment processing, and analytics. These providers have access to your information</span>
                </h1>
                <p style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">only as needed to perform their tasks on our behalf and are obligated not to disclose or use it for other purposes.</p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="3.2">
                <h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">Legal Requirements<span class="p">: We may disclose your information when required by law, court order, or government request.</span></h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="3.3">
                <h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
                    Business Transfers<span class="p">: In the event of a merger, acquisition, or sale of assets, your information may be transferred to the acquiring entity.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="4.">
        <h1 style="padding-left: 15pt; text-indent: -10pt; text-align: left;">Data Security</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;">We take appropriate measures to protect your information from unauthorized access, alteration,</p>
        <p style="padding-top: 1pt; padding-left: 5pt; text-indent: 0pt; line-height: 107%; text-align: left;">
            disclosure, or destruction. However, no data transmission over the internet can be guaranteed as 100% secure. You acknowledge that you provide your information at your own risk.
        </p>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
    </li>
    <li data-list-text="5.">
        <h1 style="padding-left: 15pt; text-indent: -10pt; text-align: left;">Your Rights</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
            You have certain rights concerning your personal information, including the right to access, correct, and delete your data. You can also opt out of receiving marketing communications from us.
        </p>
    </li>
    <li data-list-text="6.">
        <h1 style="padding-top: 1pt; padding-left: 15pt; text-indent: -10pt; text-align: left;">Third-Party Links</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
            Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of these websites. We encourage you to review the privacy policies of any third-party sites you visit.
        </p>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
    </li>
    <li data-list-text="7.">
        <h1 style="padding-left: 15pt; text-indent: -10pt; text-align: left;">Changes to this Privacy Policy</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;">We may update this Privacy Policy from time to time to reflect changes in our practices or for other</p>
        <p style="padding-top: 1pt; padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">operational, legal, or regulatory reasons. We will notify you of any changes by posting the updated policy on our website.</p>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
    </li>
    <li data-list-text="8."><h1 style="padding-left: 15pt; text-indent: -10pt; text-align: left;">Contact Us</h1></li>
</ol>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<p style="padding-left: 5pt; text-indent: 0pt; line-height: 108%; text-align: left;">
    <a href="mailto:info@tevini.co.uk" class="a" target="_blank">If you have any questions, concerns, or requests regarding this Privacy Policy or your personal information, please contact us at </a>
    <a href="mailto:info@tevini.co.uk" target="_blank">info@tevini.co.uk</a>
</p>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<p style="padding-left: 5pt; text-indent: 0pt; text-align: left;">By using our website, you consent to the practices described in this Privacy Policy.</p>



</div>




@endsection


