<html>
<head>
    <title>Financial Year</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        body {
            background-color: #0090e3;
            font-family: 'Circular',Helvetica,Arial,Lucida,sans-serif;
            color: #ffffff;
        }
        .main {
            width: 60%;
            height: 60%;
            border: 3px solid #ffffff;
            margin-top: 6%;
        }
        .country {
            width: 35% !important;
            height: auto !important;
            font-size: 17px !important;
            border: 3px solid #ffffff !important;
            color: #0090e3 !important;
            margin: 0px 0px 10px 30% !important;
        }
        div#resultdiv2 {
            padding: 30px;
        }

        .country_h4 {
            margin-top: 20px !important;
            margin-left: 30% !important;
        }

        .year_h4 {
            margin-top: 20px !important;
            margin-left: 30% !important;
        }

        .year {
            width: 35% !important;
            height: auto !important;
            font-size: 17px !important;
            border: 3px solid #ffffff !important;
            color: #0090e3 !important;
            margin: 0px 0px 10px 30% !important;
        }

        .submit {
            background-color: #009900;
            color: #ffffff;
            border: 3px solid #009900;
            width: 35% !important;
            height: auto !important;
            font-size: 17px !important;
            margin: 20px 0px 10px 30% !important;
        }
    </style>    
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8 main">
            <h4 class="country_h4"><b>Country</b></h4>
            <form action="{{ route('financial_update') }}" method="POST">
            @csrf
            <select name="country" id="countryid" class="country">
                <option value="">Select Country</option>
                <option value="UK">UK</option>
                <option value="Ireland">Ireland</option>
            </select>
            <br />
            <h4 class="year_h4" style="display:none;"><b>Year</b></h4>
            <select style="display:none;" name="year" id="yearsel" class="year">
            </select>
            <br />
            <input type="submit" class="submit" value="Submit" />
        </div>
        <div class="col-md-2"></div>
    </div>

</div>


<div class="container res" id="res" style="margin-bottom: 160px;">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8 main">
            <div id="resultdiv1" style="padding-top: 50px;padding-left: 10%;">Financial Year Start:</div>
            <div id="resultdiv2" style="padding-bottom: 50px;padding-left: 10%;">Financial Year End:</div>
            <div id="getpublicholidays"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $("#countryid").change(function(){
            getYear();
        });
    });
    function getYear(){
    var currentYear = new Date().getFullYear();
    var opt = $("#countryid option:selected").attr("value");
    if(opt !== ''){
        $("h4.year_h4").show();
        $("select.year").show();
    }else{
        $("h4.year_h4").hide();
        $("select.year").hide();
    }
    if(opt == "Ireland"){
        var i;
        var year_dropdown = '';
        year_dropdown += '<option value="">Select Year</option>';
        for(i=currentYear; i>currentYear-10; i--){
            // alert(i-1);
            year_dropdown += '<option value="' + i + '">' + i + '</option>';
        }
        $("#yearsel").html(year_dropdown);
    }
    else if(opt == "UK"){
        var i;
        var year_dropdown = '';
        year_dropdown += '<option value="">Select Year</option>';
        for(i=currentYear; i>currentYear-10; i--){
            year_dropdown += '<option value="' + (i-1) + '-' + i + '">' + (i-1) + '-' + i + '</option>';
        }
        $("#yearsel").html(year_dropdown);
    }
    }

    $(document).ready(function () {
        $(".submit").click(function(){
            event.preventDefault();
            var country = $("select#countryid").val();
            var year = $("select#yearsel").val();
            $.ajax({
                type: "POST",
                url: "{{ route('financial_update') }}",
                data: {
                    country: country,
                    year: year,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                var response_array = response.data.split("#");
                // console.log(response_array);
                var response_res0 = response_array[0].replaceAll('"', '');
                var response_res1 = response_array[1].replaceAll('"', '');
                var response_res2 = response_array[2].replaceAll('"', '');
                var response_res3 = response_array[3].replaceAll('"', '');
                var response_res4 = response_array[4].replaceAll('"', '');
                var response_res5 = response_array[5].replaceAll('"', '');

                var saturday;
                var sunday;
                var end_saturday;
                var end_sunday;
                if(response_res2 !== ""){
                    saturday = response_res2 + " is Saturday And ";
                }
                if(response_res3 !== ""){
                    sunday = response_res3 + " is Sunday";
                }
                if(response_res4 !== ""){
                    end_saturday = response_res4 + " is Saturday";
                }
                if(response_res5 !== ""){
                    end_sunday = response_res5 + " is Sunday";
                }
                if((response_res2 == "null") && (response_res3 == "null")){
                    saturday = "";
                    sunday = "";
                    $("div#resultdiv1").text("Financial Year Start:" + " " + response_res0);
                }else if((response_res2 == "null") && (response_res3 !== "null")){
                    $("div#resultdiv1").text("Financial Year Start:" + " " + response_res0 + "\n\n\n" + " (" + sunday + ") ");
                }else if((response_res3 == "null") && (response_res2 !== "null")){
                    $("div#resultdiv1").text("Financial Year Start:" + " " + response_res0 + "\n\n\n" + " (" + saturday + ") ");
                }else{
                    $("div#resultdiv1").text("Financial Year Start:" + " " + response_res0 + "\n\n\n" + " (" + saturday + " And " + sunday + ") ");
                }

                if((response_res4 == "null") && (response_res5 == "null")){
                    end_saturday = "";
                    end_sunday = "";
                    $("div#resultdiv2").text("Financial Year End:" + " " + response_res1);
                }else if((response_res4 == "null") && (response_res5 !== "null")){
                    $("div#resultdiv2").text("Financial Year End:" + " " + response_res1 + " (" + end_sunday + ") ");
                }else if((response_res5 == "null") && (response_res4 !== "null")){
                    $("div#resultdiv2").text("Financial Year End:" + " " + response_res1 + " (" + end_saturday + ") ");
                }else{
                    $("div#resultdiv2").text("Financial Year End:" + " " + response_res1 + " (" + end_saturday + " And " + end_sunday + ") ");
                }
                let year = response_array[0];
                let country;
                if(response.country == "Ireland"){
                    country = "IE";
                }else{
                    country = "GB"
                }
                $("div#getpublicholidays").html(`
                    <a href="/financial-holidays/${year}/${country}" class="holidays" style="margin-left: 7%; text-decoration: none;">
                        <input type="button" class="submit" value="Get Public Holidays" />
                    </a>
                `);
                // $("div#getpublicholidays").html('<input type="button" id="get_public_holidays" class="submit" value="Get Public Holidays" style="margin-left: 50%;" />');
                $(document).ready(function () {
                    $('html, body').animate({
                        scrollTop: $('#res').get(0).scrollHeight
                    }, 1500);
                });
                
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
                        
                });
            });
        });
    </script>
</body>
</html>