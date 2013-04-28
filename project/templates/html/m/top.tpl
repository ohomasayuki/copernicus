<!--
<div class="navbar-wrapper">
        <div class="container">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <button type="button" class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" href="#">copernicus</a>
                    <div class="nav-collapse collapse" style="height: 0px;">
                        <ul class="nav">
                            <li class="active">
                                <a href="#">Home</a>
                            </li>
                            <li>
                                <a href="#about">About</a>
                            </li>
                            <li>
                                <a href="#contact">Contact</a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="#">Action</a>
                                    </li>
                                    <li>
                                        <a href="#">Another action</a>
                                    </li>
                                    <li>
                                        <a href="#">Something else here</a>
                                    </li>
                                    <li class="divider">
                                    </li>
                                    <li class="nav-header">
                                        Nav header
                                    </li>
                                    <li>
                                        <a href="#">Separated link</a>
                                    </li>
                                    <li>
                                        <a href="#">One more separated link</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
-->
<div id="myCarousel" class="carousel slide" data-pause="remove">
        <div class="carousel-inner">
            <div class="item">
                <img src="https://s3.amazonaws.com/jetstrap-site/images/2.0/assets/cta_image1.jpg">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>
                            Example headline.
                        </h1>
                        <p class="lead">
                            Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id
                            elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies
                            vehicula ut id elit.
                        </p>
                        <a class="btn btn-large btn-primary" href="#">Sign up today</a>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="https://s3.amazonaws.com/jetstrap-site/images/2.0/assets/cta_image2.jpg">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>
                            Another example headline.
                        </h1>
                        <p class="lead">
                            Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id
                            elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies
                            vehicula ut id elit.
                        </p>
                        <a class="btn btn-large btn-primary" href="#">Learn more</a>
                    </div>
                </div>
            </div>
            <div class="item active">
                <img src="https://s3.amazonaws.com/jetstrap-site/images/2.0/assets/cta_image3.jpg">
                <div class="container">
                    <div class="carousel-caption">
                        <h1>
                            App for you!
                        </h1>
                        <p class="lead">
                            あなたが気に入るアプリを探します。
                        </p>
                        <a class="btn btn-large btn-primary" href="../facebook/fb_start.php">スタート</a>
                    </div>
                </div>
            </div>
        </div>
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
    </div>

    <style>
      
      /* GLOBAL STYLES
      -------------------------------------------------- */
      /* Padding below the footer and lighter body text */
      
      body {
        padding-bottom: 40px;
        color: #5a5a5a;
      }
      
      
      
      /* CUSTOMIZE THE NAVBAR
      -------------------------------------------------- */
      
      /* Special class on .container surrounding .navbar, used for positioning it into place. */
      .navbar-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 10;
        margin-top: 20px;
        margin-bottom: -90px; /* Negative margin to pull up carousel. 90px is roughly margins and height of navbar. */
      }
      .navbar-wrapper .navbar {
      
      }
      
      /* Remove border and change up box shadow for more contrast */
      .navbar .navbar-inner {
        border: 0;
        -webkit-box-shadow: 0 2px 10px rgba(0,0,0,.25);
        -moz-box-shadow: 0 2px 10px rgba(0,0,0,.25);
        box-shadow: 0 2px 10px rgba(0,0,0,.25);
      }
      
      /* Downsize the brand/project name a bit */
      .navbar .brand {
        padding: 14px 20px 16px; /* Increase vertical padding to match navbar links */
        font-size: 16px;
        font-weight: bold;
        text-shadow: 0 -1px 0 rgba(0,0,0,.5);
      }
      
      /* Navbar links: increase padding for taller navbar */
      .navbar .nav > li > a {
        padding: 15px 20px;
      }
      
      /* Offset the responsive button for proper vertical alignment */
      .navbar .btn-navbar {
        margin-top: 10px;
      }
      
      
      
      /* CUSTOMIZE THE CAROUSEL
      -------------------------------------------------- */
      
      /* Carousel base class */
      .carousel {
        margin-bottom: 60px;
      }
      
      .carousel .container {
        position: relative;
        z-index: 9;
      }
      
      .carousel-control {
        height: 80px;
        margin-top: 0;
        font-size: 120px;
        text-shadow: 0 1px 1px rgba(0,0,0,.4);
        background-color: transparent;
        border: 0;
        z-index: 10;
      }
      
      .carousel .item {
        height: 500px;
      }
      .carousel img {
        position: absolute;
        top: 0;
        left: 0;
        min-width: 100%;
        height: 500px;
      }
      
      .carousel-caption {
        background-color: transparent;
        position: static;
        max-width: 550px;
        padding: 0 20px;
        margin-top: 200px;
      }
      .carousel-caption h1,
      .carousel-caption .lead {
        margin: 0;
        line-height: 1.25;
        color: #fff;
        text-shadow: 0 1px 1px rgba(0,0,0,.4);
      }
      .carousel-caption .btn {
        margin-top: 10px;
      }
      
      
      
      /* MARKETING CONTENT
      -------------------------------------------------- */
      
      /* Center align the text within the three columns below the carousel */
      .marketing .span4 {
        text-align: center;
      }
      .marketing h2 {
        font-weight: normal;
      }
      .marketing .span4 p {
        margin-left: 10px;
        margin-right: 10px;
      }
      
      
      /* Featurettes
      ------------------------- */
      
      .featurette-divider {
        margin: 80px 0; /* Space out the Bootstrap <hr> more */
      }
      .featurette {
        padding-top: 120px; /* Vertically center images part 1: add padding above and below text. */
        overflow: hidden; /* Vertically center images part 2: clear their floats. */
      }
      .featurette-image {
        margin-top: -120px; /* Vertically center images part 3: negative margin up the image the same amount of the padding to center it. */
      }
      
      /* Give some space on the sides of the floated elements so text doesn't run right into it. */
      .featurette-image.pull-left {
        margin-right: 40px;
      }
      .featurette-image.pull-right {
        margin-left: 40px;
      }
      
      /* Thin out the marketing headings */
      .featurette-heading {
        font-size: 50px;
        font-weight: 300;
        line-height: 1;
        letter-spacing: -1px;
      }
      
      
      
      /* RESPONSIVE CSS
      -------------------------------------------------- */
      
      @media (max-width: 979px) {
      
        .container.navbar-wrapper {
          margin-bottom: 0;
          width: auto;
        }
        .navbar-inner {
          border-radius: 0;
          margin: -20px 0;
        }
      
        .carousel .item {
          height: 500px;
        }
        .carousel img {
          width: auto;
          height: 500px;
        }
      
        .featurette {
          height: auto;
          padding: 0;
        }
        .featurette-image.pull-left,
        .featurette-image.pull-right {
          display: block;
          float: none;
          max-width: 40%;
          margin: 0 auto 20px;
        }
      }
      
      
      @media (max-width: 767px) {
      
        .navbar-inner {
          margin: -20px;
        }
      
        .carousel {
          margin-left: -20px;
          margin-right: -20px;
        }
        .carousel .container {
      
        }
        .carousel .item {
          height: 300px;
        }
        .carousel img {
          height: 300px;
        }
        .carousel-caption {
          width: 65%;
          padding: 0 70px;
          margin-top: 100px;
        }
        .carousel-caption h1 {
          font-size: 30px;
        }
        .carousel-caption .lead,
        .carousel-caption .btn {
          font-size: 18px;
        }
      
        .marketing .span4 + .span4 {
          margin-top: 40px;
        }
      
        .featurette-heading {
          font-size: 30px;
        }
        .featurette .lead {
          font-size: 18px;
          line-height: 1.5;
        }
      
      }
      
    </style>
