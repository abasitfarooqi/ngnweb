<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NGN Rental Mgt.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <script>
        var subjectObject = {
            "Honda": {
                "Vision - NSC": ["2013", "2014", "2015", "2016", "2017 - Current"],
                "PCX - WW": ["2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020", "2021 - Current"],
                "SH": ["2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020", "2021 - Current"],
                "SH MODE - FSH / ANC": ["2013", "2014", "2015", "2016", "2017 - Current"],
                "Forza - NSS": ["2015", "2016", "2017", "2018", "2019 - Current"]
            },
            "Yamaha": {
                "NMAX": ["2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020", "2021 - Current", ],
                "XMAX - YP": ["2015", "2016", "2017", "2018", "2019 - Current", ]
            }
        }
        window.onload = function() {
            var subjectSel = document.getElementById("make");
            var topicSel = document.getElementById("model");
            var chapterSel = document.getElementById("year");
            for (var x in subjectObject) {
                subjectSel.options[subjectSel.options.length] = new Option(x, x);
            }
            subjectSel.onchange = function() {
                //empty Chapters- and Topics- dropdowns
                chapterSel.length = 1;
                topicSel.length = 1;
                //display correct values
                for (var y in subjectObject[this.value]) {
                    topicSel.options[topicSel.options.length] = new Option(y, y);
                }
            }
            topicSel.onchange = function() {
                //empty Chapters dropdown
                chapterSel.length = 1;
                //display correct values
                var z = subjectObject[subjectSel.value][this.value];
                for (var i = 0; i < z.length; i++) {
                    chapterSel.options[chapterSel.options.length] = new Option(z[i], z[i]);
                }
            }
        }
    </script>
</head>

<body>

    @include('home_client.layouts.partials.navbar')

    <main class="container">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"
        integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous">
    </script>

    <script>
        const x = document.getElementById("amount");
        // let x = 5;
        let y = 3;
        let z = x * y;
        document.getElementById("rentalDeposit").innerHTML = z;
    </script>

</body>

</html>
