<?php
header("Access-Control-Allow-Origin: *");
?>
<!DOCTYPE html>
<html>
  <head>
    <link href='//cdn.jsdelivr.net/npm/yasgui@2.7.19/dist/yasgui.min.css' rel='stylesheet' type='text/css'/>
    <style>
      /** uncomment this if you'd like to hide the endpoint selector
      .yasgui .endpointText {display:none !important;}
      **/
    </style>
  </head>
  <body>
	  <div id='yasgui'></div>
    <script src='//cdn.jsdelivr.net/npm/yasgui@2.7.19/dist/yasgui.min.js'></script>
    <script type="text/javascript">
	    var yasgui = YASGUI(document.getElementById("yasgui"), {
        //Uncomment below to change the default endpoint
        //Note: If you've already opened the YASGUI page before, you should first clear your
        //local-storage cache before you will see the changes taking effect 
        //yasqe:{sparql:{endpoint:'bla'}}
      });
    </script>
  </body>
</html>
