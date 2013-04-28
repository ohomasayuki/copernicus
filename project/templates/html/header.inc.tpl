<!DOCTYPE html>
<html lang="en">
  
  <head>
    <meta charset="utf-8">
    <title>copernicus
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Le styles -->
    <link href="https://s3.amazonaws.com/jetstrap-site/lib/bootstrap/2.3.0/css/bootstrap.css" rel="stylesheet">
    <link href="https://s3.amazonaws.com/jetstrap-site/lib/bootstrap/2.3.0/css/bootstrap-responsive.css" rel="stylesheet">
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js">
      </script>
    <![endif]-->
    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="https://s3.amazonaws.com/jetstrap-site/lib/bootstrap/2.2.1/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
    <script src="http://airve.github.io/js/response/response.min.js"></script>
    <link href="/css/jquery.pageslide.css" rel="stylesheet">
     <meta name="apple-mobile-web-app-capable" content="yes" />
  </head>
  
  <body>
    <div id="content">
        <h1><a class="open" href="#nav">Menu</a>copernicus</h1>
        <ul id="nav">
            <li><a href="/m/top.html">Top</a></li>
            <li><a href="/m/main.html">Main</a></li>
            <li><a href="/m/list.html">List</a></li>
            <li><a href="/m/start.html">Start</a></li>
            <li><a href="/facebook/oauth_start.php">Login</a></li>
            <li><a href="/facebook/logout.php">Logout</a></li>
            <li><a href="javascript:return window.reload()">再読込</a></li>
            <li><a href="javascript:return history.back()">戻る</a></li>
        </ul>

    <div class="container">
    [:$debug:]

    <style>

        body { 
            /*background: url(http://kachibito.net/sample/jquery-masonry/bg.jpg);*/
            font: 14px/18px "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #666; 
            -webkit-font-smoothing: antialiased; /* Fix for webkit rendering */
            -webkit-text-size-adjust: none;
        }

        a, a:visited { color: #C30; text-decoration: none;  }
        a:hover { color: #900; border-bottom-style: solid; }

        p { margin-bottom: 20px; }

        #content {
            width: 940px;
            padding: 0px;
            margin: 0px auto;
        }
            #content h1 { color: #eee; line-height: 1em; margin:0 0 10 0; }
            
            ul#nav { padding-left: 0; list-style: none; width: 100%; margin-bottom: 40px; }
            ul#nav:after { content: "\0020"; display: block; height: 0; clear: both; visibility: hidden; }
            
                #content ul#nav li { float: left; width: 20%; text-align: center; }
                #content ul#nav a { 
                        display: block; 
                        background: #eee;
                        color: #555; 
                        font-weight: bold; 
                        padding: 10px; 
                        /*border-right: 1px solid #ddd;*/
                }
                #content ul#nav a:hover { background: #bcb5b1; }

        .open {
            display: none;
            float: left;
            width: 40px;
            height: 30px;
            margin-right: 10px;
            background: url(http://kachibito.net/sample/pageslide/menu.png) center center no-repeat #333;
            -moz-border-radius: 8px;
            -webkit-border-radius: 8px;
            -border-radius: 8px;
            box-shadow: inset 0 0 3px #000;
            text-indent: -999999px;
            border: 0;
        }

        /* Mobile and iPad Portrait */
        @media only screen and (max-width: 959px) {
            #content { width: 748px; }
        }
        
        /* Mobile Landscape and Portrait */
        @media only screen and (max-width: 767px) {
            #content { width: 400px; }
            #content h1 { font-size: 22px; line-height: 30px; background-color: #eee; color: #333; padding: 5px; }
            #content ul#nav { display: none; }
            
            .open { display: block; }
            #pageslide { width: 200px; }
                #pageslide #nav li { padding: 5px 0; /*border-bottom: 1px solid #666;*/ }
                #pageslide #nav li a { color: #FFF; border: none; }
                #pageslide #nav li a:hover { text-decoration: underline; }
        }
        
        /* Mobile Portrait */
        @media only screen and (max-width: 479px) {
            #content { width: 280px; }
            #content h1 { font-size: 14px; }
        }

      /* star */
      wstars.a{
          color:#FFCC33;
        font-size: xx-large;
        text-decoration: none;
      }
      
      .artwork {
          border-radius: 10px;
      }
      
      /* textOverflow 文字がはみだしたとき…にする */
      .textOverflow {
          overflow: hidden;
          width: 240px;
          white-space: nowrap;
      }
       
      .textOverflow {
          text-overflow: ellipsis;
          -webkit-text-overflow: ellipsis; /* Safari */
          -o-text-overflow: ellipsis; /* Opera */
      }
      /* /textOverflow */
      /* fukidashi */
      .balloon {
        position: relative;
        margin-left: 80px;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #eee;
        border-radius: 10px;
        box-shadow: 3px 3px 5px rgba(0, 0, 0, .25);
      }
      .balloon:before {
      position: absolute; left: -80px; top:-10px;
      content: url(http://xia.jp/tmp/else/chara-mini.png);
      }
      .balloon:after {
        position: absolute; top: 10px; left: -20px;
        content: ""; width: 0; height: 0;
        border-top: 15px solid #eee;
        border-left: 20px solid transparent;
      }
      /* /fukidashi */
      
      .app-detail-container {
          font-size: smaller;
          border-radius: 10px;
          margin-top:10px;
          background-color: #eee;
          padding: 5px;
          box-shadow: 3px 3px 5px rgba(0, 0, 0, .25);
      }
      .app-detail-icon {
          float: left;
          margin-right: 10px;
      }
      .app-price {
          width:100px;
      }
      .have {
      }
      .outofeye {
          position: relative;
      }
      
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js">
    </script>
    <script src="https://s3.amazonaws.com/jetstrap-site/lib/bootstrap/2.3.0/js/bootstrap.js">
    </script>
    <script>
      // forked from __whats's "五つ星をクリックして段階評価するサンプル" http://jsdo.it/__whats/hLCW
      $(function() {
          //画像を置く場所がないのでテキストで代用しています。
          // var star00 = '<img src="/staticfiles/image/star0.gif" alt="" width="24" height="24"/>';
          var star00 = '☆';
          // var star01 = '<img src="/staticfiles/image/star1.gif" alt="" width="24" height="24"/>';
          var star01 = '★';
          var rank = 1;
          writeStar();
          $('a').live('click', function() {
              rank = $('a').index(this) + 1;
            writeStar();
          });
          function writeStar() {
            $('#stars').empty();
            for (var i = 0; i < 5; i++) {
                var star = (i < rank) ? star01 : star00;
                $('<a href="#"></a>').html(star).appendTo($('#stars'));
            }
            $("#wstars").val(rank);
          }
      });
    </script>
    <script src="/js/jquery.pageslide.min.js"></script>
    <script>
        $(".open").pageslide();
    </script>

