                         


                      
                              </div>

                           </div>
                        </div>
                     </div>
                       <footer >
                        <div class="pcoded pcoded-footer-bottom">
                           <p >© <? echo date('Y'); ?> GlobalVoiceDirect.com, All Rights Reserved.</p>
                        </div>
                       
                        </footer>
                  </div>

               </div>

               <div id="styleSelector"></div>
            </div>

         </div>

      </div>
      <script type="text/javascript">
         function getsmsDetail(id) {
         $.ajax({
            url: 'actions/getSmsSentDet.php',
            type: 'POST',
            data: {
               id: id
            },
            success: function(data) {
                 var queryData = JSON.parse(data);
         
               console.log(queryData);

               $('#sms_date').text(queryData['created_at']);
               $('#sms_to').val(queryData['sent_to']);
               if(queryData['accepted']){
                  $('#sms_status').addClass('green-text');
                  $('#sms_status').val("Sent");
               }else{
                  $('#sms_status').addClass('red-text');
                  $('#sms_status').val("Not Sent");
               }
               $('#sms_text').val(queryData['sent_sms']);
                
                modal.style.display = "block";
                
            },
            error: function() {
                alert("Something Went Wrong...Try Again.");
            },
            
         });
         
      }
      </script>
      <!-- <script src="files/assets/js/script.js"></script> -->
      <script type="text/javascript" src="files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
      <script type="text/javascript" src="files/bower_components/popper.js/js/popper.min.js"></script>
      <script type="text/javascript" src="files/bower_components/bootstrap/js/bootstrap.min.js"></script>
      <script src="files/assets/pages/waves/js/waves.min.js"></script>
      <script type="text/javascript" src="files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
      <script src="files/assets/pages/widget/amchart/amcharts.js"></script>
      <script src="files/assets/pages/widget/amchart/serial.js"></script>
      <script src="files/assets/pages/widget/amchart/light.js"></script>
      <script src="files/bower_components/chartist/js/chartist.js"></script>
      <script src="files/assets/js/pcoded.min.js"></script>
      <script src="files/assets/js/vertical/vertical-layout.min.js"></script>
      <script src="files/bower_components/jquery.steps/js/jquery.steps.js"></script>
      <script type="text/javascript" src="files/assets/pages/dashboard/custom-dashboard.min.js"></script>
      <script type="text/javascript" src="files/assets/js/script.min.js"></script>
   </body>
</html>