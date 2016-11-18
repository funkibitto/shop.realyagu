<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{ get_title() }}
        <link rel="stylesheet" href="http://getbootstrap.com/dist/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="http://<?=getenv('STATIC_SHOP_DOMAIN')?>/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        {{ link_to(null, 'class': 'navbar-brand', 'REALYAGU')}}
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="#">Home</a></li>
                            <li><a href="#">About</a></li>
                            <li><a href="#">Contact</a></li>
                            <li class="dropdown">
                                <ul class="dropdown-menu">
                                    {%- set menus = [
                                    'Home': 'index',
                                    'About': 'about'
                                    ] -%}
                                    {%- for key, value in menus %}
                                    {% if value == dispatcher.getControllerName() %}
                                    <li class="active">{{ link_to(value, key) }}</li>
                                    {% else %}
                                    <li>{{ link_to(value, key) }}</li>
                                    {% endif %}
                                    {%- endfor -%}
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            {%- if not(logged_in is empty) %}
                            <li>{{ link_to('users', 'Users Panel') }}</li>
                            <li>{{ link_to('session/logout', 'Logout') }}</li>
                            {% else %}
                            <li>{{ link_to('session/login', 'Login') }}</li>
                            {% endif %}
                        </ul>
                    </div><!--/.nav-collapse -->
                </div><!--/.container-fluid -->
            </nav>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3">
                        {{ partial('left') }}
                    </div>
                    <div class="col-sm-9">
                        {{ content() }}
                    </div>
                </div>
            </div>
            <!--<footer class="footer">-->
                <!--{{ link_to("privacy", "Privacy Policy") }}-->
                <!--{{ link_to("terms", "Terms") }}-->

                <!--© {{ date("Y") }} by funkibitto.-->
            <!--</footer>-->
        </div>



        <!--{{ content() }}-->

    </body>
</html>
