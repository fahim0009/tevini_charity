@extends('frontend.layouts.master')

@section('content')
<style>


 h1 { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 11pt; }
 .p, p { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; margin:0pt; }
 .s1 { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 11pt; }
 .a, a { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 .s5 { color: black; font-family:Calibri, sans-serif; font-style: italic; font-weight: normal; text-decoration: underline; font-size: 11pt; }
 .s6 { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 10pt; }
 .s7 { color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 10pt; }
 .s8 { color: #00F; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 11pt; }
 li {display: block; }
 #l1 {padding-left: 0pt;counter-reset: c1 1; }
 #l1> li>*:first-child:before {counter-increment: c1; content: counter(c1, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 11pt; }
 #l1> li:first-child>*:first-child:before {counter-increment: c1 0;  }
 #l2 {padding-left: 0pt;counter-reset: c2 1; }
 #l2> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l2> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l3 {padding-left: 0pt;counter-reset: c2 1; }
 #l3> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l3> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l4 {padding-left: 0pt;counter-reset: c3 1; }
 #l4> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l4> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l5 {padding-left: 0pt;counter-reset: c3 1; }
 #l5> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l5> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l6 {padding-left: 0pt;counter-reset: c4 1; }
 #l6> li>*:first-child:before {counter-increment: c4; content: "("counter(c4, lower-latin)") "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l6> li:first-child>*:first-child:before {counter-increment: c4 0;  }
 #l7 {padding-left: 0pt;counter-reset: c2 1; }
 #l7> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l7> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l8 {padding-left: 0pt;counter-reset: c2 1; }
 #l8> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l8> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l9 {padding-left: 0pt;counter-reset: c3 1; }
 #l9> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l9> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l10 {padding-left: 0pt;counter-reset: c2 1; }
 #l10> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l10> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l11 {padding-left: 0pt;counter-reset: c3 1; }
 #l11> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l11> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l12 {padding-left: 0pt;counter-reset: c3 1; }
 #l12> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l12> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l13 {padding-left: 0pt;counter-reset: d1 5; }
 #l13> li>*:first-child:before {counter-increment: d1; content: counter(d1, decimal)" "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l13> li:first-child>*:first-child:before {counter-increment: d1 0;  }
 #l14 {padding-left: 0pt;counter-reset: d2 2; }
 #l14> li>*:first-child:before {counter-increment: d2; content: counter(d1, decimal)"."counter(d2, decimal)" "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l14> li:first-child>*:first-child:before {counter-increment: d2 0;  }
 #l15 {padding-left: 0pt;counter-reset: d3 3; }
 #l15> li>*:first-child:before {counter-increment: d3; content: counter(d1, decimal)"."counter(d2, decimal)"."counter(d3, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 10pt; }
 #l15> li:first-child>*:first-child:before {counter-increment: d3 0;  }
 #l16 {padding-left: 0pt;counter-reset: c2 1; }
 #l16> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l16> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l17 {padding-left: 0pt;counter-reset: c3 1; }
 #l17> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l17> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l18 {padding-left: 0pt;counter-reset: c2 1; }
 #l18> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l18> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l19 {padding-left: 0pt;counter-reset: c3 1; }
 #l19> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l19> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l20 {padding-left: 0pt;counter-reset: c2 1; }
 #l20> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l20> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l21 {padding-left: 0pt;counter-reset: c3 1; }
 #l21> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l21> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l22 {padding-left: 0pt;counter-reset: c4 1; }
 #l22> li>*:first-child:before {counter-increment: c4; content: "("counter(c4, lower-latin)") "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l22> li:first-child>*:first-child:before {counter-increment: c4 0;  }
 #l23 {padding-left: 0pt;counter-reset: c3 1; }
 #l23> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l23> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l24 {padding-left: 0pt;counter-reset: c2 1; }
 #l24> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l24> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l25 {padding-left: 0pt;counter-reset: c2 1; }
 #l25> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l25> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l26 {padding-left: 0pt;counter-reset: c3 1; }
 #l26> li>*:first-child:before {counter-increment: c3; content: counter(c1, decimal)"."counter(c2, decimal)"."counter(c3, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l26> li:first-child>*:first-child:before {counter-increment: c3 0;  }
 #l27 {padding-left: 0pt;counter-reset: c2 1; }
 #l27> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l27> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l28 {padding-left: 0pt;counter-reset: c2 1; }
 #l28> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l28> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 #l29 {padding-left: 0pt;counter-reset: c2 1; }
 #l29> li>*:first-child:before {counter-increment: c2; content: counter(c1, decimal)"."counter(c2, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l29> li:first-child>*:first-child:before {counter-increment: c2 0;  }
 li {display: block; }
 #l30 {padding-left: 0pt;counter-reset: e1 1; }
 #l30> li>*:first-child:before {counter-increment: e1; content: counter(e1, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 11pt; }
 #l30> li:first-child>*:first-child:before {counter-increment: e1 0;  }
 #l31 {padding-left: 0pt;counter-reset: e2 1; }
 #l31> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l31> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l32 {padding-left: 0pt;counter-reset: e2 1; }
 #l32> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l32> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l33 {padding-left: 0pt;counter-reset: e2 1; }
 #l33> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l33> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l34 {padding-left: 0pt;counter-reset: e2 1; }
 #l34> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l34> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l35 {padding-left: 0pt;counter-reset: e2 1; }
 #l35> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l35> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l36 {padding-left: 0pt;counter-reset: e2 1; }
 #l36> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l36> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l37 {padding-left: 0pt;counter-reset: e2 1; }
 #l37> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l37> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l38 {padding-left: 0pt;counter-reset: e2 1; }
 #l38> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l38> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l39 {padding-left: 0pt;counter-reset: e2 1; }
 #l39> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l39> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l40 {padding-left: 0pt;counter-reset: e2 1; }
 #l40> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l40> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l41 {padding-left: 0pt;counter-reset: e3 1; }
 #l41> li>*:first-child:before {counter-increment: e3; content: counter(e1, decimal)"."counter(e2, decimal)"."counter(e3, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l41> li:first-child>*:first-child:before {counter-increment: e3 0;  }
 #l42 {padding-left: 0pt;counter-reset: e4 1; }
 #l42> li>*:first-child:before {counter-increment: e4; content: counter(e1, decimal)"."counter(e2, decimal)"."counter(e3, decimal)"."counter(e4, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l42> li:first-child>*:first-child:before {counter-increment: e4 0;  }
 #l43 {padding-left: 0pt;counter-reset: e3 1; }
 #l43> li>*:first-child:before {counter-increment: e3; content: counter(e1, decimal)"."counter(e2, decimal)"."counter(e3, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l43> li:first-child>*:first-child:before {counter-increment: e3 0;  }
 #l44 {padding-left: 0pt;counter-reset: e2 1; }
 #l44> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l44> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l45 {padding-left: 0pt;counter-reset: e3 1; }
 #l45> li>*:first-child:before {counter-increment: e3; content: counter(e1, decimal)"."counter(e2, decimal)"."counter(e3, decimal)". "; color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; }
 #l45> li:first-child>*:first-child:before {counter-increment: e3 0;  }
 #l46 {padding-left: 0pt;counter-reset: e2 1; }
 #l46> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l46> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 #l47 {padding-left: 0pt;counter-reset: e2 1; }
 #l47> li>*:first-child:before {counter-increment: e2; content: counter(e1, decimal)"."counter(e2, decimal)". "; color: black; font-style: normal; font-weight: normal; text-decoration: none; }
 #l47> li:first-child>*:first-child:before {counter-increment: e2 0;  }
 table, tbody {vertical-align: top; overflow: visible; }
</style>
<div class="container">
    <h1 style="padding-top: 1pt; padding-left: 6pt; text-indent: 0pt; text-align: left;">Tevini Prepaid Mastercard® Cardholder Terms 28/06/23</h1>
<p style="text-indent: 0pt; text-align: left;"><br /></p>


<ol>
    <li>
        <h1>These terms</h1>
        <ol>
            <li>
                <h1>
                    What these terms cover<span class="p">. These are the terms and conditions (the “</span>Terms<span class="p">”) which govern the use of the personal, non-transferable card scheme branded prepaid card (the “</span>Card
                    <span class="p">”) which you have been issued with or will be issued with.</span>
                </h1>
            </li>
            <li>
                <h1>
                    Why you should read them
                    <span class="p">
                        . Please read these Terms carefully before you use your Card. These Terms tell you who we are, who we work with, how you can use your Card and the steps you need to take to protect yourself from unauthorised use of
                        the Card and how you and we may change or end the contract, what to do if there is a problem and other important information. If you think that there is a mistake in these Terms, please contact us to discuss.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li>
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Information about us and how to contact us</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol>
            <li>
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Who we are
                    <a href="mailto:support@payr.net" class="a" target="_blank">
                        . The Card is issued by Payrnet Limited whose company number is 09883437 and whose registered office is 1 Snowden Street, London, England, EC2A 2DQ. Payrnet Limited can be contacted by email –
                    </a>
                    <a href="mailto:support@payr.net" class="s1" target="_blank">support@payr.net</a><a href="https://www.railsr.com/payrnet" class="a" target="_blank">. Payrnet Limited’s web address is </a>
                    <a href="https://www.railsr.com/payrnet" class="s1" target="_blank">https://www.railsr.com/payrnet</a><a href="https://www.railsr.com/payrnet" target="_blank">.</a>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li>
                <p style="padding-top: 2pt; padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We are authorised by the Financial Conduct Authority under the Electronic Money Regulations 2011 (registration number 900594) for the issuing of electronic money (“<b>e-money</b>”).
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li>
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Who we work with when providing you with services relating to the Card.
                    <span class="p">Although we are the sole issuer of the Card, we work with Tevini, who are a customer of QPay, trading as Quantum Card Services Ltd, 7 - 8 Church Street, Wimborne, Dorset, BH21 1JH (the “</span>Distributor
                    <a href="http://www.tevini.co.uk/" class="a" target="_blank">
                        ”). You can find out more information about the Card and how to apply for a Card by contacting Tevini. If you have a Card, information is available by logging in to your account at https://
                    </a>
                    <a href="http://www.tevini.co.uk/" target="_blank">www.tevini.co.uk/</a>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li>
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: left;">The Distributor will be your first point of contact in relation to these Terms, for example if you:</p>
                <ol id="l4">
                    <li data-list-text="2.4.1."><p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">wish to cancel the Card or complain about the service you have been provided with pursuant to these Terms;</p></li>
                    <li data-list-text="2.4.2."><p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">let us know that the Card has been or potentially has been lost, stolen or misappropriated; and</p></li>
                    <li data-list-text="2.4.3.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">report an unauthorised Transactions relating to your Card.</p>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                </ol>
            </li>
            <li>
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                        The services provided by the Distributor are governed by a separate set of terms and conditions which are set out in in your donation agreement and on the website at 
                    <a href="http://www.tevini.co.uk/" target="_blank">www.tevini.co.uk/</a>
                </p>
            </li>
            <li>
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Payrnet Limited (“<b>Payrnet</b>”) also provide you with the e-money account (the “<b>Account</b>”) where the funds, which can be spent using the Card, are held. The services provided by Payrnet are governed by a
                    separate set of terms and conditions between you and Payrnet which are set out below in Payrnet’s consumer terms<span style="color: #00aeee;">. </span>Please note that the funds in the Account will not earn any interest.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li>
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: left;">How to contact us<span class="p">. You can contact us by:</span></h1>
                <ol id="l5">
                    <li data-list-text="2.7.1.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">telephoning us at 0203 976 1122 <i>Mon to Thu, 09:30-16:00</i>;</p>
                    </li>
                    <li data-list-text="2.7.2.">
                        <p style="padding-top: 1pt; padding-left: 69pt; text-indent: -27pt; text-align: left;">
                            <a href="mailto:support@fundd.org" class="a" target="_blank">emailing us at </a>
                            <span style="color: black; font-family: Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 11pt;">support@fundd.org</span> ; or
                        </p>
                    </li>
                    <li data-list-text="2.7.3.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">using any of the communication methods available:</p>
                        <ol id="l6">
                            <li data-list-text="(a)">
                                <p style="padding-left: 102pt; text-indent: -18pt; text-align: left;">on the secure area of our Website;</p>
                                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                            </li>
                        </ol>
                    </li>
                </ol>
            </li>
            <li data-list-text="2.8.">
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    How we may contact you
                    <span class="p">
                        . If we have to contact you we will do so by telephone or by writing to you at the telephone number, email address or postal address you provided to us or the Distributor. Any changes to your telephone number, email
                        address or postal address or other personal data we hold about you must be notified by you immediately and in writing in accordance with section 2.7.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="3.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Commencement and expiry of these Terms</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l7">
            <li data-list-text="3.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    You shall be deemed to accept these Terms by using the Card. The Card shall remain our property and will be delivered by us, or on our behalf, by the Distributor.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="3.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    The Terms, excluding Section 7.3, will terminate on the expiry date printed on the Card (“<b>Expiry Date</b>”) unless the Card is auto-renewed, in which case you will be issued with a new Card before the existing one
                    expires. In this instance these Terms will remain valid until the existing Card expires or is otherwise as set out in these Terms.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="4.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Issuance and activation of the Card</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l8">
            <li data-list-text="4.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: left;">You may be issued with:</p>
                <p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">
                    4.1.1.a “physical” Card, which will have the details of the PAN, the Expiry Date of the Card and the CVV code printed on it (the “<b>Physical Card</b>”); or
                </p>
                <p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">
                    4.1.2.a “virtual” Card, in which case you will not receive a Physical Card but will receive details of the PAN, the Expiry Date and the CVV2 code (the “<b>Virtual Card</b>”).
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="4.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    In order to start using the Card, you must activate it in accordance with instructions given to you by the Distributor. You must keep your Physical Card and the details of the Virtual Card (as applicable) in a safe place
                    and protect it against unauthorised access or use by third parties.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="4.3.">
                <p style="padding-left: 45pt; text-indent: -21pt; line-height: 13pt; text-align: left;">If you are issued with a Physical Card:</p>
                <ol id="l9">
                    <li data-list-text="4.3.1."><p style="padding-left: 67pt; text-indent: -25pt; line-height: 13pt; text-align: left;">you must sign the Physical Card as soon as you receive it;</p></li>
                    <li data-list-text="4.3.2.">
                        <p style="padding-left: 67pt; text-indent: -25pt; line-height: 13pt; text-align: left;">you must obtain your secret personal identification number (“<b>PIN</b>”) electronically via the</p>
                        <p style="padding-left: 67pt; text-indent: 0pt; text-align: left;">Distributor’s Website or the Distributor’s App.</p>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                </ol>
            </li>
            <li data-list-text="4.4.">
                <p style="padding-top: 2pt; padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    You should memorise your PIN when you receive it. If you need to keep the written version of the PIN or separately write the PIN down for future reference, you must never keep it with the Card. You must never disclose
                    your PIN to any other person, not even us. If you have not protected your PIN and your Card is used without your knowledge using the correct PIN, this may be classed as negligence for the purposes of Section 8.
                </p>
                <p style="padding-left: 6pt; text-indent: 0pt; line-height: 13pt; text-align: left;">1.</p>
            </li>
            <li data-list-text="4.5.">
                <p style="padding-left: 45pt; text-indent: -21pt; line-height: 13pt; text-align: left;">You can manage the Card on your secure area of the Distributor’s Website and on the</p>
                <p style="padding-left: 45pt; text-indent: 0pt; text-align: left;">Distributor’s App.</p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="4.6.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    The Card shall remain valid until the Expiry Date. If you require a replacement Card, please contact the Distributor using the contact details set out in section 2.7. Please note that an additional fee may be charged for
                    a replacement Card - please see the fees section for more information.
                </p>
            </li>
            <li data-list-text="4.7.">
                <p style="padding-top: 1pt; padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    The Card is an e-money product and as such it is not covered by the Financial Services Compensation Scheme. You may only use the Card for lawful Transactions.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="5.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Transactions</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l10">
            <li data-list-text="5.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: left;">You may use your Card to enter into the following transactions (hereinafter referred to as</p>
                <p style="padding-left: 45pt; text-indent: 0pt; text-align: left;">“<b>Transactions</b>”):</p>
                <ol id="l11">
                    <li data-list-text="5.1.1.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">
                            to donate to charities who accept Mastercard, and to only make these donations as agreed with the Charity and set out in your donation agreement.
                        </p>
                    </li>
                    <li data-list-text="5.1.2.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">Donations can only be made in GBP pound sterling.</p>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                </ol>
            </li>
            <li data-list-text="5.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: left;">You can authorise a Transaction by:</p>
                <ol id="l12">
                    <li data-list-text="5.2.1.">
                        <p style="padding-left: 42pt; text-indent: 0pt; text-align: left;">
                            inserting the Card into a chip &amp; PIN device and the correct PIN being entered; 5.2.2.providing relevant information to the merchant that allows the merchant to process the
                        </p>
                        <p style="padding-left: 67pt; text-indent: 0pt; text-align: left;">
                            Transaction, for example, providing the merchant with the PAN, the Expiry Date and the CVV2 in the case of an internet or other non-face-to-face Transaction;
                        </p>
                        <ol id="l13">
                            <ol id="l14">
                                <ol id="l15">
                                    <li data-list-text="5.2.3.">
                                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">
                                            relevant information being provided to the payment initiation service provider that allows the payment initiation service provider to process the Transaction;
                                        </p>
                                    </li>
                                    <li data-list-text="5.2.4."><p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">the Card is tapped against a “contactless” enabled reader and accepted by such reader.</p></li>
                                </ol>
                            </ol>
                        </ol>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                </ol>
            </li>
            <li data-list-text="5.3.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If any of the methods of authorisation set out in section 5.2 are used, we shall be entitled to assume that you have authorised a Transaction unless we were informed that the relevant details of the Card have been lost,
                    stolen or misappropriated prior the Transaction taking place.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="5.4.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: left;">You acknowledge the correctness of the amount of each Transaction which you authorise.</p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="5.5.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Once you have authorised a Transaction, the Transaction cannot be stopped or revoked. You may in certain circumstances be entitled to a refund in accordance with these Terms.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="5.6.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    On receipt of notification of your authorisation of a Transaction and the Transaction payment order from the merchant and/or authorised bank, normally we will deduct the value of the Transaction, plus any applicable fees
                    and charges, from the available funds in the Account. We will execute the Transaction by crediting the account of the merchant’s payment service provider by the end of the next business day following the notification. If
                    the notification is received on a non-business day or after 4:30 pm on a business day, it will be deemed received on the next business day.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="5.7.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We are not liable if, for any reason, the affiliated merchants or authorised banks do not accept the Card, or accept it only partly, nor are we liable in the case of late delivery of, or failure to deliver, goods or
                    services. In the event of disputes or complaints of any kind concerning goods or services, or the exercise of any right in this connection, you should contact the affiliated merchant and/or authorised bank.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="5.8.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    It is your responsibility to ensure that there are available funds in your Account to cover any spend, allowing for any applicable fees under these Terms. Should the Account at any time and for any reason have a negative
                    balance, you shall repay the excess amount immediately and in full.
                </p>
            </li>
            <li data-list-text="5.9.">
                <p style="padding-top: 1pt; padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We and the Distributor have the right to review and change the spending limits on the Card at any time. You will be notified of any such changes via the Distributor’s Website and/or the Distributor’s App.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="6.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Non-execution of a Transaction</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l16">
            <li data-list-text="6.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">In certain circumstances we may refuse to execute a Transaction that you have authorised. These circumstances include:</p>
                <ol id="l17">
                    <li data-list-text="6.1.1.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: justify;">if we have reasonable concerns about the security of the Card or suspect the Card is being used in a fraudulent or unauthorised manner;</p>
                    </li>
                    <li data-list-text="6.1.2.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: justify;">
                            if there are insufficient funds available to cover the Transaction and all associated fees at the time that we receive notification of the Transaction or if there is an outstanding shortfall on the balance of the
                            Account;
                        </p>
                    </li>
                    <li data-list-text="6.1.3.">
                        <p style="padding-left: 42pt; text-indent: 0pt; text-align: justify;">
                            if we have reasonable grounds to believe you are acting in breach of these Terms; 6.1.4.if there are errors, failures (mechanical or otherwise) or refusals by merchants, payment
                        </p>
                        <p style="padding-left: 67pt; text-indent: 0pt; text-align: justify;">processors or payment schemes processing Transactions, or</p>
                        <p style="padding-left: 42pt; text-indent: 0pt; text-align: justify;">6.1.5.if we are required to do so by law.</p>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                </ol>
            </li>
            <li data-list-text="6.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Unless it would be unlawful for us to do so, where we refuse to complete a Transaction, we will notify you as soon as reasonably practicable that it has been refused and the reasons for the refusal, together, where
                    relevant, with the procedure for correcting any factual errors that led to the refusal. Where the refusal is reasonably justified, we may charge you fee when we notify you that your payment request has been refused.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="6.3.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    You may also claim a refund for a Transaction that you authorised provided that your authorisation did not specify the exact amount when you consented to the Transaction, and the amount of the Transaction exceeded the
                    amount that you could reasonably have expected it to be taking into account your previous spending pattern on the Card, these Terms and the relevant circumstances.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="6.4.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Such a refund must be requested from us within 8 weeks of the amount being deducted from the Card. We may require you to provide us with evidence to substantiate your claim. Any refund or justification for refusing a
                    refund will be provided within 10 business days of receiving your refund request or, where applicable, within 10 business days of receiving any further evidence requested by us. Any refund shall be equal to the amount of
                    the Transaction. Any such refund will not be subject to any fee.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="7.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Access to information on Transactions and available funds in the Account</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l18">
            <li data-list-text="7.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    The Distributor has set up a secure area on the Distributor’s Website and on the Distributor’s App where you can view the available balance in your Account and view the details of any Transactions you have entered into.
                    You can gain access to this by following the instructions on the Distributor’s Website or the Distributor’s App. You must keep the credentials to obtain access to the secure areas safe and not disclose them to anyone.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="7.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: left;">We can, upon request, send you monthly information (“e-statement”) by email setting out:</p>
                <ol id="l19">
                    <li data-list-text="7.2.1."><p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">a reference enabling you to identify each Transaction;</p></li>
                    <li data-list-text="7.2.2."><p style="padding-left: 67pt; text-indent: -25pt; text-align: left;">the amount of each Transaction;</p></li>
                    <li data-list-text="7.2.3."><p style="padding-left: 67pt; text-indent: -25pt; line-height: 13pt; text-align: left;">the currency in which the Card is debited;</p></li>
                    <li data-list-text="7.2.4."><p style="padding-left: 67pt; text-indent: -25pt; line-height: 13pt; text-align: left;">the amount of any Transaction charges including their break down, where applicable;</p></li>
                    <li data-list-text="7.2.5.">
                        <p style="padding-top: 1pt; padding-left: 67pt; text-indent: -25pt; text-align: justify;">
                            the exchange rate used in the Transaction by us and the amount of the Transaction after the currency conversion, where applicable; and
                        </p>
                    </li>
                    <li data-list-text="7.2.6.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: justify;">the Transaction debit value date.</p>
                        <p style="padding-left: 42pt; text-indent: 0pt; text-align: justify;">
                            If you would like us to provide you with the e-statement more often than monthly or not by email (or if agreed differently under this section 7, more often than agreed or in a different manner than agreed) then
                            we may charge you a reasonable administration fee to cover our costs of providing the information more often or in a different manner.
                        </p>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                </ol>
            </li>
            <li data-list-text="7.3.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If for any reason you have some available funds left in your Account following the termination of these Terms, they shall be returned to the Distributor.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="8.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Loss of the Card / Transaction refunds</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l20">
            <li data-list-text="8.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    As soon as you become aware of any loss, theft, misappropriation or unauthorised use of the Card, PIN or other security details, you must immediately notify us using the contact details set out in section 2.7.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: left;">In the event of theft, you should consider reporting the theft to the police.</p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.3.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If we believe you did not authorise a particular Transaction or that a Transaction was incorrectly carried out, in order to get a refund you must contact us as soon as you notice the problem using the contact details set
                    out in section 2.7, and in any case no later than 13 months after the amount of the Transaction has been deducted from your Account.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.4.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We will refund any unauthorised Transaction and any associated Transaction fees and charges payable under these Terms subject to the rest of this section 8.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.5.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    This refund shall be made as soon as practicable and in any event no later than the end of the business day following the day on which we become aware of the unauthorised Transaction, unless we have reasonable grounds to
                    suspect fraudulent behaviour and notify the appropriate authorities. If we become aware of the unauthorised Transaction on a non-business day or after 4:30 pm on a business day, we will be deemed to have only become
                    aware of the unauthorised Transaction at the beginning of the next business day.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.6.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If we are liable for an incorrectly executed Transaction, we will immediately refund you the amount of the incorrectly executed Transaction together with and any associated Transaction fees and charges payable under
                    these Terms. Depending on the circumstances, we may require you to complete a dispute declaration form relating to the incorrectly executed Transaction. We may conduct an investigation either before or after any refund
                    has been determined or made. We will let you know as soon as possible the outcome of any such investigation.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.7.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If a Transaction initiated by a merchant (for example, this happens when you use the Card in a shop) has been incorrectly executed and we receive proof from the merchant’s payment service provider that we are liable for
                    the incorrectly executed Transaction, we will refund as appropriate and immediately the Transaction and any associated Transaction fees and charges payable under these Terms.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.8.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We are not liable for any incorrectly executed Transactions if we can show that the payment was actually received by the merchant’s payment service provider, in which case they will be liable.
                </p>
            </li>
            <li data-list-text="8.9.">
                <p style="padding-top: 1pt; padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If you receive a late payment from another payment service provider (e.g. a refund from a retailer’s bank) via us, we will credit the Account with the relevant amount of any associated fees and charges so that you will
                    not be at a loss.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.10.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">We will limit your liability to £35 for any losses incurred in respect of unauthorised Transactions subject to the following:</p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                <ol id="l21">
                    <li data-list-text="8.10.1.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: justify;">
                            you will be liable for all losses incurred in respect of an unauthorised Transaction if you have acted fraudulently, or have intentionally or with gross negligence failed to: (a) look after and use the Card in
                            accordance with these Terms; or (b) notify us of the problem in accordance with this section 8;
                        </p>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                    <li data-list-text="8.10.2.">
                        <p style="padding-left: 78pt; text-indent: -36pt; line-height: 13pt; text-align: justify;">except where you have acted fraudulently, you will not be liable for any losses:</p>
                        <ol id="l22">
                            <li data-list-text="(a)">
                                <p style="padding-left: 112pt; text-indent: -28pt; text-align: justify;">
                                    incurred in respect of an unauthorised Transaction which arises after your notification to us of the loss, theft or misappropriation of the Card;
                                </p>
                            </li>
                            <li data-list-text="(b)"><p style="padding-left: 102pt; text-indent: -18pt; text-align: justify;">arising where you have used the Card in a distance contract, for example, for an online purchase;</p></li>
                            <li data-list-text="(c)">
                                <p style="padding-left: 102pt; text-indent: -18pt; text-align: justify;">
                                    arising where the loss, theft or misappropriation of the Card was not detectable by you before the unauthorised Transaction took place;
                                </p>
                            </li>
                            <li data-list-text="(d)"><p style="padding-left: 102pt; text-indent: -18pt; text-align: justify;">where we have failed to provide you with the appropriate means of notification;</p></li>
                            <li data-list-text="(e)">
                                <p style="padding-left: 102pt; text-indent: -18pt; text-align: justify;">
                                    arising where we are required by law (anticipated to apply from 14 September 2019) to apply Strong Customer Authentication (as defined in section 8.11) but fail to do so;
                                </p>
                            </li>
                            <li data-list-text="(f)">
                                <p style="padding-left: 102pt; text-indent: -18pt; text-align: justify;">
                                    the losses were caused by an act or omission of any employee, agent or branch of ours or any entity which carries out activities on our behalf.
                                </p>
                                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                            </li>
                        </ol>
                    </li>
                </ol>
            </li>
            <li data-list-text="8.11.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    “Strong Customer Authentication” means authentication based on the use of two or more elements that are independent, in that the breach of one element does not compromise the reliability of any other element, and
                    designed in such a way as to protect the confidentiality of the authentication data, with the elements falling into two or more of the following categories: (a) something known only by you (“knowledge”), (b) something
                    held only by you (“possession”); (c) something inherent to you (“inherence”). Strong Customer Authentication it is used to make Transactions more secure.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.12.">
                <p style="padding-left: 78pt; text-indent: -54pt; text-align: justify;">We are required to provide Strong Customer Authentication when:</p>
                <ol id="l23">
                    <li data-list-text="8.12.1.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: justify;">
                            you view the available balance on your Account either through the Distributor’s Website or the Distributor’s App and/or through an account information service provider (“AISP”);
                        </p>
                    </li>
                    <li data-list-text="8.12.2.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: justify;">
                            when you initiate an electronic Transaction, directly [or when you initiate a remote electronic Transaction through a payment initiation service provider (“PISP”)]; or
                        </p>
                    </li>
                    <li data-list-text="8.12.3.">
                        <p style="padding-left: 67pt; text-indent: -25pt; text-align: justify;">when you carry out any action through a remote channel which may imply a risk of payment fraud or other abuses.</p>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                </ol>
            </li>
            <li data-list-text="8.13.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If our investigations show that any disputed Transaction was authorised by you or you may have acted fraudulently or with gross negligence, we may reverse any refund made and you will be liable for all losses we suffer
                    in connection with the Transaction including but not limited to the cost of any investigation carried out by us in relation to the Transaction. We will give you reasonable notice of any reverse refund.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="9.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Blocking of the Card</h1>
        <p style="padding-top: 1pt; padding-left: 41pt; text-indent: 0pt; text-align: justify;">
            We may block the Card, in which case you will not be able to execute any further Transactions, if we have reasonable concerns about the security of the Card or suspect the Card is being used in a fraudulent or unauthorised
            manner. We will notify you of any such blocking in advance, or immediately after if this is not possible, and of the reasons for the suspension unless to do so would compromise reasonable security measures or otherwise be
            unlawful. We will unblock the Card and, where appropriate, issue a new Card, PIN and other security features free of charge as soon as practicable once the reasons for the suspension cease to exist.
        </p>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
    </li>
    <li data-list-text="10.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Data Protection</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l24">
            <li data-list-text="10.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    <a href="http://www.railsr.com/privacy-policy" class="a" target="_blank">You agree that we can use your personal data in accordance with these Terms and our privacy policy, which is set out on https://</a>
                    www.railsr.com/privacy-policy. This privacy policy includes details of the personal information that we collect, how it will be used, and who we pass it to. You can tell us if you do not want to receive any marketing
                    materials from us. For the avoidance of doubt, we will share your personal data with the Distributor.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="10.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    To comply with applicable know-your-client-rules and anti-money laundering regulations (such as the Money Laundering, Terrorist Financing and Transfer of Funds (Information on the Payer) Regulations 2017 and the Proceeds
                    of Crime Act 2002, we and/or the Distributor and/or each of our banking providers and any other business partner (the “<b>Partner</b>”) shall be entitled to carry out all necessary verifications of your identity. The
                    above mentioned Partner and the Distributor may use a recognised agency for this verification purposes (details of the agency used will be provided to you on request). Such verifications will not affect your credit score
                    but may leave a ‘soft footprint’ on your credit history.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="11.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Fees and spending limits</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l25">
            <li data-list-text="11.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">You are liable for paying all fees arising from your use of the Card and subject to all spending limits placed on the Card by us.</p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="11.2.">
                <p style="padding-left: 78pt; text-indent: -54pt; text-align: left;">The fees and spending limits on the Card are set out:</p>
                <ol id="l26">
                    <li data-list-text="11.2.1.">
                        <p style="padding-left: 78pt; text-indent: -36pt; text-align: left;">
                            in the table set out in <u><b>Annex A</b></u><b> </b>attached hereto;
                        </p>
                    </li>
                    <li data-list-text="11.2.2."><p style="padding-left: 78pt; text-indent: -36pt; line-height: 13pt; text-align: left;">on the secure area of the Distributor’s Website; and/or</p></li>
                    <li data-list-text="11.2.3.">
                        <p style="padding-left: 78pt; text-indent: -36pt; line-height: 13pt; text-align: left;">on the Distributor’s App.</p>
                        <p style="text-indent: 0pt; text-align: left;"><br /></p>
                    </li>
                </ol>
            </li>
        </ol>
    </li>
    <li data-list-text="12.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Complaints</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l27">
            <li data-list-text="12.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If you would like to make a complaint relating to these Terms, please contact us using the contact details in section 2.7 so we can resolve the issue. We will promptly send you a complaint acknowledgement and a copy of
                    our complaints procedure.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="12.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Please note that you may request a copy of our complaints procedure at any time. Details of our complaints procedure can also be found on our website. You agree to cooperate with us and provide the necessary information
                    for us to investigate and resolve the complaint as quickly as possible.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="12.3.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We will endeavour to handle your complaint fairly and quickly, however, if you are not satisfied with the outcome, you may contact the Financial Ombudsman Service at Exchange Tower, London E14 9SR; telephone: 0800 023
                    4567 or 0300 123 9 123; website:
                </p>
                <p style="padding-top: 1pt; padding-left: 45pt; text-indent: 0pt; text-align: left;">
                    <a href="mailto:complaint.info@financial-ombudsman.org.uk" class="a" target="_blank">http://www.financial-ombudsman.org.uk; and e-mail: complaint.info@financial-</a>ombudsman.org.uk.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="12.4.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We are a “trader” and “online trader” for the purposes of the Alternative Dispute Resolution for Consumer Disputes (Competent Authorities and Information) Regulations 2015 (“ADR Law”). The Financial Ombudsman Service is
                    the only “ADR entity” we are legally obliged and committed to use in order to resolve disputes with consumers for the purposes of the ADR Law. We do not agree to resolve disputes with consumers using any other ADR entity
                    or similar entity.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="12.5.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    <a href="https://ec.europa.eu/consumers/odr/main/?event=main.adr.show" class="a" target="_blank">The European Commission’s online dispute resolution (“ODR”) platform can be found at </a>
                    <span style="color: black; font-family: Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 11pt;">https://ec.europa.eu/consumers/odr/main/?event=main.adr.show</span>. The
                    ODR platform can be used to resolve disputes between us and consumers.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="13.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Third Party Payment Service Providers</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l28">
            <li data-list-text="13.1.">
                <p style="padding-left: 78pt; text-indent: -54pt; text-align: left;">This section 13 applies when you use the services of an AISP or a PISP.</p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="13.2.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We may deny an AISP or PISP access to the Account for reasonably justified and duly evidenced reasons relating to unauthorised or fraudulent access to the Account by that AISP. If we do deny access in this way, we will
                    notify you of the denial and the reason for the denial in advance if possible, or immediately after the denial of access, unless to do so would compromise reasonably justified security reasons or is unlawful. We will
                    allow AISP [or PISP] access to the Account once the reasons for denying access no longer apply.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="14.">
        <h1 style="padding-left: 24pt; text-indent: -18pt; text-align: left;">Other important terms</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l29">
            <li data-list-text="14.1.">
                <p style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    The Terms and all communications will be in English. You may request a copy of these Terms free of charge at any time during the contractual relationship. If we need to contact you in the event of suspected or actual
                    fraud or security threats, we will first send you an SMS or email prompting you to contact our customer services team using the contact information we have been supplied with.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="14.2.">
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    We may transfer this agreement to someone else
                    <span class="p">
                        . We may transfer our rights and obligations under these Terms to another organisation. We will always tell you in writing if this happens and we will ensure that the transfer will not affect your rights under the
                        contract.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="14.3.">
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    You need our consent to transfer your rights to someone else<span class="p">. You may only transfer your rights or your obligations under these Terms to another person if we agree to this in writing.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="14.4.">
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Nobody else has any rights under this contract. <span class="p">This contract is between you and us. No other person shall have any rights to enforce any of its terms.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="14.5.">
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Changes to these Terms / Termination
                    <span class="p">
                        . We reserve the right to amend these Terms for any reason by giving you two-months’ notice by e-mail. You will be deemed to have accepted the changes if you raise no objection prior to the expiry of the period set
                        out in the notice. If you do not wish to accept the changes, you may terminate these Terms immediately and without charge by proving us with notice at any time prior to the expiry of the notice
                    </span>
                </h1>
                <p style="padding-top: 1pt; padding-left: 45pt; text-indent: 0pt; text-align: justify;">
                    period. At all other times you may terminate these Terms at any time by giving us one months’ notice in accordance with section 2.7 and we may terminate these Terms by giving you two months’ notice in accordance with
                    section 2.8.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="14.6.">
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    If a court finds part of this contract illegal, the rest will continue in force
                    <span class="p">
                        . Each of the sections and sub-sections of these Terms operate separately. If any court or relevant authority decides that any of them are unlawful, the remaining paragraphs will remain in full force and effect.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="14.7.">
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Even if we delay in enforcing this contract, we can still enforce it later
                    <span class="p">
                        . If we do not insist immediately that you do anything you are required to do under these Terms, or if we delay in taking steps against you in respect of your breaking this contract, that will not mean that you do
                        not have to do those things and it will not prevent us taking steps against you at a later date. For example, if you do not pay us on time and we do not chase you but we continue to provide the services, we can still
                        require you to make the payment at a later date.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="14.8.">
                <h1 style="padding-left: 45pt; text-indent: -21pt; text-align: justify;">
                    Which laws apply to this contract and where you may bring legal proceedings
                    <span class="p">
                        . These Terms are governed by English law and you can bring legal proceedings in respect of these Terms in the English courts. If you live in Scotland you can bring legal proceedings in respect of these Terms in
                        either the Scottish or the English courts. If you live in Northern Ireland you can bring legal proceedings in respect of these Terms in either the Northern Irish or the English courts.
                    </span>
                </h1>
            </li>
        </ol>
    </li>
</ol>
<p class="s5" style="padding-top: 1pt; padding-left: 6pt; text-indent: 0pt; text-align: left;">Annex A – Fees Table</p>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<table style="border-collapse: collapse; margin-left: 6.75399pt;" cellspacing="0">
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s6" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Card Fees</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p style="text-indent: 0pt; text-align: left;"><br /></p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Card issue fee</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">No fee</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Replacement card</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">No fee</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Cancellation of card</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">No fee</p>
        </td>
    </tr>
    <tr style="height: 37pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p style="text-indent: 0pt; text-align: left;"><br /></p>
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; text-align: left;">Top-up fee</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 7pt; padding-right: 6pt; text-indent: 0pt; line-height: 12pt; text-align: center;">N/A, card can only be loaded by the Charity that gave you the Card</p>
        </td>
    </tr>
    <tr style="height: 49pt;">
        <td
            style="width: 431pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;"
            colspan="2"
        >
            <p class="s6" style="padding-left: 5pt; text-indent: 0pt; line-height: 12pt; text-align: left;">Usage restrictions:</p>
            <p class="s7" style="padding-left: 5pt; padding-right: 284pt; text-indent: 0pt; text-align: left;">Over 18’s and UK residents only. No cash access.</p>
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Card can only be used to make donations at Charities accepting Mastercard</p>
        </td>
    </tr>
    <tr style="height: 25pt;">
        <td
            style="width: 431pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;"
            colspan="2"
        >
            <p class="s6" style="padding-left: 5pt; text-indent: 0pt; line-height: 12pt; text-align: left;">Expiry:</p>
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Card valid for 3 years</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s6" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Transaction Fees</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p style="text-indent: 0pt; text-align: left;"><br /></p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Fee for purchases in the currency of the card</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">No fee</p>
        </td>
    </tr>
    <tr style="height: 25pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; padding-right: 6pt; text-indent: 0pt; line-height: 12pt; text-align: left;">Fee for purchases not in currency of card (foreign exchange service charge)</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; text-align: center;">Not permitted</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s6" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Cash Withdrawal Fees</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">ATM not permitted</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s6" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Card limits – Loading</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">N/A</p>
        </td>
    </tr>
    <tr style="height: 25pt;">
        <td
            style="width: 431pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;"
            colspan="2"
        >
            <p class="s6" style="padding-left: 5pt; text-indent: 0pt; line-height: 12pt; text-align: left;">Transaction limits:</p>
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Your transactions are limited by the value loaded on the card. Above this the following limits apply:</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Minimum single card payment</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">£2.00</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Maximum single card payment</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">£10,000</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Maximum daily card payment</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">£10,000</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Maximum daily number of transactions</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">20</p>
        </td>
    </tr>
    <tr style="height: 13pt;">
        <td style="width: 310pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 11pt; text-align: left;">Maximum cumulative spend over 4 days</p>
        </td>
        <td style="width: 121pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
            <p class="s7" style="padding-left: 19pt; padding-right: 19pt; text-indent: 0pt; line-height: 11pt; text-align: center;">£15,000</p>
        </td>
    </tr>
</table>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<h1 style="padding-left: 5pt; text-indent: 0pt; line-height: 31pt; text-align: left;">PAYRNET LIMITED TERMS AND CONDITIONS FOR CONSUMERS ELECTRONIC MONEY ACCOUNTS BACKGROUND</h1>
<p style="padding-left: 6pt; text-indent: 0pt; text-align: left;">
    This Agreement: This Agreement is with PayrNet Limited, a company incorporated in England and Wales (company number: 09883437) with its registered office at &quot;1 Snowden Street, London,
</p>
<p style="padding-left: 6pt; text-indent: 0pt; line-height: 13pt; text-align: left;">England, EC2A 2DQ&quot; (hereinafter referred to in this Agreement as “Payrnet”, “we” “us”). We are an</p>
<p style="padding-left: 6pt; text-indent: 0pt; text-align: left;">
    Electronic Money Institution (“EMI”) and are authorised by the Financial Conduct Authority under the Electronic Money Regulations 2011 (“EMR 2011”) (register reference 900594) for the issuing of electronic money.
</p>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<p style="padding-left: 6pt; text-indent: 0pt; text-align: left;">
    Our relationship with Tevini: As an EMI, we have appointed QPAY (trading as Quantum Card Services Ltd) as a distributor of our services who manage the program on behalf of Tevini. A distributor means a person who distributes or redeems
    electronic money on behalf of an electronic money institution but who does not provide payment services on behalf of the electronic money institution (as distributor is defined in the EMR 2011).
</p>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<h1 style="padding-left: 5pt; text-indent: 0pt; text-align: left;">AGREED TERMS</h1>
<p style="text-indent: 0pt; text-align: left;"><br /></p>
<ol id="l30">
    <li data-list-text="1.">
        <h1 style="padding-left: 42pt; text-indent: -36pt; text-align: left;">OUR TERMS</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l31">
            <li data-list-text="1.1.">
                <h1 style="padding-left: 42pt; text-indent: -36pt; text-align: left;">Interpreting this Agreement. <span class="p">In order to easily understand the terms of this Agreement,</span></h1>
                <p style="padding-top: 1pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    please first refer to clause 3 which, amongst other things, sets out the meaning of capitalised terms used in this Agreement.
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="1.2.">
                <h1 style="padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Why should you read it?
                    <span class="p">
                        Please read this Agreement carefully before you agree to it, as its terms apply to the services provided by us. The Agreement explains many of your responsibilities to us and our responsibilities to you, how and when
                        this Agreement can be terminated and the extent of our liability to you. If there are any terms that you do not understand or do not wish to agree to, please contact us. You should only activate your card and agree
                        to the terms of this Agreement if you agree to be bound by this Agreement.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="2.">
        <h1 style="padding-left: 42pt; text-indent: -36pt; text-align: left;">INFORMATION ABOUT US AND HOW TO CONTACT US</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l32">
            <li data-list-text="2.1.">
                <h1 style="padding-left: 42pt; text-indent: -36pt; text-align: left;">Who we are. <span class="p">We are PayrNet Limited, an EMI as described above.</span></h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="2.2.">
                <h1 style="padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Communications between us are to be in English. <span class="p">This Agreement is concluded in England and all communications between you and us shall be in English only.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="2.3.">
                <h1 style="padding-left: 6pt; text-indent: 0pt; text-align: left;">
                    How to contact us. <span class="p">All queries should be directed towards Tevini. You can contact Tevini using details set out in 2.7 of the Consumer cardholder terms above.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="2.4.">
                <h1 style="padding-left: 6pt; text-indent: 0pt; line-height: 109%; text-align: left;">
                    How we may contact you.
                    <span class="p">
                        If we have to contact you we will do so as follows: in the first instance via Tevini except in urgent cases. If we have not been able to contact you through Tevini or if the matter is urgent, we will contact you by
                        writing to you at the email address(es), you provided when agreeing to this Agreement or by using any other contact details you have provided to us or have used in communications with us or Tevini.
                    </span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="2.5.">
                <h1 style="padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    ‘Writing’ includes emails. <span class="p">When we use the words “writing” or “written” in this Agreement, this includes emails.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="2.6.">
                <h1 style="padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    Some of the services we provide are subject to the Payment Services Regulations 2017.
                    <span class="p">The Regulations regulate how payments must be transmitted and provide protection for the clients of authorised payment institutions and electronic money institutions.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
        </ol>
    </li>
    <li data-list-text="3.">
        <h1 style="padding-left: 42pt; text-indent: -36pt; text-align: left;">INTERPRETATION</h1>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <ol id="l33">
            <li data-list-text="3.1.">
                <p style="padding-left: 42pt; text-indent: -36pt; text-align: left;">The definitions set out in this clause apply in this Agreement as follows:</p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                <h1 style="padding-left: 5pt; text-indent: 0pt; text-align: left;">“Agreement” <span class="p">means this agreement and the privacy policy.</span></h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                <h1 style="padding-left: 6pt; text-indent: 0pt; text-align: left;">
                    “Consumer“ <span class="p">means an individual who, in entering into this Agreement, is acting for a purpose other than a trade, business or profession.</span>
                </h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                <h1 style="padding-left: 6pt; text-indent: 0pt; text-align: left;">“Electronic Money” <span class="p">means electronically stored monetary value as represented by a claim against us.</span></h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                <h1 style="padding-left: 5pt; text-indent: 0pt; text-align: left;">“Regulations” <span class="p">means the Payment Services Regulations 2017 (SI 2017 No. 752).</span></h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                <h1 style="padding-left: 6pt; text-indent: 0pt; text-align: left;">
                    “Safeguarded Account”
                    <span class="p">
                        means the bank account(s) belonging to us, which are separate to our own office bank accounts, into which we will receive money from you, or on your behalf, in return for the issuance of Electronic Money.
                    </span>
                </h1>
                <h1 style="padding-top: 1pt; padding-left: 5pt; text-indent: 0pt; text-align: left;">“Services” <span class="p">means the e-money account services.</span></h1>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
                <h1 style="padding-left: 5pt; text-indent: 0pt; text-align: left;">
                    “Website” <a href="http://www.tevini.co.uk/" class="a" target="_blank">means our website https://</a><a href="http://www.tevini.co.uk/" target="_blank">www.tevini.co.uk/</a>
                </h1>
            </li>
            <li data-list-text="3.2.">
                <p style="padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">Clause headings shall not affect the interpretation of this Agreement and references to clauses are to the clauses of this Agreement.</p>
            </li>
            <li data-list-text="3.3.">
                <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    Any words following the terms including, include, in particular, for example or any similar expression shall be construed as illustrative and shall not limit the sense of the words, description, definition, phrase or
                    term preceding those terms.
                </p>
            </li>
            <li data-list-text="3.4.">
                <p style="padding-top: 8pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Unless the context otherwise requires, words in the singular shall include the plural and in the plural shall include the singular.
                </p>
            </li>
            <li data-list-text="3.5.">
                <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    A reference to a statute or statutory provision is a reference to it as amended, extended or reenacted from time to time and reference to a statute or statutory provision shall include all subordinate legislation made
                    from time to time.
                </p>
            </li>
        </ol>
    </li>
    <li data-list-text="4.">
        <h1 style="padding-top: 8pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">TERM AND BECOMING A CLIENT</h1>
        <ol id="l34">
            <li data-list-text="4.1.">
                <h1 style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    How can you agree to this Agreement? <span class="p">You can agree to this Agreement by checking the box online confirming that you agree to the terms and conditions during the card activation process.</span>
                </h1>
            </li>
            <li data-list-text="4.2.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    When will you become a client of ours?
                    <span class="p">You will be bound by this Agreement once you have agreed to it as set out above and this Agreement shall remain in force until terminated in accordance with its terms.</span>
                </h1>
            </li>
        </ol>
    </li>
    <li data-list-text="5.">
        <h1 style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">SERVICES</h1>
        <ol id="l35">
            <li data-list-text="5.1.">
                <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 110%; text-align: left;">
                    As part of the Services, we shall issue you with Electronic Money upon receipt of money from you or a third party on your behalf, store your Electronic Money and redeem Electronic Money both on your express instruction
                    and in accordance with this Agreement and the agreement of the Tevini.
                </p>
            </li>
            <li data-list-text="5.2.">
                <h1 style="padding-top: 8pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    Our Services do not include the provision of advice.
                    <span class="p">We do not offer advice under this Agreement on any matter including (without limit) the merits or otherwise of any currency transactions, on taxation, or markets.</span>
                </h1>
            </li>
        </ol>
    </li>
    <li data-list-text="6.">
        <h1 style="padding-top: 8pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">ISSUING ELECTRONIC MONEY TO YOU</h1>
        <ol id="l36">
            <li data-list-text="6.1.">
                <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Where we receive money from you or on your behalf, this money will be held by us in the relevant Safeguarded Account in exchange for the issuance by us to you of Electronic Money. Your funds will not be used by us for
                    any other purpose and in the unlikely event that we become insolvent, your e-money is protected in an EEA-authorised credit institution or the Bank of England.
                </p>
            </li>
            <li data-list-text="6.2.">
                <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    When we issue you with Electronic Money, us holding the funds corresponding to the Electronic Money is not the same as a Bank holding your money in that (i) we cannot and will not use the funds to invest or lend to other
                    persons or entities; (ii) the Electronic Money will not accrue interest and (iii) the Electronic Money is not a deposit and is therefore not covered by the Financial Services Compensation Scheme but it is held by us and
                    protected in the relevant Safeguarded Account.
                </p>
            </li>
            <li data-list-text="6.3.">
                <p style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">You may hold Electronic Money and we may hold funds corresponding to your Electronic</p>
                <p style="padding-top: 1pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    Money indefinitely. However, if we hold Electronic Money for you for more than two years <i>without </i>any activity on the account, we shall use reasonable endeavours to contact you to redeem the Electronic Money and
                    return the corresponding funds to you. If we are unable to contact you, we may redeem the Electronic Money and send the corresponding funds, less any of our costs incurred, to the last known bank account we have on file
                    for you.
                </p>
            </li>
            <li data-list-text="6.4."><p style="padding-top: 8pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">We accept no responsibility in the event that you send money to the incorrect account.</p></li>
            <li data-list-text="6.5.">
                <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    We do not accept cash or cheques. We accept monies via a variety of methods of electronic funds transfer to our bank account, the details of which we shall provide to you upon request.
                </p>
            </li>
        </ol>
    </li>
    <li data-list-text="7.">
        <h1 style="padding-top: 8pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">GENERAL LIMITATION OF LIABILITY</h1>
        <ol id="l37">
            <li data-list-text="7.1.">
                <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    Where we and another person (such as a payment services provider) are liable to you in respect of the same matter or item, you agree that our liability to you will not be increased by any limitation of liability you have
                    agreed with that other person or because of your inability to recover from that other person beyond what our liability would have been had no such limitation been agreed and/or if that other person had paid his or its
                    share.
                </p>
            </li>
            <li data-list-text="7.2.">
                <p style="padding-top: 8pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Where any loss, liability, cost or expense (a “Loss”) is suffered by you for which we would otherwise be jointly and severally or jointly liable with any third party or third parties, the extent to which such Loss shall
                    be recoverable by you from us (as opposed to any third parties) shall be limited so as to be in proportion to the aggregate of our contribution to the overall fault for such Loss, as agreed between all of the relevant
                    parties or, in the absence of agreement, as determined by a court of competent jurisdiction. For the purposes of assessing the contribution to the Loss in question of any third party for the purposes of this clause, no
                    account shall be taken of any limit imposed or agreed on the amount of liability of such third party by any agreement (including any settlement agreement) made before or after such Loss occurred or was otherwise
                    incurred.
                </p>
            </li>
            <li data-list-text="7.3.">
                <p style="padding-top: 6pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Nothing in this Agreement limits or excludes our liability for death or personal injury caused by our negligence or for any damage or liability incurred by you as a result of fraud or fraudulent misrepresentation by us
                    or to the extent that the liability may not be excluded or limited by any applicable law.
                </p>
            </li>
        </ol>
    </li>
    <li data-list-text="8.">
        <h1 style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">COMPLAINTS</h1>
        <ol id="l38">
            <li data-list-text="8.1.">
                <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 110%; text-align: left;">
                    <a href="mailto:contactus@qcs-uk.com" class="a" target="_blank">
                        If you feel that we have not met your expectations in the delivery of our Services, in the first instance contact Tevini using the contact email address for complaints set out in 2.7 of the Consumer cardholder terms
                        above. If Tevini does not deal with your complaint adequately, please contact QPAY via email to
                    </a>
                    <a href="mailto:complaints@payr.net" class="a" target="_blank">contactus@qcs-uk.com. If your complaint it still not dealt with adequately please contact us at </a>
                    <a href="mailto:complaints@payr.net" class="s8" target="_blank">complaints@payr.net</a><a href="mailto:complaints@payr.net" target="_blank">.</a>
                </p>
                <p style="text-indent: 0pt; text-align: left;"><br /></p>
            </li>
            <li data-list-text="8.2.">
                <p style="padding-top: 2pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    We have internal procedures for handling complaints fairly and promptly in accordance with the Financial Conduct Authority’s requirements. A copy of our complaints procedure is available upon request.
                </p>
            </li>
            <li data-list-text="8.3.">
                <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    <a href="http://www.financialombudsman.org.uk/" class="a" target="_blank">
                        If you are an eligible complainant you may be able to take your complaint to the Financial Ombudsman Service should you not be satisfied with our final response. Eligibility criteria and information on the procedures
                        involved are available from
                    </a>
                    <a href="http://www.financialombudsman.org.uk/" class="s8" target="_blank">http://www.financialombudsman.org.uk</a>. In addition, please note that disputes may be submitted for online resolution to the European
                    Commission Online Dispute Resolution platform.
                </p>
            </li>
        </ol>
    </li>
    <li data-list-text="9.">
        <h1 style="padding-top: 1pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">ESTABLISHING YOUR IDENTITY</h1>
        <ol id="l39">
            <li data-list-text="9.1.">
                <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    To comply with the requirements of the Money Laundering, Terrorist Financing and Transfer of Funds (Information on the Payer) Regulations 2017, the Proceeds of Crime Act 2002 and EU Wire Transfer Regulations (Regulation
                    (EU) 2015/847) and related regulations, it may be necessary to obtain from you, and retain, evidence of your personal identity in our records from time to time. If satisfactory evidence is not promptly provided to us we
                    cannot accept your instructions.
                </p>
            </li>
            <li data-list-text="9.2.">
                <p style="padding-top: 8pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    We may keep records of the contents and results of any searches that we carry out on you in accordance with all current and applicable laws. You acknowledge that us carrying out an electronic verification check or, if
                    required, a credit reference agency check will leave a soft footprint on your credit history.
                </p>
            </li>
            <li data-list-text="9.3.">
                <p style="padding-top: 8pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    We are obliged to report any reasonable suspicions about activities on the electronic accounts to the regulatory authorities. This may affect our relationship with you so far as confidentiality is concerned. If we are
                    required under legislation (including the Money Laundering, Terrorist Financing and Transfer of Funds (Information on the Payer) Regulations 2017 and the Proceeds of Crime Act 2002) to refrain from communicating with you
                    and/or proceeding with your instructions, we can accept no liability for the consequences of being prevented from doing so.
                </p>
            </li>
            <li data-list-text="9.4.">
                <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    The personal information we have collected from you will be shared with fraud prevention agencies who will use it to prevent fraud and money-laundering and to verify your identity. If fraud is detected, you could be
                    refused certain services, finance, or employment. Further details of how your information will be used by us and these fraud prevention agencies, and your data protection rights, can be found in our privacy policy.
                </p>
            </li>
        </ol>
    </li>
    <li data-list-text="10.">
        <h1 style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">TERMINATION</h1>
        <ol id="l40">
            <li data-list-text="10.1.">
                <h1 style="padding-top: 9pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">When we may terminate or suspend this Agreement.</h1>
                <ol id="l41">
                    <li data-list-text="10.1.1.">
                        <p style="padding-top: 9pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">We can terminate this Agreement at any time:</p>
                        <ol id="l42">
                            <li data-list-text="10.1.1.1."><p style="padding-top: 9pt; padding-left: 78pt; text-indent: -72pt; text-align: left;">If you breach this Agreement; and/or</p></li>
                            <li data-list-text="10.1.1.2."><p style="padding-top: 9pt; padding-left: 78pt; text-indent: -72pt; text-align: left;">if we suspect that you are using the Services for any illegal purposes.</p></li>
                        </ol>
                    </li>
                    <li data-list-text="10.1.2.">
                        <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                            We may suspend or terminate your access to the Services where we have reasonable grounds for concern relating to: (i) the security of your account(s), whether or not you have informed us of a security breach;
                            and/or (ii) the suspected unauthorised or fraudulent use of your account(s).
                        </p>
                    </li>
                    <li data-list-text="10.1.3.">
                        <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                            If Tevini notifies us that its agreement with you has terminated we can terminate this agreement with immediate effect.
                        </p>
                    </li>
                    <li data-list-text="10.1.4.">
                        <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                            If you terminate your agreement with Tevini, or that agreement is terminated, we can terminate this Agreement with immediate effect.
                        </p>
                    </li>
                    <li data-list-text="10.1.5.">
                        <p style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">If our agreement with Tevini terminates, we will give you not less than two (2) month’s</p>
                        <p style="padding-top: 1pt; padding-left: 6pt; text-indent: 0pt; text-align: left;">written notice to terminate this Agreement.</p>
                    </li>
                    <li data-list-text="10.1.6.">
                        <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                            We may terminate this Agreement at any time and for any reason by giving you not less than two (2) month’s written notice.
                        </p>
                    </li>
                </ol>
            </li>
            <li data-list-text="10.2.">
                <p style="padding-top: 1pt; padding-left: 6pt; text-indent: 0pt; line-height: 110%; text-align: justify;">
                    When you may terminate this Agreement. You can terminate this Agreement at any time and for any reason by cancelling your agreement with Tevini. We may contact you to confirm your request.
                </p>
            </li>
            <li data-list-text="10.3.">
                <p style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">Effect of Termination. Upon the effective date of termination:</p>
                <ol id="l43">
                    <li data-list-text="10.3.1."><p style="padding-top: 9pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">you will no longer be able to avail yourself of the Services;</p></li>
                    <li data-list-text="10.3.2.">
                        <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                            we shall redeem any Electronic Money we hold for you and send the equivalent funds to a bank account in your name, unless agreed by both parties, less any monies which are due and owing to us.
                        </p>
                    </li>
                </ol>
            </li>
            <li data-list-text="10.4.">
                <p style="padding-top: 8pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    After termination, you may contact us using the contact details set out in clause 2.3 to redeem any Electronic Money you still hold with us.
                </p>
            </li>
        </ol>
    </li>
    <li data-list-text="11.">
        <h1 style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">CONFIDENTIALITY</h1>
        <ol id="l44">
            <li data-list-text="11.1.">
                <p style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    We undertake that we shall not at any time, disclose to any person any of your confidential information, except in the following circumstances:
                </p>
                <ol id="l45">
                    <li data-list-text="11.1.1.">
                        <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: justify;">
                            to our employees, officers, representatives or advisers who need to know such information for the purposes of exercising our rights or carrying out our obligations under or in connection with this Agreement. We
                            shall ensure that our employees, officers, representatives or advisers to whom we disclose your confidential information comply with this clause; and
                        </p>
                    </li>
                    <li data-list-text="11.1.2.">
                        <p style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">as may be required by law, a court of competent jurisdiction or any governmental or regulatory authority.</p>
                    </li>
                </ol>
            </li>
        </ol>
    </li>
    <li data-list-text="12.">
        <h1 style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">HOW WE MAY USE YOUR PERSONAL INFORMATION</h1>
        <ol id="l46">
            <li data-list-text="12.1.">
                <h1 style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    How we may use your personal information. <a href="https://railsr.com/payrnet" class="a" target="_blank">We will only use your personal information as set out in our privacy policy which can be found</a>
                    <span style="color: #1153cc; font-family: Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 11pt;"> https://railsr.com/payrnet</span>
                    <span class="p">. (Payrnet is a wholly owned subsidiary of Railsbank Technology Limited).</span>
                </h1>
            </li>
        </ol>
    </li>
    <li data-list-text="13.">
        <h1 style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">GENERAL</h1>
        <ol id="l47">
            <li data-list-text="13.1.">
                <h1 style="padding-top: 9pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Recording of telephone conversations.
                    <span class="p">
                        We may record telephone conversations with or without use of a warning tone and we may use these recordings as evidence for a particular purpose or in relation to disputes as well as for our ongoing quality control
                        and training programme. We may also maintain a record of all emails sent by or to us. All those recordings and records will be maintained at our absolute discretion and are our property and can be used by us in the
                        case of a dispute. We do not guarantee that we will maintain such recordings or records or be able to make them available to you. You consent to the use and admissibility of any such recording as evidence in any
                        dispute or anticipated dispute between the parties which relates to the dealings between the parties.
                    </span>
                </h1>
            </li>
            <li data-list-text="13.2.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Ensuring this Agreement is legally enforceable.
                    <span class="p">
                        For a contract to be legally enforceable, there needs to be an offer, acceptance, and consideration. This Agreement constitutes our offer to make the Services available to you and you agreeing to this Agreement
                        constitutes your acceptance of this offer. In order to ensure that this Agreement is legally binding, upon you becoming a client, you promise to pay us the sum of onePound sterling, upon demand from us, as
                        consideration.
                    </span>
                </h1>
            </li>
            <li data-list-text="13.3.">
                <h1 style="padding-top: 7pt; padding-left: 42pt; text-indent: -36pt; text-align: left;">Even if we delay in enforcing under this Agreement, we can still enforce it later. <span class="p">If we do</span></h1>
                <p style="padding-top: 1pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    not insist immediately that you do anything you are required to do under this Agreement, or if we delay in taking steps against you in respect of your breach of this Agreement, that will not mean that you do not have to
                    do those things and it will not prevent us taking steps against you at a later date.
                </p>
            </li>
            <li data-list-text="13.4.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    What if something unexpected happens?
                    <span class="p">
                        We shall have no liability to you under this Agreement or any Contract if we are prevented from or delayed in performing our obligations under this Agreement, or from carrying on our business, by acts, events,
                        omissions or accidents beyond our reasonable control, including, without limitation, strikes, lockouts or other industrial disputes (whether involving us or any other party), failure of a utility service or transport
                        or telecommunications network, act of God, war, riot, civil commotion, malicious damage, compliance with any law or governmental order, rule, regulation or direction, accident, breakdown of plant or machinery, fire,
                        flood, storm or our default of subcontractors, provided that you are notified of such an event and its expected duration.
                    </span>
                </h1>
            </li>
            <li data-list-text="13.5.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    If a court finds part of this Agreement illegal, the rest will continue in force.
                    <span class="p">
                        Each of the subclauses, clauses and paragraphs of this Agreement operates separately. If any court or relevant authority decides that any of them are unlawful, the remaining subclauses, clauses and paragraphs will
                        remain in full force and effect.
                    </span>
                </h1>
            </li>
            <li data-list-text="13.6.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    We are not partners and neither of us may act as the other’s agent.
                    <span class="p">
                        Nothing in this Agreement is intended to or shall operate to create a partnership or joint venture between you and us, or authorise either party to act as agent for the other, and neither party shall have the
                        authority to act in the name or on behalf of or otherwise to bind the other in any way (including, but not limited to, the making of any representation or warranty, the assumption of any obligation or liability and
                        the exercise of any right or power).
                    </span>
                </h1>
            </li>
            <li data-list-text="13.7.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    We can make amendments to this Agreement.
                    <span class="p">
                        We shall have the right to make such amendments to this Agreement, via Tevini, as are necessary to comply with any laws and regulations that are applicable to the performance of our obligations under this Agreement
                        where such laws and regulations are implemented and/or amended after the date of this Agreement. Such amendments may be made at any time upon as much notice as possible to you and shall take effect following such
                        notice, if any. If you object to the proposed amendments, you have the right to terminate this Agreement without charge before the date proposed by us for the entry into force of the changes. You will be deemed to
                        have accepted the proposed amendments unless you notify us and terminate this Agreement before the date proposed by us for the entry into force of the changes. If we receive no objection from you, such amendments
                        shall take effect from the date specified by us but may not affect any rights or obligations that have already arisen and will not be retrospective.
                    </span>
                </h1>
            </li>
            <li data-list-text="13.8.">
                <h1 style="padding-top: 8pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: justify;">
                    What happens if you are jointly a client of ours with another person?
                    <span class="p">Where you comprise two or more people, each person will be jointly and severally liable to us in respect of all obligations contained in this Agreement.</span>
                </h1>
            </li>
            <li data-list-text="13.9.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">
                    Can you obtain a copy of this Agreement or additional information?
                    <span class="p">You may request and we shall provide a copy of this Agreement and any information set out in Schedule 4 of the Regulations (if relevant) at any time prior to termination of this Agreement.</span>
                </h1>
            </li>
            <li data-list-text="13.10.">
                <h1 style="padding-top: 8pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: justify;">
                    We may transfer this agreement to someone else. <span class="p">We may transfer our rights and obligations under this Agreement to another organisation without your consent. We will always tell</span>
                </h1>
                <p style="padding-top: 1pt; padding-left: 6pt; text-indent: 0pt; line-height: 111%; text-align: left;">you in writing if this happens and we will ensure that the transfer will not affect your rights under this Agreement.</p>
            </li>
            <li data-list-text="13.11.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: justify;">
                    You need our consent to transfer your rights to someone else (except that you can always transfer our guarantee).
                    <span class="p">You may only transfer your rights or your obligations under this Agreement to another person if we agree to this in writing.</span>
                </h1>
            </li>
            <li data-list-text="13.12.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: justify;">
                    Nobody else has any rights under this Agreement. <span class="p">This contract is between you and us. No other person shall have any rights to enforce any of its terms.</span>
                </h1>
            </li>
            <li data-list-text="13.13.">
                <h1 style="padding-top: 7pt; padding-left: 6pt; text-indent: 0pt; line-height: 112%; text-align: left;">
                    Which laws apply to this Agreement and where you may bring legal proceedings.
                    <span class="p">
                        These terms are governed by English law and you can bring legal proceedings in respect of the products in the English courts. If you live in Scotland you can bring legal proceedings in respect of the Services in
                        either the Scottish or the English courts. If you live in Northern Ireland you can bring legal proceedings in respect of the Services in either the Northern Irish or the English courts.
                    </span>
                </h1>
            </li>
        </ol>
    </li>
</ol>


</div>
@endsection


