<?php
session_start();
require_once __DIR__ . '/config/db.connection.php';
require_once __DIR__ . '/app/check_user.php';
ini_set('display_errors',  1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>IMS | Proponents</title>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/png" href="../goldenminds.favicon.png" sizes="16x16" /> 
    <?php require_once __DIR__ . '/components/css.file-links.inc.php';?>
    <link defer href="resources/css/proponents.css" rel="stylesheet"/>
  </head>
  <body style="background: url('resources/images/system-photo/gmc-bg.png');">
    <?php require_once __DIR__ . '/components/ui.side-nav.php';?>
    <?php require_once __DIR__ . '/components/ui.top-nav.php';?>
    <?php require_once __DIR__ . '/components/js.file-links.inc.php';?>
    <div class="content-wrap">
      <div class="main">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-8 p-r-0 title-margin-right">
              <div class="page-header">
                <div class="page-title">
                  <h6 class="clock m-t-30"><?php echo date("M-d-Y")?> / <?php echo date(" h: i A");?></h6>
                </div> 
              </div> 
            </div>

            <div class="col-lg-4 p-l-0 title-margin-left">
              <div class="page-header">
                <div class="page-title">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item" style="margin-left: -100px;">
                      <a href="proponents" class="active"> Proponents Profile </a>
                    </li>
                    <li class="breadcrumb-item">Dashboard</li>
                  </ol>
                </div>
              </div>
            </div>     
          </div> <!--end row-->

          <!-- start main content -->
          <section class="pb-5" id="main-content">
            <div class="container">
              <h2 class="text-center">Research Team</h2>
              <hr class="midline">
              <h5 class="text-center mb-5">Researcher from Golden Minds Colleges <br/>Santa Maria - Campus</h5>
              <div class="card col-md-12 mt-2" >
                <div id="researchTeam" class="carousel slide" data-ride="carousel" data-interval="100000">
                  <!-- data fetch via ajax - json -->
                  <div class="w-100 carousel-inner mb-5" role="listbox" id="proponents-container"></div>
                  <!-- prev button -->
                  <a class="carousel-control-prev" href="#researchTeam" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true">
                      <i class="fas fa-chevron-left"></i>
                    </span>
                    <span class="sr-only">Previous</span>
                  </a>
                  <!-- next button -->
                  <a class="carousel-control-next" href="#researchTeam" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true">
                      <i class="fas fa-chevron-right"></i>
                    </span>
                    <span class="sr-only">Next</span>
                  </a>
                </div>
              </div><!--card-->
            </div><!--second container-->
          </section>
                  
        </div><!--container-->
      </div><!--main-->
    </div><!--content-wrapper-->

    <script src="resources/js/proponents.js"> </script>
    <script type="text/javascript">
      function check_sesssion_id() {
        var session_id = "<?php echo $_SESSION['session_id']; ?>";
        fetch('app/check_login.php').then(function(response){
          return response.json();
        }).then(function(responseData){
        if(responseData.output == 'logout'){
          window.location.href = '../auth/logout.php';
          }
        });
      }
      setInterval(function(){
        check_sesssion_id();
      }, 10000);
    </script>
  </body>
</html>