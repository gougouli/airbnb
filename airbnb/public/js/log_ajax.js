$(function(){
     $('.state_message').removeClass('popup').removeClass('no').removeClass('yes').removeClass('message');
     $('#register .modal-wrapper').removeClass('error-anim');
     $(".form-register").submit(function(event){
          event.preventDefault();
          registerForm();
     });
     $(".form-login").submit(function(event){
          event.preventDefault();
          loginForm();
     });
     $(".form-forgot-pass").submit(function(event){
          event.preventDefault();
          forgotPassForm();
     });
     $(".form-new-pass").submit(function(event){
         event.preventDefault();
         modifyPass();
     });
     $(".form-help").submit(function(event){
          event.preventDefault();
          helpForm();
     });
     $(".form-create-acco").submit(function(event){
          event.preventDefault();
          accoCreateForm();
     });

}); // HELP EVENT



function registerForm(){

     // Initiate letiables With Form Content
     let lname = $("#lname").val();
     let fname = $("#fname").val();
     let email = $("#email").val();
     let pass = $("#pass").val();
     let repass = $("#repass").val();
     let birth = $("#birth").val();
     $.post("model/register.php",{lname: lname, fname: fname, email:email, pass:pass, repass: repass, birth: birth}, function(data){
          $('.form-register .state_message').addClass('message');
          $('.form-register .state_message').removeClass('yes');
          $('.form-register .state_message').removeClass('no');
          if(data!="ok"){

               $('.form-register .state_message').addClass('no');
               $('.form-register .state_message').empty().append("Oups, " + data);
               $('#register .modal-wrapper').addClass('error-anim');

          }else{
               $("#lname").val('');$("#email").val('');
               $("#pass").val('');$("#fname").val('');
               $("#repass").val('');$("#birth").val('');
               $('.form-register .state_message').addClass('yes');
               $('.form-register .state_message').empty().append('Un mail de confirmation vous à été envoyé.');
          }
     });
}

function loginForm(){
     $('.loader_form').hide();
     $('#login .modal-wrapper').removeClass('error-anim');

     // Initiate letiables With Form Content
     let email = $("#email2").val();
     let pass = $("#pass2").val();
     let keep_pass = document.getElementById('keep_pass').checked;
     $.post("model/login.php",{email: email, pass:pass, keep_pass: keep_pass}, function(data){
          $('.form-login .state_message').addClass('message');
          if(data !="ok" && data != "reserve" && data != "host" && data !="account"){
               $('.form-login .state_message').addClass('no');
               $('.form-login .state_message').empty().append("Oups, " + data);
               $('#login .modal-wrapper').addClass('error-anim');
          }else{
               $("#pass2").val('');$("#email2").val('');
               $('.form-login .state_message').addClass('yes');
               $('.form-login .state_message').empty().append('Connexion effectuée, redirection en cours ...');
               $('.loader_form').show();
               //if(data =="host" || data == "reserve"){
               //    setTimeout(function(){window.location="/"+ data;},2000);
               //}else{
                   setTimeout(function(){window.location="/account";},2000);

               //}
          }
     });
}

function forgotPassForm(){
     $('#forgot-pass .modal-wrapper').removeClass('error-anim');

     // Initiate letiables With Form Content
     let email = $("#email3").val();
     $.post("model/sendmail_pass.php",{email: email}, function(data){
          $('.form-forgot-pass .state_message').addClass('message');
          if(data!="ok"){
               $('.form-forgot-pass .state_message').addClass('no');
               $('.form-forgot-pass .state_message').empty().append("Oups, " + data);
               $('#forgot-pass .modal-wrapper').addClass('error-anim');
          }else{
               $("#email3").val('');
               $('.form-forgot-pass .state_message').addClass('yes');
               $('.form-forgot-pass .state_message').empty().append('Un mail de rectification de votre mot de passe vous à été envoyé.');
          }
     });
}

function modifyPass(){
     $('#new-pass .modal-wrapper').removeClass('error-anim');

     // Initiate letiables With Form Content
     let token = $("#token").val();
     let pass = $("#pass3").val();
     let repass = $("#repass3").val();
     $.post("/model/modify_pass.php",{token: token, pass:pass, repass: repass}, function(data){
          $('.form-new-pass .state_message').addClass('message');
          if(data!="ok"){
               $('.form-new-pass .state_message').addClass('no');
               $('.form-new-pass .state_message').empty().append("Oups, " + data);
               $('#new-pass .modal-wrapper').addClass('error-anim');
          }else{
               $("#pass3").val('');$("#token").val('');$("#repass3").val('');

               $('.form-new-pass .state_message').addClass('yes');
               $('.form-new-pass .state_message').empty().append('Votre mot de passe a bien été modifié. Redirection sur le menu de connexion ...');
               setTimeout(function(){window.location="/login";},3000);
          }
     });
}

function helpForm(){
     $('#help .modal-wrapper').removeClass('error-anim');
     // Initiate letiables With Form Content
     let email = $("#email4").val();
     let object = $("#object").val();
     let message = $("#message").val();
     $.post("model/help.php",{email: email, object: object, message:message, captcha: grecaptcha.getResponse()}, function(data){
          $('.form-help .state_message').addClass('message');
          if(data!="ok"){
               $('.form-help .state_message').addClass('no');
               $('.form-help .state_message').empty().append("Oups, " + data);
               $('#help .modal-wrapper').addClass('error-anim');
          }else{
               $("#object").val('');$("#email4").val('');$("#message").val('');
               $('.form-help .state_message').addClass('yes');
               $('.form-help .state_message').empty().append('Votre mail vient d\'être envoyer au support. (Attente de réponse: 24h à 48h)');
               setTimeout(function(){window.location="/";},5000);
          }
     })
}

function accoCreateForm(){
     // Initiate letiables With Form Content
     let id_seller = $("#id_seller").val() ;
     let title = $("#title").val();
     let size = $("#size").val();
     let single_bed = $("#single_bed").val();

     let double_bed = $("#double_bed").val();
     let time_arrive = $("#time_arrive").val();
     let time_go = $("#time_go").val();
     let description = $("#description").val();
     let animal = document.getElementById('animal').checked;
     let handicap = document.getElementById('handicap').checked;
     let breakfast = document.getElementById('breakfast').checked;
     let dinner = document.getElementById('dinner').checked;
     
     let country = $("#country").val();
     let city = $("#city").val();
     let zip = $("#zip").val();
     let address = $("#address").val();
     let sub_address = $("#sub_address").val();
     let price = $("#price").val();
     let other = $("#other").val();
     let okey = document.getElementById('okey').checked;
     let password = $("#password").val()

     $.post("model/create_acco.php",{
          id_seller:id_seller, title:title, size:size, single_bed:single_bed,
          double_bed:double_bed, time_arrive:time_arrive, time_go:time_go, description:description,
          animal:animal, handicap:handicap, breakfast:breakfast,dinner:dinner, country:country, city:city, zip:zip, address:address,
          sub_address:sub_address ,price:price, other:other, okey:okey, password:password
     }, function(data){
          $('.form-create-acco .state_message').addClass('popup');

          if(data!="ok"){
               $('.form-create-acco .state_message').addClass('no');
               $('.form-create-acco .state_message').empty().append("Oups, " + data);
               setTimeout(function(){$('.form-create-acco .state_message').empty().removeClass('popup').removeClass('no');},3000);
          }else{
               $("#password").val("");$("#okey").val("");
               $('.form-create-acco .state_message').addClass('yes');

               //$('.form-create-acco .state_message').empty().append('Votre offre de séjour est désormais en ligne.');
               $('.form-create-acco .state_message').empty().append(data);
               //setTimeout(function(){window.location="/account";},3000);

          }
     });
}
