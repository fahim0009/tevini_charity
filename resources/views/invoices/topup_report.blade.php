<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>voucher</title>
   <style>
 * {
	 margin: 0;
	 padding: 0;
}
 .wrapper {
	 max-width: 960px;
	 margin: 0 auto;
	 box-shadow: rgba(0, 0, 0, 0.04) -1px 2px 20px 15px;
	 padding: 25px;
}
 .wrapper .heading .title {
	 text-align: right;
	 font-weight: 600;
	 font-size: 2.1rem;
	 text-transform: uppercase;
	 font-family: monospace;
	 letter-spacing: 1px;
	 color: #436784;
}
 .wrapper .heading .subHead {
	 margin: 20px 0;
	 display: flex;
	 justify-content: space-between;
}
 .wrapper .heading .subHead .left {
	 color: #436784;
	 text-transform: capitalize;
	 line-height: 1.3;
	 font-size: 16px;
	 font-family: sans-serif;
}
 .wrapper .heading .subHead .right {
	 text-align: right;
	 color: #436784;
	 text-transform: capitalize;
	 line-height: 1.3;
	 font-size: 16px;
	 font-family: sans-serif;
}
 .wrapper .heading .donated {
	 color: #436784;
	 font-family: sans-serif;
}
 .wrapper .tableData {
	 margin-top: 100px;
	 min-width: 400px;
	 overflow-x: auto;
}
 .wrapper .tableData table {
	 width: 100%;
	 text-align: center;
	 border-collapse: collapse;
}
 .wrapper .tableData table tr th {
	 background-color: #436784;
	 color: azure;
	 padding: 6px;
	 font-family: sans-serif;
	 border-right: 1px solid #fff;
}
 .wrapper .tableData table tr {
	 border-bottom: 1px solid #ebebeb;
}
 .wrapper .tableData table tr td {
	 padding: 6px;
	 color: #080808;
	 text-transform: capitalize;
}
 .wrapper .tableData table tr:nth-child(even) {
	 background: #436784 14;
}
 
 
   </style>
</head>

<body>

    <div class="wrapper">

        <div class="heading">
            <div class="title">
                {{$title}}
            </div>
            <div class="subHead">
                <div class="left">
                    Tevini Limited <br>
                    Registered charity no. 282079 <br>
                    5A Holmdale Terrace<br>
                    N156PP

                </div>
                <div class="right">
                    Date: <b>@php echo date('d-m-Y'); @endphp</b> <br>
                    Receipt <b>#@php echo(rand(100,999));  @endphp</b>

                </div>
            </div>
            <p class="donated">
               <b> Donation By:</b>	{{$donationBy}}
            </p>
        </div>

        <div class="tableData">
            <table>
               <thead>
                <tr>
                    <th>Date</th>
                    <th></th>
                    <th></th>
                    <th>Donation type</th>
                    <th></th>
                    <th></th>
                    <th>Total</th>
                </tr>
               </thead>
               <tbody>
                <tr>
                    <td>@php echo date('d-m-Y'); @endphp</td>
                    <td></td>
                    <td></td>
                    <td>{{$source}}</td>
                    <td></td>
                    <td></td>
                    <td>£{{$balance}}</td>
                </tr>
                <tr> 
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b>£{{$balance}}</b></td>
                </tr>
               </tbody>
            </table>
        </div>

    </div>

</body>

</html>