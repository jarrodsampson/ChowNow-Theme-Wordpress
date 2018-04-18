<!DOCTYPE html>
<html lang="en-US">
   <head>
      <meta charset="utf-8">
      <link rel="icon" type="image/png" href="https://www.chownow.com/wp-content/uploads/2015/08/cn-logo.png" />
      <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width"/>
      <title>GoDaddy | ChowNow</title>
      <?php wp_head(); ?>
   </head>
   <body id="page-top">
      <!-- Navigation -->
      <nav class="navbar navbar-expand-lg navbar-shrink navbar-light fixed-top transition" id="mainNav">
         <div class="container nav-size">
            <div class="sq_chownowLogo"></div>
            <div class="sq_partnerLogo"></div>
            <div class="collapse navbar-collapse" id="navbarResponsive">
               <ul class="navbar-nav ml-auto">
                  <li class="nav-item">
                     <a class="nav-link js-scroll-trigger" href="#learn">Learn</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link js-scroll-trigger" href="#testimonials">Testimonials</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link js-scroll-trigger" href="#demo">Demo</a>
                  </li>
               </ul>
            </div>
         </div>
      </nav>
      <header class="masthead">
         <div class="sq_headLogos">
            <div class="sq_chownowLogo"></div>
            <div class="sq_partnerLogo"></div>
         </div>
         <div class="container h-100">
            <div class="row h-100">
               <div class="col-lg-12 my-auto">
                  <div class="header-content mx-auto">
                     <h1><?php $bannerHeading = get_option( 'banner_heading' ); echo $bannerHeading ?></h1>
                     <p><?php $bannerDesc = get_option( 'banner_description' ); echo $bannerDesc ?></p>
                     <a href="#demo" class="btn btn-outline btn-xl js-scroll-trigger">Get a Free Product Demo</a>
                  </div>
               </div>
            </div>
         </div>
      </header>