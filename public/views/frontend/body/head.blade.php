<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1RE49QH35E"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-1RE49QH35E');
    </script>

    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
    <title>NGN</title>

    <meta name="author" content="Emmanuel Nwokedi">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css">
    @vite(['resources/css/app.css', 'resources/css/style.css'])
{{-- all40 --}}
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">
  <!-- Tailwind CSS -->
  <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">

    <!--[if lt IE 9]>
        <script src="javascript/html5shiv.js"></script>
        <script src="javascript/respond.min.js"></script>
    <![endif]-->

    {{-- <script src="//widget.simplybook.it/v2/widget/widget.js"></script>
    <script>
        var widget = new SimplybookWidget({
            "widget_type": "button",
            "url": "https:\/\/neguinhomotorslimited.simplybook.it",
            "theme": "simple_beauty_theme",
            "theme_settings": {
                "sb_base_color": "#cd3232",
                "header_color": "#ffffff",
                "timeline_hide_unavailable": "1",
                "hide_past_days": "0",
                "timeline_show_end_time": "0",
                "timeline_modern_display": "as_slots",
                "display_item_mode": "list",
                "body_bg_color": "#ffffff",
                "sb_review_image": "",
                "dark_font_color": "#474747",
                "light_font_color": "#ffffff",
                "sb_company_label_color": "#333333",
                "hide_img_mode": "1",
                "show_sidebar": "1",
                "sb_busy": "#dad2ce",
                "sb_available": "#d3e0f1"
            },
            "timeline": "modern",
            "datepicker": "top_calendar",
            "is_rtl": false,
            "app_config": {
                "clear_session": 0,
                "allow_switch_to_ada": 0,
                "predefined": []
            },
            "button_title": "BOOK SERVICE OR MOT",
            "button_background_color": "#cd3232",
            "button_text_color": "#ffffff",
            "button_position": "right",
            "button_position_offset": "55%"
        });
    </script> --}}

    @vite('resources/js/app.js')

</head>
