<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name').' - '.config('app.description')}}</title>

    @livewireStyles
    <link rel="stylesheet" media="all" href="{!! mix('css/main.css') !!}" />
    <link rel="stylesheet" media="all" href="{!! mix('css/frontend.css') !!}" />
    @auth
        @if (auth()->user()->hasRole('admin') || (auth()->user()->id === $url->user_id))
            @if (isset($url))
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript">
                google.charts.load('current', {
                    'packages':['geochart'],
                });
                google.charts.setOnLoadCallback(drawRegionsMap);
                
                function drawRegionsMap() {
                    var regions = [
                        ['Country', 'Visitor'],
                    ];

                    @foreach ($url->regions as $region => $count)
                        regions.push(["{{$region}}", {{$count}}])
                    @endforeach
                    var data = google.visualization.arrayToDataTable(regions);

                    var options = {
                        colorAxis: {
                            colors: ['rgb(0, 90, 158)']
                        }
                    };

                    var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

                    chart.draw(data, options);
                }
            </script>
            @endif
        @endif
    @endauth
</head>

<body class="@yield('css_class')">
    @include('partials.nav-header')

    @yield('content')

    <script src="{!! mix('js/manifest.js') !!}"></script>
    <script src="{!! mix('js/vendor.js') !!}"></script>
    <script src="{!! mix('js/frontend.js') !!}"></script>
    @livewireScripts
</body>

</html>
