@extends('frontend.layouts.user')

@section('content')


<div id="main-container" class="">
    <!-- Google Charts -->
  <script type="text/javascript">


          function drawChart1640603711() {


            var data = new google.visualization.DataTable();
            data.addColumn('number', '');
            data.addColumn('number', '');

      var data = google.visualization.arrayToDataTable([
                ['', ''],
                ['Apr 21',  0],['May 21',  0],['Jun 21',  0],['Jul 21',  0],['Aug 21',  0],['Sep 21',  0],['Oct 21',  0],['Nov 21',  0],['Dec 21',  0]                ]);

var formatter = new google.visualization.NumberFormat(
    {negativeColor: 'red', negativeParens: true, pattern: "\u00a3"+'###,###'});
 formatter.format(data, 1);

            var options = {
              series: {
                  0: { color: '#00ADEE' }
              },
                legend: {position: 'none'},
                vAxis: {format: '£#,###'}

            };

            var chart = new google.charts.Line(document.getElementById('chart_div1640603711'));

            chart.draw(data, google.charts.Line.convertOptions(options));
          }

      $(document).ready(function(){
      google.charts.load('current', {'packages':['line']});
            google.charts.setOnLoadCallback(drawChart1640603711);

          });

          /*google.charts.load('current', {'packages':['corechart', 'line']});
            google.charts.setOnLoadCallback(drawChart);

          function drawChart() {

            var data = new google.visualization.DataTable();
            data.addColumn('number', '');
            data.addColumn('number', '');

      var data = google.visualization.arrayToDataTable([
                ['', '', {'type': 'string', 'role': 'style'}],
                ['APR',  4100,'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['MAY',  4200, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['JUN',  3500,'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['JUL',  3700, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['AGO',  2900, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['SEP',  4900, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['OCT',  4100, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['NOV',  4900, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['DEC',  3100, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['JAN',  3200, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['FEB',  3000, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],
                ['MAR',  4000, 'point { size: 8; stroke-color: #fff; stroke-width: 2 }'],

              ]);

            var options = {
            legend: 'none',
               colors: ['#00ADEE'],
                lineWidth: 5,
                pointSize: 10,
            };

            var chart =new google.visualization.LineChart(document.getElementById('chart_div'));

            chart.draw(data, options);
          } */


  </script>

<main class="main-givings content-desktop">
  <div class="header-fixed visible-xs">
          <header class="header">
              <div class="container ">
                  <div class="row">
                      <div class="header-mobile-transactions">
                          <div class="col-xs-2">
                              <a href="dashboard.php" class="go-back guideLinr-element-highlight">
                                  <i class="fa fa-angle-left" aria-hidden="true"></i>
                              </a>
                          </div><!-- /col -->
                          <div class="col-xs-8">
                              <h2 class="title">My Givings</h2>
                          </div><!-- /col -->
                          <div class="col-xs-2">
                              <a href="#" class="nav-mobile nav-icon4 visible-xs ">
                                  <span></span>
                                  <span></span>
                                  <span></span>
                              </a>
                          </div><!-- /col -->
                      <div class="col-xs-12 header-mobile-transactions">
                      <ul class="nav-transactions transaction_page_mobile">
                          <li class="nav-transactions-li"> <a href="?period=thisyear" class="ajaxlink nav-transactions-lkn active">THIS YEAR</a> </li>
                          <li class="nav-transactions-li"> <a href="?period=alltime" class="ajaxlink nav-transactions-lkn ">ALL TIME</a> </li>
                      </ul>
                  </div>
                  </div><!-- /row  -->
              </div><!-- /container  -->
          </div></header>
  </div>
  <div class="hidden-xs">
      <div class="row">
          <div class="col-md-12">
              <h2 class="title-givings">My Giving</h2>
              <ul class="nav-givings">
                  <li class="nav-givings-li">
                      <a href="?period=thisyear" id="current" class="ajaxlink nav-givings-lkn active"><span class="badge current-badge"></span>THIS YEAR</a>
                  </li>
                  <li class="nav-givings-li">
                      <a href="?period=alltime" id="previous" class="ajaxlink nav-givings-lkn "><span class="badge previous-badge"></span>ALL TIME</a>
                  </li>
              </ul>
          </div><!-- /col -->
      </div><!-- /row -->
  </div>
      <div class="row">
      <div class="col-md-12">
          <ul class="second-nav-givings">
              <li class="second-nav-givings-li">
                  <a href="?type=financial" class="second-nav-givings-lkn active ajaxlink">FINANCIAL YEAR</a>
              </li>
              <li class="second-nav-givings-li">
                  <a href="?type=calendar" class="second-nav-givings-lkn  ajaxlink">CALENDAR YEAR</a>
              </li>
              <li class="second-nav-givings-li">
                  <a href="?type=12months" class="second-nav-givings-lkn  ajaxlink">PREVIOUS 12  <br class="visible-xs">MONTHS</a>
              </li>
          </ul>
      </div>
  </div>
      <div class="row">
      <div class="col-lg-8 ">
          <div class="givings-info">
                                                      <div class="givings-info-title">SO FAR THIS <strong>FINANCIAL YEAR</strong> YOU HAVE GIVEN <b></b></div>

              <div class="givings-info-amount">£0.00</div>
                              <div class="givings-info-percentage">
                                                  </div>
          </div>
          <div class="givings-graphic">
              <!-- Google Charts -->
              <div id="chart_div1640603711"><div style="position: relative; width: 609px; height: 200px;"><div style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%;"><svg width="609" height="200"><defs><clipPath id="rablfilter0"><rect x="25.5" y="7.5" width="577" height="172"></rect></clipPath></defs><g><rect x="0" y="0" width="609" height="200" fill="#ffffff" fill-opacity="1" stroke="#ffffff" stroke-opacity="0" stroke-width="0"></rect><rect x="25.5" y="7.5" width="577" height="172" fill="#ffffff" fill-opacity="1" stroke="#ffffff" stroke-opacity="0" stroke-width="1"></rect></g><g><line x1="25.5" x2="602.5" y1="179.5" y2="179.5" stroke="#E0E0E0" stroke-width="1"></line><line x1="25.5" x2="602.5" y1="136.5" y2="136.5" stroke="#E0E0E0" stroke-width="1"></line><line x1="25.5" x2="602.5" y1="93.5" y2="93.5" stroke="#9E9E9E" stroke-width="1"></line><line x1="25.5" x2="602.5" y1="50.5" y2="50.5" stroke="#E0E0E0" stroke-width="1"></line><line x1="25.5" x2="602.5" y1="7.5" y2="7.5" stroke="#E0E0E0" stroke-width="1"></line></g><g><path d="M 37.5 93.5 L 106.625 93.5 L 175.75 93.5 L 244.875 93.5 L 314 93.5 L 383.125 93.5 L 452.25 93.5 L 521.375 93.5 L 590.5 93.5" fill="none" stroke="#00adee" stroke-width="2" clip-path="url(#rablfilter0)"></path><circle cx="37.5" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle><circle cx="106.625" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle><circle cx="175.75" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle><circle cx="244.875" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle><circle cx="314" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle><circle cx="383.125" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle><circle cx="452.25" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle><circle cx="521.375" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle><circle cx="590.5" cy="93.5" r="4" fill="#00adee" fill-opacity="0" clip-path="url(#rablfilter0)"></circle></g><g></g><g><text x="37.5" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-17.5390625px">Apr 21</text><text x="106.625" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-19.5234375px">May 21</text><text x="175.75" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-18.15625px">Jun 21</text><text x="244.875" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-16.3046875px">Jul 21</text><text x="314" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-18.78125px">Aug 21</text><text x="383.125" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-18.328125px">Sep 21</text><text x="452.25" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-17.453125px">Oct 21</text><text x="521.375" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-18.7890625px">Nov 21</text><text x="590.5" y="196.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-18.484375px">Dec 21</text><text x="19.5" y="183.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-17.03125px">-£1</text><text x="19.5" y="140.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-17.03125px">-£1</text><text x="19.5" y="97.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-13.71875px">£0</text><text x="19.5" y="54.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-13.71875px">£1</text><text x="19.5" y="11.5" style="cursor: default; user-select: none; -webkit-font-smoothing: antialiased; font-family: Roboto; font-size: 12px;" fill="#757575" dx="-13.71875px">£1</text></g><g></g><g></g><g></g></svg></div></div></div>
              <!--<img src="images/img-prueba.png">-->
          </div>
      </div>
      <div class="col-lg-4 givings-charities">
                          <div class="givings-charities-title">YOUR TOP CHARITIES - <span>This financial year</span></div>
                      <div class="givings-charities-help"><span class="fa-stack fa-lg"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-info fa-stack-1x"></i></span> Select a charity to see a detailed report</div>
          <table class="givings-charities-table table">
              <tbody>
                                  </tbody>
          </table>
                  </div>
  </div>
  <div class="visible-xs col-xs-12">
      <a href="#" class="btn btn-primary transition external-lkn givings-charities-btn hidden">YOUR TOP CHARITIES</a>
  </div>
  <div class="row">
      <div class="table-container-xs">
          <div class="col-md-12">
                              <h2 class="givings-months-table-title ">MONTH-BY-MONTH BREAKDOWN</h2>
                              <table class="givings-months-table table">
                  <thead>
                      <tr>
                                                      <th>MONTH</th>
                                                      <th class="">DONATED AMOUNT</th>
                          <th class="visible-xs">DONATIONS</th>
                          <th class="hidden-xs">DONATIONS</th>
                          <th class="hidden-xs">VOUCHERS</th>
                          <th class="hidden-xs">ONLINE</th>
                          <th class="">AVERAGE AMOUNT</th>
                          <th class="hidden-xs">MOST SUPPORTED<br>CHARITY</th>
                          <th class="hidden-xs">AMOUNT DONATED TO<br> MOST SUPPORTED CHARITY</th>
                      </tr>
                  </thead>
                  <tbody>
                                              <tr class="balance-down" data-id="Apr2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=05-04-2021&amp;enddate=30-04-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">Apr 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=05-04-2021&amp;enddate=30-04-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=05-04-2021&amp;enddate=30-04-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=05-04-2021&amp;enddate=30-04-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=05-04-2021&amp;enddate=30-04-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=05-04-2021&amp;enddate=30-04-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=05-04-2021&amp;enddate=30-04-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=05-04-2021&amp;enddate=30-04-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>
                                              <tr class="balance-down" data-id="May2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-05-2021&amp;enddate=31-05-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">May 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-05-2021&amp;enddate=31-05-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-05-2021&amp;enddate=31-05-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-05-2021&amp;enddate=31-05-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-05-2021&amp;enddate=31-05-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-05-2021&amp;enddate=31-05-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-05-2021&amp;enddate=31-05-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-05-2021&amp;enddate=31-05-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>
                                              <tr class="balance-down" data-id="Jun2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-06-2021&amp;enddate=30-06-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">Jun 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-06-2021&amp;enddate=30-06-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-06-2021&amp;enddate=30-06-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-06-2021&amp;enddate=30-06-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-06-2021&amp;enddate=30-06-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-06-2021&amp;enddate=30-06-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-06-2021&amp;enddate=30-06-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-06-2021&amp;enddate=30-06-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>
                                              <tr class="balance-down" data-id="Jul2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-07-2021&amp;enddate=31-07-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">Jul 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-07-2021&amp;enddate=31-07-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-07-2021&amp;enddate=31-07-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-07-2021&amp;enddate=31-07-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-07-2021&amp;enddate=31-07-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-07-2021&amp;enddate=31-07-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-07-2021&amp;enddate=31-07-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-07-2021&amp;enddate=31-07-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>
                                              <tr class="balance-down" data-id="Aug2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-08-2021&amp;enddate=31-08-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">Aug 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-08-2021&amp;enddate=31-08-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-08-2021&amp;enddate=31-08-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-08-2021&amp;enddate=31-08-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-08-2021&amp;enddate=31-08-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-08-2021&amp;enddate=31-08-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-08-2021&amp;enddate=31-08-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-08-2021&amp;enddate=31-08-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>
                                              <tr class="balance-down" data-id="Sep2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-09-2021&amp;enddate=30-09-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">Sep 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-09-2021&amp;enddate=30-09-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-09-2021&amp;enddate=30-09-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-09-2021&amp;enddate=30-09-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-09-2021&amp;enddate=30-09-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-09-2021&amp;enddate=30-09-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-09-2021&amp;enddate=30-09-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-09-2021&amp;enddate=30-09-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>
                                              <tr class="balance-down" data-id="Oct2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-10-2021&amp;enddate=31-10-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">Oct 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-10-2021&amp;enddate=31-10-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-10-2021&amp;enddate=31-10-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-10-2021&amp;enddate=31-10-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-10-2021&amp;enddate=31-10-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-10-2021&amp;enddate=31-10-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-10-2021&amp;enddate=31-10-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-10-2021&amp;enddate=31-10-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>
                                              <tr class="balance-down" data-id="Nov2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-11-2021&amp;enddate=30-11-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">Nov 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-11-2021&amp;enddate=30-11-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-11-2021&amp;enddate=30-11-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-11-2021&amp;enddate=30-11-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-11-2021&amp;enddate=30-11-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-11-2021&amp;enddate=30-11-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-11-2021&amp;enddate=30-11-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-11-2021&amp;enddate=30-11-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>
                                              <tr class="balance-down" data-id="Dec2021" data-type="TR">
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-12-2021&amp;enddate=31-12-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <div class="date">Dec 2021</div>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-12-2021&amp;enddate=31-12-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right balance-transition voucher-balance">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-12-2021&amp;enddate=31-12-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-12-2021&amp;enddate=31-12-2021&amp;transaction_type=VO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-12-2021&amp;enddate=31-12-2021&amp;transaction_type=NV,SO&amp;charity_id=">
                                  <span class="givings-quantity"> 0 </span>
                              </a>
                          </td>
                          <td class="date-td">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-12-2021&amp;enddate=31-12-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">£0.00</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-12-2021&amp;enddate=31-12-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="supported-charity">N/A</span>
                              </a>
                          </td>
                          <td class="date-td hidden-xs">
                              <a class="ajaxlink" href="transactions.php?ajax=1&amp;return=1&amp;dateType=custom&amp;startdate=1-12-2021&amp;enddate=31-12-2021&amp;transaction_type=NV,SO,VO&amp;charity_id=">
                                  <span class="text-right amount-block">N/A</span>
                              </a>
                          </td>
                      </tr>

                  </tbody>
              </table>
          </div>
      </div>
  </div>
</main>
<div class="modal-givings modal-gral modal fade" id="modal-givings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

<div class="modal-dialog" role="document">

  <div class="modal-content">

      <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>
          <div class="modal-givings-content">
                          <div class="givings-charities-title">YOUR TOP CHARITIES - <span>This financial year</span></div>

<div class="givings-charities-help" style="padding-left: 27px;padding-top:0"><span class="fa-stack fa-lg"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-info fa-stack-1x"></i></span> Click on a charity to view a detailed report</div>

              <div class="modal-givings-sub-content">
                  <table class="givings-charities-table table">
                      <tbody>
                                                  </tbody>
                  </table>
              </div>
          </div>

                   <button type="button" class="lkn-bottom-modal lnk-cancel" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">Cancel</span>
                  </button>

                  <button type="button" class="lkn-bottom-modal lnk-accept givings-popup-accept" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">View report</span>
                  </button>


      </div><!-- /modal-body -->

  </div><!-- /modal-content -->

</div><!-- /modal-dialog -->

</div><!-- /modal -->
</div>

@endsection
